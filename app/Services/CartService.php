<?php

namespace App\Services;

use App\Models\PrintfulVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * @return array<int, array{
     *     product_id: int,
     *     product_name: string,
     *     product_thumbnail_url: string|null,
     *     variant_id: int,
     *     printful_variant_id: int|null,
     *     variant_name: string|null,
     *     sku: string|null,
     *     price: float,
     *     currency: string|null,
     *     quantity: int,
     *     variant_thumbnail_url: string|null
     * }>
     */
    public function all(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function add(PrintfulVariant $variant, int $quantity = 1): void
    {
        $variant->loadMissing('product');

        $cart = $this->all();
        $key = $variant->id;
        $quantity = max(1, $quantity);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = $this->formatItem($variant, $quantity);
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function update(int $variantId, int $quantity): bool
    {
        $cart = $this->all();

        if (! isset($cart[$variantId])) {
            return false;
        }

        $cart[$variantId]['quantity'] = max(1, $quantity);

        Session::put(self::SESSION_KEY, $cart);

        return true;
    }

    public function has(int $variantId): bool
    {
        return isset($this->all()[$variantId]);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->all();

        unset($cart[$variantId]);

        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    public function subtotal(): float
    {
        $total = 0.0;

        foreach ($this->all() as $item) {
            $total += ((float) $item['price']) * ((int) $item['quantity']);
        }

        return round($total, 2);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function subtotalFor(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $total += ((float) $item['price']) * ((int) $item['quantity']);
        }

        return round($total, 2);
    }

    /**
     * @return array{
     *     product_id: int,
     *     product_name: string,
     *     product_thumbnail_url: string|null,
     *     variant_id: int,
     *     printful_variant_id: int|null,
     *     variant_name: string|null,
     *     sku: string|null,
     *     price: float,
     *     currency: string|null,
     *     quantity: int,
     *     variant_thumbnail_url: string|null
     * }
     */
    private function formatItem(PrintfulVariant $variant, int $quantity): array
    {
        $product = $variant->product;

        return [
            'product_id' => (int) $variant->printful_product_id,
            'product_name' => $product?->name ?? 'Untitled product',
            'product_thumbnail_url' => $product?->thumbnail_url,
            'variant_id' => (int) $variant->id,
            'printful_variant_id' => $variant->printful_variant_id !== null
                ? (int) $variant->printful_variant_id
                : null,
            'variant_name' => $variant->name,
            'sku' => $variant->sku,
            'price' => $variant->retail_price !== null ? (float) $variant->retail_price : 0.0,
            'currency' => $variant->currency,
            'quantity' => max(1, $quantity),
            'variant_thumbnail_url' => $variant->thumbnail_url,
        ];
    }
}
