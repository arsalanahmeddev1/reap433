<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderEmailService $orderEmails,
    ) {}

    public function index()
    {
        $orders = Order::query()
            ->latest()
            ->get();

        return view('screens.admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('screens.admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        if ($order->status === 'completed') {
            return response()->json([
                'message' => __('Completed orders cannot be changed.'),
            ], 422);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Order::STATUSES)],
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $validated['status'],
        ]);

        if ($oldStatus !== $order->status) {
            $this->orderEmails->sendOrderStatusChanged($order, $oldStatus);
        }

        return response()->json([
            'message' => __('Order status updated successfully.'),
            'status' => $order->status,
        ]);
    }
}
