<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WholeSellerSetting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'product_discount' => 'integer',
            'order_quantity' => 'integer',
        ];
    }

    public static function current(): self
    {
        $setting = static::query()->first();

        if ($setting) {
            return $setting;
        }

        return static::query()->create([
            'product_discount' => 1,
            'order_quantity' => 1,
        ]);
    }

    public static function productDiscountPercent(): int
    {
        return max(0, min(100, (int) static::current()->product_discount));
    }

    public static function minimumOrderQuantity(): int
    {
        return max(1, (int) static::current()->order_quantity);
    }

    public static function appliesToCurrentUser(): bool
    {
        $user = Auth::user();

        return $user
            && $user->isWholeSeller()
            && $user->isApproved();
    }

    /**
     * Apply admin whole-seller product discount for approved whole sellers only.
     */
    public static function applyProductDiscount(float $price): float
    {
        if (! static::appliesToCurrentUser()) {
            return round($price, 2);
        }

        $discount = static::productDiscountPercent();

        if ($discount <= 0) {
            return round($price, 2);
        }

        return round($price * (1 - ($discount / 100)), 2);
    }

    /**
     * Ensure each cart line meets the minimum wholesale order quantity.
     *
     * @param  array<string, array<string, mixed>>  $items
     */
    public static function cartQuantityValidationMessage(array $items): ?string
    {
        if (! static::appliesToCurrentUser()) {
            return null;
        }

        $minQty = static::minimumOrderQuantity();
        $invalid = [];

        foreach ($items as $item) {
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity < $minQty) {
                $name = (string) ($item['product_name'] ?? __('Product'));
                if (! empty($item['variant_name'])) {
                    $name .= ' ('.$item['variant_name'].')';
                }
                $invalid[] = $name;
            }
        }

        if ($invalid === []) {
            return null;
        }

        return __('Wholesale orders require at least :min of each product. Please update the quantity for: :products', [
            'min' => $minQty,
            'products' => implode(', ', $invalid),
        ]);
    }
}
