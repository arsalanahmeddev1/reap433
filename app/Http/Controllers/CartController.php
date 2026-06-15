<?php

namespace App\Http\Controllers;

use App\Models\PrintfulVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function index(): View
    {
        return view('cart.index', [
            'items' => $this->cart->all(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(Request $request, PrintfulVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $variant->loadMissing('product');

        $this->cart->add($variant, (int) $validated['quantity']);

        return redirect()
            ->route('cart.index')
            ->with('success', __('Product added to cart.'));
    }

    public function update(Request $request, int $variantId): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if (! $this->cart->update($variantId, (int) $validated['quantity'])) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('Item not found in cart.'));
        }

        return redirect()
            ->route('cart.index')
            ->with('success', __('Cart updated.'));
    }

    public function remove(int $variantId): RedirectResponse
    {
        if (! $this->cart->has($variantId)) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('Item not found in cart.'));
        }

        $this->cart->remove($variantId);

        return redirect()
            ->route('cart.index')
            ->with('success', __('Item removed from cart.'));
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()
            ->route('cart.index')
            ->with('success', __('Cart cleared.'));
    }
}
