<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PrintfulFulfillmentService;
use App\Services\PrintfulService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderPrintfulController extends Controller
{
    public function createDraft(Order $order, PrintfulFulfillmentService $fulfillment): RedirectResponse
    {
        if (strcasecmp((string) $order->payment_status, 'paid') !== 0) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', __('Only paid orders can be submitted to Printful.'));
        }

        if ($order->printful_order_id !== null) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', __('This order already has a Printful order. Duplicate submission is not allowed.'));
        }

        $result = $fulfillment->submitPaidOrder($order);

        if ($result['success'] && ! $result['skipped']) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', __('Draft created in Printful.'));
        }

        if ($result['skipped']) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', __('This order already has a Printful order. Duplicate submission is not allowed.'));
        }

        $detail = $result['error'] ?? $result['message'];

        return redirect()
            ->route('orders.show', $order)
            ->with('error', __('Printful order failed.').' '.$detail);
    }

    public function confirm(Request $request, Order $order, PrintfulFulfillmentService $fulfillment): RedirectResponse
    {
        $result = $fulfillment->confirmDraftOrder($order, automatic: false);

        if ($result['success']) {
            return redirect()
                ->route('orders.show', $order->fresh())
                ->with('success', __('Printful order confirmed.'));
        }

        $detail = $result['error'] ?? $result['message'];

        return redirect()
            ->route('orders.show', $order)
            ->with('error', __('Printful order failed.').' '.$detail);
    }

    public function status(Request $request, Order $order, PrintfulService $printful): JsonResponse|RedirectResponse
    {
        if ($order->printful_order_id === null) {
            return $this->respond($request, false, __('No Printful order exists for this local order yet.'));
        }

        $response = $printful->getOrder($order->printful_order_id);

        if (! $response['success']) {
            $this->persistStatusSyncError($order, $response);

            Log::warning('Printful order status fetch failed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'printful_order_id' => $order->printful_order_id,
                'http_status' => $response['status'],
                'error' => $response['error'],
            ]);

            return $this->respond(
                $request,
                false,
                __('Printful order failed.').' '.($response['error'] ?? ''),
                statusCode: 422,
            );
        }

        $printfulData = is_array($response['data']) ? $response['data'] : [];
        $printfulStatus = strtolower((string) ($printfulData['status'] ?? $order->printful_status ?? ''));

        DB::transaction(function () use ($order, $response, $printfulData, $printfulStatus) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingRawData = is_array($lockedOrder->raw_data) ? $lockedOrder->raw_data : [];
            $printfulRaw = is_array($existingRawData['printful'] ?? null) ? $existingRawData['printful'] : [];

            $updates = [
                'printful_status' => $printfulStatus,
                'raw_data' => array_merge($existingRawData, [
                    'printful' => array_merge($printfulRaw, [
                        'last_status_sync_at' => now()->toIso8601String(),
                        'status' => [
                            'http_status' => $response['status'],
                            'order' => $printfulData,
                        ],
                    ]),
                ]),
            ];

            $mappedStatus = $this->resolveLocalStatusFromPrintfulSync($printfulStatus);

            if ($mappedStatus !== null && $lockedOrder->status !== 'completed' && $lockedOrder->status !== 'cancelled') {
                $updates['status'] = $mappedStatus;
            }

            $lockedOrder->update($updates);
        });

        $order->refresh();

        return $this->respond(
            $request,
            true,
            __('Printful status updated.'),
            [
                'printful_order_id' => $order->printful_order_id,
                'printful_status' => $order->printful_status,
                'local_status' => $order->status,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function persistStatusSyncError(Order $order, array $response): void
    {
        $existingRawData = is_array($order->raw_data) ? $order->raw_data : [];
        $printfulRaw = is_array($existingRawData['printful'] ?? null) ? $existingRawData['printful'] : [];

        $order->update([
            'raw_data' => array_merge($existingRawData, [
                'printful' => array_merge($printfulRaw, [
                    'status_sync_failed_at' => now()->toIso8601String(),
                    'status_sync_error' => [
                        'http_status' => $response['status'] ?? null,
                        'error' => $response['error'] ?? null,
                    ],
                ]),
            ]),
        ]);
    }

    private function resolveLocalStatusFromPrintfulSync(string $printfulStatus): ?string
    {
        return match (strtolower($printfulStatus)) {
            'draft', 'failed' => 'printful_draft',
            'canceled', 'cancelled' => 'cancelled',
            'fulfilled', 'shipped' => 'shipped',
            'delivered', 'archived' => 'delivered',
            'pending', 'inprocess', 'partial', 'onhold' => 'processing',
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    private function respond(
        Request $request,
        bool $success,
        string $message,
        ?array $data = null,
        int $statusCode = 200,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(array_filter([
                'success' => $success,
                'message' => $message,
                'data' => $data,
            ]), $success ? $statusCode : ($statusCode >= 400 ? $statusCode : 422));
        }

        return redirect()
            ->route('orders.show', $request->route('order'))
            ->with($success ? 'success' : 'error', $message);
    }
}
