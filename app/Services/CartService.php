<?php

namespace App\Services;

use App\Models\PrintfulCartItem;
use App\Models\PrintfulVariant;
use App\Models\ProductCustomization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $cart = Session::get(self::SESSION_KEY, []);

        // Normalize legacy integer keys to strings.
        $normalized = [];
        foreach ($cart as $key => $item) {
            $normalized[(string) $key] = $item;
        }

        return $this->refreshCustomizedImages($normalized);
    }

    /**
     * Re-resolve customization preview URLs so missing files fall back to variant thumbs.
     *
     * @param  array<string, array<string, mixed>>  $cart
     * @return array<string, array<string, mixed>>
     */
    private function refreshCustomizedImages(array $cart): array
    {
        foreach ($cart as $key => $item) {
            $uuid = $item['customization_uuid'] ?? null;
            if (! $uuid) {
                continue;
            }

            $customization = ProductCustomization::query()
                ->with('variant')
                ->where('uuid', $uuid)
                ->first();

            if (! $customization) {
                continue;
            }

            $fallback = $customization->variant?->thumbnail_url
                ?? $item['product_thumbnail_url']
                ?? null;
            $preview = $customization->previewUrl() ?: $fallback;

            $cart[$key]['preview_image'] = $preview;
            $cart[$key]['variant_thumbnail_url'] = $preview;
            $cart[$key]['print_file_url'] = $customization->printFileUrl() ?: $customization->uploadUrl();
        }

        return $cart;
    }

    public function add(PrintfulVariant $variant, int $quantity = 1): void
    {
        $variant->loadMissing('product');

        $cart = $this->all();
        $key = $this->standardKey($variant->id);
        $quantity = max(1, $quantity);

        if (isset($cart[$key]) && empty($cart[$key]['customization_id'])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = $this->formatItem($variant, $quantity);
        }

        Session::put(self::SESSION_KEY, $cart);
        $this->persistAuthenticatedCart();
    }

    public function addCustomized(PrintfulVariant $variant, ProductCustomization $customization, int $quantity = 1): void
    {
        $variant->loadMissing('product');
        $quantity = max(1, $quantity);

        $cart = $this->all();
        $key = $this->customKey($customization->uuid);

        if (isset($cart[$key]) && ($cart[$key]['customization_uuid'] ?? null) === $customization->uuid) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = $this->formatCustomizedItem($variant, $customization, $quantity);
        }

        Session::put(self::SESSION_KEY, $cart);
        $this->persistAuthenticatedCart();
    }

    public function update(int|string $cartKey, int $quantity): bool
    {
        $cart = $this->all();
        $key = (string) $cartKey;

        if (! isset($cart[$key])) {
            return false;
        }

        $cart[$key]['quantity'] = max(1, $quantity);

        Session::put(self::SESSION_KEY, $cart);
        $this->persistAuthenticatedCart();

        return true;
    }

    public function has(int|string $cartKey): bool
    {
        return isset($this->all()[(string) $cartKey]);
    }

    public function remove(int|string $cartKey): void
    {
        $cart = $this->all();

        unset($cart[(string) $cartKey]);

        Session::put(self::SESSION_KEY, $cart);
        $this->persistAuthenticatedCart();
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        $this->persistAuthenticatedCart();
    }

    public function count(): int
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    public function subtotal(): float
    {
        return $this->subtotalFor($this->all());
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     */
    public function subtotalFor(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $total += ((float) $item['price']) * ((int) $item['quantity']);
        }

        return round($total, 2);
    }

    public function persistAuthenticatedCart(): void
    {
        if (! Auth::check()) {
            return;
        }

        $userId = (int) Auth::id();
        $items = $this->all();

        PrintfulCartItem::query()->where('user_id', $userId)->delete();

        foreach ($items as $item) {
            PrintfulCartItem::query()->create([
                'user_id' => $userId,
                'variant_id' => (int) $item['variant_id'],
                'printful_variant_id' => $item['printful_variant_id'] ?? null,
                'printful_product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'] ?? 'Untitled product',
                'variant_name' => $item['variant_name'] ?? null,
                'sku' => $item['sku'] ?? null,
                'price' => (float) ($item['price'] ?? 0),
                'currency' => $item['currency'] ?? 'USD',
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                'thumbnail_url' => $item['preview_image']
                    ?? $item['variant_thumbnail_url']
                    ?? $item['product_thumbnail_url']
                    ?? null,
            ]);
        }
    }

    public function standardKey(int $variantId): string
    {
        return (string) $variantId;
    }

    public function customKey(string $uuid): string
    {
        return 'custom:'.$uuid;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(PrintfulVariant $variant, int $quantity): array
    {
        $product = $variant->product;

        return [
            'cart_key' => $this->standardKey($variant->id),
            'is_customized' => false,
            'customization_id' => null,
            'customization_uuid' => null,
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
            'preview_image' => $variant->thumbnail_url,
            'color' => null,
            'size' => null,
            'placement' => null,
            'print_file_url' => null,
            'custom_text_summary' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatCustomizedItem(
        PrintfulVariant $variant,
        ProductCustomization $customization,
        int $quantity,
    ): array {
        $base = $this->formatItem($variant, $quantity);
        $fee = (float) $customization->customization_fee;

        return array_merge($base, [
            'cart_key' => $this->customKey($customization->uuid),
            'is_customized' => true,
            'customization_id' => $customization->id,
            'customization_uuid' => $customization->uuid,
            'price' => round(((float) $base['price']) + $fee, 2),
            'customization_fee' => $fee,
            'preview_image' => $customization->previewUrl() ?: $base['variant_thumbnail_url'],
            'variant_thumbnail_url' => $customization->previewUrl() ?: $base['variant_thumbnail_url'],
            'color' => $customization->color,
            'size' => $customization->size,
            'placement' => $customization->placement,
            'print_file_url' => $customization->printFileUrl() ?: $customization->uploadUrl(),
            'custom_text_summary' => $this->textSummary($customization),
        ]);
    }

    private function textSummary(ProductCustomization $customization): ?string
    {
        $settings = $customization->text_settings;
        if (! is_array($settings) || empty($settings['content'])) {
            return null;
        }

        return Str::limit((string) $settings['content'], 80);
    }
}
