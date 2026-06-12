<?php

namespace App\Http\Controllers;

use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::current();
        $cart->load(['items.product.images', 'items.product.category', 'items.variation']);

        return view('screens.web.cart.index', compact('cart'));
    }
}
