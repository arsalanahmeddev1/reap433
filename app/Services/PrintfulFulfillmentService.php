<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintfulVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PrintfulFulfillmentService
{
    public function __construct(
        private readonly PrintfulService $printful,
    ) {}

    /**
     * Submit a paid local order to Printful as a draft (not confirmed for fulfillment).
     *
     * @return array{
     *     success: bool,
     *     skipped: bool,
     *     message: string,
     *     order_id: int|null,
     *     order_number: string|null,
     *     printful_order_id: int|null,
     *     printful_status: string|null,
     *     error: string|null,
     *     validation_errors: list<string>|null
     * }
     */
    public function submitPaidOrder(Order $order): array
    {
        $order->loadMissing('items');

        Log::info('Printful fulfillment submission started.', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'item_count' => $order->items->count(),
            'payment_status' => $order->payment_status,
            'existing_printful_order_id' => $order->printful_order_id,
        ]);

        if ($order->printful_order_id !== null) {
            Log::info('Printful fulfillment submission skipped (already submitted).', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'printful_order_id' => $order->printful_order_id,
            ]);

            return $this->result(
                success: true,
                skipped: true,
                message: 'Order already has a Printful order ID.',
                order: $order,
            );
        }

        $validationErrors = $this->validateOrder($order);

        if ($validationErrors !== []) {
            Log::warning('Printful fulfillment validation failed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'validation_errors' => $validationErrors,
            ]);

            return $this->result(
                success: false,
                skipped: false,
                message: 'Order failed validation for Printful submission.',
                order: $order,
                error: $validationErrors[0],
                validationErrors: $validationErrors,
            );
        }

        $payload = $this->buildDraftOrderPayload($order);

        try {
            $draftResult = DB::transaction(function () use ($order, $payload) {
                /** @var Order $lockedOrder */
                $lockedOrder = Order::query()
                    ->whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedOrder->loadMissing('items');

                if ($lockedOrder->printful_order_id !== null) {
                    Log::info('Printful fulfillment submission skipped after lock (already submitted).', [
                        'order_id' => $lockedOrder->id,
                        'order_number' => $lockedOrder->order_number,
                        'printful_order_id' => $lockedOrder->printful_order_id,
                    ]);

                    return $this->result(
                        success: true,
                        skipped: true,
                        message: 'Order already has a Printful order ID.',
                        order: $lockedOrder,
                    );
                }

                $response = $this->printful->createDraftOrder($payload);

                if (! $response['success']) {
                    Log::error('Printful fulfillment draft creation failed.', [
                        'order_id' => $lockedOrder->id,
                        'order_number' => $lockedOrder->order_number,
                        'http_status' => $response['status'],
                        'error' => $response['error'],
                        'item_count' => $lockedOrder->items->count(),
                    ]);

                    return $this->result(
                        success: false,
                        skipped: false,
                        message: 'Printful draft order creation failed.',
                        order: $lockedOrder,
                        error: $response['error'] ?? 'Printful draft order creation failed.',
                    );
                }

                $printfulData = is_array($response['data']) ? $response['data'] : [];
                $printfulOrderId = isset($printfulData['id']) && is_numeric($printfulData['id'])
                    ? (int) $printfulData['id']
                    : null;

                if ($printfulOrderId === null) {
                    Log::error('Printful fulfillment draft response missing order ID.', [
                        'order_id' => $lockedOrder->id,
                        'order_number' => $lockedOrder->order_number,
                        'http_status' => $response['status'],
                    ]);

                    return $this->result(
                        success: false,
                        skipped: false,
                        message: 'Printful draft order response did not include an order ID.',
                        order: $lockedOrder,
                        error: 'Printful draft order response did not include an order ID.',
                    );
                }

                $printfulStatus = $this->stringOrNull($printfulData['status'] ?? null) ?? 'draft';

                $existingRawData = is_array($lockedOrder->raw_data) ? $lockedOrder->raw_data : [];

                $lockedOrder->update([
                    'printful_order_id' => $printfulOrderId,
                    'printful_status' => $printfulStatus,
                    'status' => 'printful_draft',
                    'raw_data' => array_merge($existingRawData, [
                        'printful' => [
                            'submitted_at' => now()->toIso8601String(),
                            'draft' => [
                                'http_status' => $response['status'],
                                'order' => $printfulData,
                            ],
                        ],
                    ]),
                ]);

                $lockedOrder = $lockedOrder->fresh(['items']);

                Log::info('Printful fulfillment draft created.', [
                    'order_id' => $lockedOrder->id,
                    'order_number' => $lockedOrder->order_number,
                    'printful_order_id' => $printfulOrderId,
                    'printful_status' => $printfulStatus,
                    'local_status' => $lockedOrder->status,
                ]);

                return $this->result(
                    success: true,
                    skipped: false,
                    message: 'Printful draft order created successfully.',
                    order: $lockedOrder,
                    printfulStatus: $printfulStatus,
                );
            });

            if (
                $draftResult['success']
                && ! $draftResult['skipped']
                && $this->printful->isAutoConfirmEnabled()
            ) {
                Log::warning('Automatic Printful confirmation enabled via PRINTFUL_AUTO_CONFIRM.', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'printful_order_id' => $draftResult['printful_order_id'],
                ]);

                $freshOrder = Order::query()->with('items')->findOrFail($order->id);
                $confirmResult = $this->confirmDraftOrder($freshOrder, automatic: true);

                if ($confirmResult['success']) {
                    $confirmResult['message'] = 'Printful draft created and automatically confirmed.';
                } else {
                    $confirmResult['message'] = 'Printful draft created but automatic confirmation failed.';
                }

                return $confirmResult;
            }

            return $draftResult;
        } catch (Throwable $exception) {
            Log::error('Printful fulfillment submission exception.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => $exception->getMessage(),
            ]);

            return $this->result(
                success: false,
                skipped: false,
                message: 'Printful fulfillment submission failed unexpectedly.',
                order: $order,
                error: 'Printful fulfillment submission failed unexpectedly.',
            );
        }
    }

    /**
     * Confirm an existing Printful draft order.
     *
     * @return array{
     *     success: bool,
     *     skipped: bool,
     *     message: string,
     *     order_id: int|null,
     *     order_number: string|null,
     *     printful_order_id: int|null,
     *     printful_status: string|null,
     *     error: string|null,
     *     validation_errors: list<string>|null
     * }
     */
    public function confirmDraftOrder(Order $order, bool $automatic = false): array
    {
        $order->loadMissing('items');

        if ($order->printful_order_id === null) {
            return $this->result(
                success: false,
                skipped: false,
                message: 'No Printful order exists for this local order.',
                order: $order,
                error: 'No Printful order exists for this local order.',
            );
        }

        $currentPrintfulStatus = strtolower((string) ($order->printful_status ?? ''));

        if (! in_array($currentPrintfulStatus, ['draft', 'failed'], true)) {
            return $this->result(
                success: false,
                skipped: false,
                message: 'Printful order cannot be confirmed in its current status.',
                order: $order,
                error: sprintf(
                    'Printful order cannot be confirmed while status is %s.',
                    $order->printful_status ?? 'unknown',
                ),
            );
        }

        try {
            $response = $this->printful->confirmOrder($order->printful_order_id, $automatic);

            if (! $response['success']) {
                $this->markPrintfulConfirmFailed($order, $response);

                Log::warning('Printful order confirmation failed.', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'printful_order_id' => $order->printful_order_id,
                    'automatic' => $automatic,
                    'http_status' => $response['status'],
                    'error' => $response['error'],
                ]);

                return $this->result(
                    success: false,
                    skipped: false,
                    message: 'Printful order confirmation failed.',
                    order: $order->fresh(),
                    error: $response['error'] ?? 'Printful order confirmation failed.',
                );
            }

            $printfulData = is_array($response['data']) ? $response['data'] : [];
            $printfulStatus = strtolower((string) ($printfulData['status'] ?? 'pending'));

            DB::transaction(function () use ($order, $response, $printfulData, $printfulStatus) {
                $lockedOrder = Order::query()
                    ->whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $existingRawData = is_array($lockedOrder->raw_data) ? $lockedOrder->raw_data : [];
                $printfulRaw = is_array($existingRawData['printful'] ?? null) ? $existingRawData['printful'] : [];

                $lockedOrder->update([
                    'printful_status' => $printfulStatus,
                    'status' => $this->resolveLocalStatusAfterConfirm($printfulStatus),
                    'raw_data' => array_merge($existingRawData, [
                        'printful' => array_merge($printfulRaw, [
                            'confirmed_at' => now()->toIso8601String(),
                            'confirm' => [
                                'http_status' => $response['status'],
                                'order' => $printfulData,
                            ],
                        ]),
                    ]),
                ]);
            });

            $order->refresh();

            Log::info('Printful order confirmed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'printful_order_id' => $order->printful_order_id,
                'automatic' => $automatic,
                'printful_status' => $printfulStatus,
            ]);

            return $this->result(
                success: true,
                skipped: false,
                message: 'Printful order confirmed successfully.',
                order: $order,
                printfulStatus: $printfulStatus,
            );
        } catch (Throwable $exception) {
            Log::error('Printful order confirmation exception.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'printful_order_id' => $order->printful_order_id,
                'automatic' => $automatic,
                'message' => $exception->getMessage(),
            ]);

            return $this->result(
                success: false,
                skipped: false,
                message: 'Printful order confirmation failed unexpectedly.',
                order: $order,
                error: 'Printful order confirmation failed unexpectedly.',
            );
        }
    }

    /**
     * @return list<string>
     */
    private function validateOrder(Order $order): array
    {
        $errors = [];

        if (strcasecmp((string) $order->payment_status, 'paid') !== 0) {
            $errors[] = 'Order payment_status must be paid.';
        }

        if ($order->items->isEmpty()) {
            $errors[] = 'Order must contain at least one item.';
        }

        if ($order->printful_order_id !== null) {
            $errors[] = 'Order already has a Printful order ID.';
        }

        foreach ($this->requiredRecipientFields() as $field => $label) {
            if (! filled($order->{$field})) {
                $errors[] = "Missing required shipping field: {$label}.";
            }
        }

        foreach ($order->items as $item) {
            $syncVariantId = $this->resolveSyncVariantId($item);

            if ($syncVariantId === null) {
                $errors[] = sprintf(
                    'Order item #%d is missing a valid Printful sync variant ID.',
                    $item->id,
                );
            }
        }

        return $errors;
    }

    /**
     * @return array<string, string>
     */
    private function requiredRecipientFields(): array
    {
        return [
            'customer_name' => 'customer_name',
            'customer_email' => 'customer_email',
            'address1' => 'address1',
            'city' => 'city',
            'state_code' => 'state_code',
            'country_code' => 'country_code',
            'zip' => 'zip',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDraftOrderPayload(Order $order): array
    {
        $currency = strtoupper((string) ($order->currency ?: 'USD'));
        $subtotal = $this->formatMoney($order->subtotal);

        $items = $order->items->map(function (OrderItem $item) {
            $syncVariantId = $this->resolveSyncVariantId($item);

            return [
                'sync_variant_id' => $syncVariantId,
                'quantity' => (int) $item->quantity,
                'retail_price' => $this->formatMoney($item->price),
            ];
        })->values()->all();

        return [
            'external_id' => (string) $order->order_number,
            'recipient' => array_filter([
                'name' => (string) $order->customer_name,
                'email' => (string) $order->customer_email,
                'phone' => filled($order->customer_phone) ? (string) $order->customer_phone : null,
                'address1' => (string) $order->address1,
                'address2' => filled($order->address2) ? (string) $order->address2 : null,
                'city' => (string) $order->city,
                'state_code' => (string) $order->state_code,
                'country_code' => strtoupper((string) $order->country_code),
                'zip' => (string) $order->zip,
            ], static fn ($value) => $value !== null && $value !== ''),
            'items' => $items,
            'retail_costs' => [
                'currency' => $currency,
                'subtotal' => $subtotal,
                'shipping' => '0.00',
                'tax' => '0.00',
                'total' => $subtotal,
            ],
        ];
    }

    private function resolveSyncVariantId(OrderItem $item): ?int
    {
        $raw = is_array($item->raw_data) ? $item->raw_data : [];

        if (isset($raw['printful_variant_id']) && is_numeric($raw['printful_variant_id'])) {
            $syncVariantId = (int) $raw['printful_variant_id'];

            if ($this->isKnownSyncVariantId($syncVariantId)) {
                return $syncVariantId;
            }
        }

        if ($item->printful_variant_id !== null) {
            $syncVariantId = PrintfulVariant::query()
                ->where('printful_variant_id', $item->printful_variant_id)
                ->value('printful_variant_id');

            if ($syncVariantId !== null) {
                return (int) $syncVariantId;
            }

            $localVariant = PrintfulVariant::query()->find($item->printful_variant_id);

            if ($localVariant?->printful_variant_id !== null) {
                return (int) $localVariant->printful_variant_id;
            }
        }

        if (isset($raw['variant_id']) && is_numeric($raw['variant_id'])) {
            $localVariant = PrintfulVariant::query()->find((int) $raw['variant_id']);

            if ($localVariant?->printful_variant_id !== null) {
                return (int) $localVariant->printful_variant_id;
            }
        }

        return null;
    }

    private function isKnownSyncVariantId(int $syncVariantId): bool
    {
        return PrintfulVariant::query()
            ->where('printful_variant_id', $syncVariantId)
            ->exists();
    }

    private function formatMoney(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function markPrintfulConfirmFailed(Order $order, array $response): void
    {
        $existingRawData = is_array($order->raw_data) ? $order->raw_data : [];
        $printfulRaw = is_array($existingRawData['printful'] ?? null) ? $existingRawData['printful'] : [];

        $order->update([
            'printful_status' => 'failed',
            'raw_data' => array_merge($existingRawData, [
                'printful' => array_merge($printfulRaw, [
                    'confirm_failed_at' => now()->toIso8601String(),
                    'confirm_error' => [
                        'http_status' => $response['status'] ?? null,
                        'error' => $response['error'] ?? null,
                    ],
                ]),
            ]),
        ]);
    }

    private function resolveLocalStatusAfterConfirm(string $printfulStatus): string
    {
        return match (strtolower($printfulStatus)) {
            'failed' => 'printful_draft',
            'canceled', 'cancelled' => 'cancelled',
            default => 'processing',
        };
    }

    /**
     * @param  list<string>|null  $validationErrors
     * @return array{
     *     success: bool,
     *     skipped: bool,
     *     message: string,
     *     order_id: int|null,
     *     order_number: string|null,
     *     printful_order_id: int|null,
     *     printful_status: string|null,
     *     error: string|null,
     *     validation_errors: list<string>|null
     * }
     */
    private function result(
        bool $success,
        bool $skipped,
        string $message,
        Order $order,
        ?string $error = null,
        ?string $printfulStatus = null,
        ?array $validationErrors = null,
    ): array {
        return [
            'success' => $success,
            'skipped' => $skipped,
            'message' => $message,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'printful_order_id' => $order->printful_order_id,
            'printful_status' => $printfulStatus ?? $order->printful_status,
            'error' => $error,
            'validation_errors' => $validationErrors,
        ];
    }
}
