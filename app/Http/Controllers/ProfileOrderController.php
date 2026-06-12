<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->get();

        return view('profile.orders.index', [
            'user' => $request->user(),
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $order->load(['items.product.images', 'addresses']);

        return view('profile.orders.show', [
            'user' => $request->user(),
            'order' => $order,
        ]);
    }
}
