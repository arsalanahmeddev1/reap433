<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CheckoutCouponService
{
    private const SESSION_KEY = 'checkout_coupon_code';

    public function __construct(
        private readonly CartService $cart,
    ) {}

    /**
     * @return array{success: bool, message: string}
     */
    public function apply(string $code): array
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return ['success' => false, 'message' => __('Please enter a coupon code.')];
        }

        if ($this->cart->count() === 0) {
            return ['success' => false, 'message' => __('Your cart is empty.')];
        }

        $coupon = $this->findActiveByCode($code);

        if (! $coupon) {
            return ['success' => false, 'message' => __('Invalid or inactive coupon code.')];
        }

        Session::put(self::SESSION_KEY, $coupon->coupon_code);

        return [
            'success' => true,
            'message' => __('Coupon applied successfully.'),
        ];
    }

    public function remove(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function code(): ?string
    {
        $code = Session::get(self::SESSION_KEY);

        return is_string($code) && $code !== '' ? strtoupper($code) : null;
    }

    public function resolve(): ?Coupon
    {
        $code = $this->code();

        if ($code === null) {
            return null;
        }

        $coupon = $this->findActiveByCode($code);

        if (! $coupon) {
            $this->remove();

            return null;
        }

        return $coupon;
    }

    /**
     * @return array{
     *     coupon: ?Coupon,
     *     cart_subtotal: float,
     *     discount_amount: float,
     *     total: float
     * }
     */
    public function summary(?float $cartSubtotal = null): array
    {
        $cartSubtotal = $cartSubtotal ?? $this->cart->subtotal();
        $coupon = $this->resolve();
        $discount = $this->discountFor($cartSubtotal, $coupon);
        $total = max(0, round($cartSubtotal - $discount, 2));

        return [
            'coupon' => $coupon,
            'cart_subtotal' => $cartSubtotal,
            'discount_amount' => $discount,
            'total' => $total,
        ];
    }

    public function discountFor(float $subtotal, ?Coupon $coupon = null): float
    {
        $coupon ??= $this->resolve();

        if (! $coupon || $subtotal <= 0) {
            return 0.0;
        }

        $percent = max(0, min(100, (int) $coupon->discount_in_percent));
        $discount = round($subtotal * ($percent / 100), 2);

        return min($discount, $subtotal);
    }

    public function findActiveByCode(string $code): ?Coupon
    {
        return Coupon::query()
            ->where('coupon_code', strtoupper(trim($code)))
            ->where('status', 'active')
            ->first();
    }
}
