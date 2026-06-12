<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartItemController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1|max:999',
            'line_price' => 'nullable|numeric|min:0',
            'product_variation_id' => [
                'nullable',
                'integer',
                Rule::exists('product_variations', 'id')->where(fn ($q) => $q->where('product_id', (int) $request->input('product_id'))),
            ],
        ]);

        $qty = max(1, min(999, (int) ($data['qty'] ?? 1)));

        $product = Product::query()->with('productType')->findOrFail($data['product_id']);

        if ($product->isVariable() && empty($data['product_variation_id']) && ! isset($data['line_price'])) {
            return response()->json([
                'success' => false,
                'message' => __('Please select product options before adding to cart.'),
            ], 422);
        }

        $variation = null;
        if (! empty($data['product_variation_id'])) {
            $variation = ProductVariation::query()
                ->where('id', (int) $data['product_variation_id'])
                ->where('product_id', $product->id)
                ->first();
        }

        $unitPrice = isset($data['line_price'])
            ? (float) $data['line_price']
            : $this->resolveLineUnitPrice($product, $variation);

        if (auth()->check()) {
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        } else {
            $cart = Cart::firstOrCreate(['session_id' => session()->getId()]);
        }

        $itemQuery = $cart->items()->where('product_id', $product->id);
        if ($variation) {
            $itemQuery->where('product_variation_id', $variation->id);
        } else {
            $itemQuery->whereNull('product_variation_id');
        }

        $item = $itemQuery->first();

        if ($item) {
            $item->increment('qty', $qty);
            $item->update(['price' => $unitPrice]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variation_id' => $variation?->id,
                'qty' => $qty,
                'price' => $unitPrice,
            ]);
        }

        $count = (int) $cart->items()->sum('qty');

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => __('Product added to cart'),
        ]);
    }

    public function updateQty(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $qty = max(1, min(999, (int) $request->input('qty', 1)));
        $cartItem->update(['qty' => $qty]);

        $cart = $cartItem->cart()->with('items')->first();

        return response()->json([
            'success' => true,
            'qty' => $cartItem->qty,
            'itemSubtotal' => $cartItem->subtotal,
            'cartSubtotal' => $cart->total(),
            'cartTotal' => $cart->total(),
            'cartCount' => (int) $cart->items->sum('qty'),
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart?->load('items');

        return response()->json([
            'success' => true,
            'cartSubtotal' => $cart ? $cart->total() : 0,
            'cartTotal' => $cart ? $cart->total() : 0,
            'cartCount' => $cart ? (int) $cart->items->sum('qty') : 0,
        ]);
    }

    private function authorizeCartItem(CartItem $cartItem): void
    {
        $cart = $cartItem->cart;
        if (! $cart) {
            abort(404);
        }

        if (auth()->check()) {
            if ((int) $cart->user_id !== (int) auth()->id()) {
                abort(403);
            }

            return;
        }

        if ($cart->session_id !== session()->getId()) {
            abort(403);
        }
    }

    private function resolveLineUnitPrice(Product $product, ?ProductVariation $variation): float
    {
        if ($variation) {
            $p = (float) $variation->price;
            if ($p > 0) {
                return $p;
            }
        }

        $base = (float) $product->price;
        if ($base > 0) {
            return $base;
        }

        if ($product->isVariable()) {
            $from = (float) ($product->from_price ?? 0);
            if ($from > 0) {
                return $from;
            }
            $minVar = (float) ($product->variations()->min('price') ?? 0);
            if ($minVar > 0) {
                return $minVar;
            }
        }

        return max(0.0, $base);
    }
}
