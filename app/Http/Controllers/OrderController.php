<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->with('user')
            ->latest()
            ->get();

        return view('screens.admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.images', 'addresses']);

        return view('screens.admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        if ($order->order_status === 'completed') {
            return response()->json([
                'message' => __('Completed orders cannot be changed.'),
            ], 422);
        }

        $validated = $request->validate([
            'order_status' => ['required', 'string', Rule::in([
                'pending',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
            ])],
        ]);

        $order->update([
            'order_status' => $validated['order_status'],
        ]);

        return response()->json([
            'message' => __('Order status updated successfully.'),
            'status' => $order->order_status,
        ]);
    }
}
