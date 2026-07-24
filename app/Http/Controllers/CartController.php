<?php

namespace App\Http\Controllers;

use App\Models\PrintfulVariant;
use App\Models\WholeSellerSetting;
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
        $items = $this->cart->all();
        $wholesaleSummary = $this->cart->wholesaleDiscountSummary($items);

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
            'wholesaleSummary' => $wholesaleSummary,
            'wholesalerMinQty' => wholesaler_min_order_quantity(),
            'isWholesalerCheckout' => WholeSellerSetting::appliesToCurrentUser(),
        ]);
    }

    public function add(Request $request, PrintfulVariant $variant): RedirectResponse
    {
        $minQty = wholesaler_min_order_quantity();

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:'.$minQty, 'max:99'],
        ], [
            'quantity.min' => __('Wholesale orders require at least :min of each product.', ['min' => $minQty]),
        ]);

        $variant->loadMissing('product');

        $this->cart->add($variant, (int) $validated['quantity']);

        return redirect()
            ->route('cart.index')
            ->with('success', __('Product added to cart.'));
    }

    public function update(Request $request, string $variantId): RedirectResponse
    {
        $minQty = wholesaler_min_order_quantity();

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:'.$minQty, 'max:99'],
        ], [
            'quantity.min' => __('Wholesale orders require at least :min of each product.', ['min' => $minQty]),
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

    public function remove(string $variantId): RedirectResponse
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
