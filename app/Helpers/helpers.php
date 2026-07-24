<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('checkUser')) {
    function checkUser()
    {
        return Auth::user();
    }
}

if (! function_exists('formatRole')) {
    function formatRole($roleName)
    {
        return \Illuminate\Support\Str::of($roleName ?? 'Not Set')->replace('_', ' ')->title();
    }
}

if (! function_exists('isRole')) {
    function isRole($role)
    {
        $user = Auth::user();
        if (!$user) return false;

        $userRole = $user->roles->first();

        if (!$userRole) return false;

        // If role passed is numeric → check id
        if (is_numeric($role)) {
            return $userRole->id == $role;
        }

        // If string → check role name
        return $userRole->name === $role;
    }
}

function generateRandomPassword($length = 10)
{
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*'), 0, $length);
}


if (!function_exists('userRoleId')) {
    function userRoleId()
    {
        return auth()->check() ? auth()->user()->roles->first()->id : null;
    }
}

if (!function_exists('userHasRole')) {
    function userHasRole($roleName)
    {
        return auth()->check() && auth()->user()->hasRole($roleName);
    }
}

if (! function_exists('current_user')) {
    function current_user() {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

if (! function_exists('wholesaler_product_price')) {
    /**
     * Storefront price after whole-seller discount (when applicable).
     */
    function wholesaler_product_price(float|int|string|null $price): ?float
    {
        if ($price === null || $price === '') {
            return null;
        }

        return \App\Models\WholeSellerSetting::applyProductDiscount((float) $price);
    }
}

if (! function_exists('wholesaler_discount_percent')) {
    function wholesaler_discount_percent(): int
    {
        if (! \App\Models\WholeSellerSetting::appliesToCurrentUser()) {
            return 0;
        }

        return \App\Models\WholeSellerSetting::productDiscountPercent();
    }
}

if (! function_exists('wholesaler_min_order_quantity')) {
    function wholesaler_min_order_quantity(): int
    {
        if (! \App\Models\WholeSellerSetting::appliesToCurrentUser()) {
            return 1;
        }

        return \App\Models\WholeSellerSetting::minimumOrderQuantity();
    }
}
