<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id'];

    public function items()
    {

        return $this->hasMany(CartItem::class);
    }

    public function total()
    {
        return $this->items->sum('subtotal');
    }

    public static function current()
    {
        $cart = auth()->check()
            ? self::firstOrCreate(['user_id' => auth()->id()])
            : self::firstOrCreate(['session_id' => session()->getId()]);

        $cart->loadMissing('items.product');

        return $cart;
    }

    /** Total line-item quantity for the current user or session (does not create a cart row). */
    public static function itemCount(): int
    {
        if (auth()->check()) {
            $cart = self::query()->where('user_id', auth()->id())->first();
        } else {
            $cart = self::query()->where('session_id', session()->getId())->first();
        }

        if (! $cart) {
            return 0;
        }

        return (int) $cart->items()->sum('qty');
    }

    /**
     * Merge guest cart (by session) into user cart after login.
     * Pass $guestSessionId when session was already regenerated (e.g. by Fortify) so we use the pre-login session ID.
     *
     * @param  User  $user
     * @param  string|null  $guestSessionId  Pre-login session ID (captured before login middleware)
     */
    public static function attachToUserAfterLogin($user, $guestSessionId = null)
    {
        $sessionId = $guestSessionId ?? session()->getId();
        $sessionCart = self::where('session_id', $sessionId)->first();
        $userCart = self::where('user_id', $user->id)->first();

        if (! $sessionCart) {
            return;
        }

        if (! $userCart) {
            $sessionCart->update([
                'user_id' => $user->id,
                'session_id' => null,
            ]);

            return;
        }

        foreach ($sessionCart->items as $item) {
            $existingQuery = $userCart->items()->where('product_id', $item->product_id);
            if ($item->product_variation_id) {
                $existingQuery->where('product_variation_id', $item->product_variation_id);
            } else {
                $existingQuery->whereNull('product_variation_id');
            }
            $existingItem = $existingQuery->first();

            if ($existingItem) {
                $existingItem->increment('qty', $item->qty);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'product_variation_id' => $item->product_variation_id,
                    'qty' => $item->qty,
                    'price' => $item->price,
                ]);
            }
        }

        $sessionCart->delete();
    }
}
