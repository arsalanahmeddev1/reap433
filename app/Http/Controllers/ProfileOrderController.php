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
        if (strcasecmp((string) $order->customer_email, (string) $request->user()->email) !== 0) {
            abort(403);
        }

        $order->load('items');

        return view('profile.orders.show', [
            'user' => $request->user(),
            'order' => $order,
        ]);
    }
}
