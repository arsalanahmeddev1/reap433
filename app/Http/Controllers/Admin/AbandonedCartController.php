<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartOffer;
use App\Models\Coupon;
use App\Models\User;
use App\Services\AbandonedCartEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbandonedCartController extends Controller
{
    public function __construct(
        private readonly AbandonedCartEmailService $abandonedCartEmails,
    ) {}

    public function index(): View
    {
        $users = User::query()
            ->whereHas('printfulCartItems')
            ->with('printfulCartItems')
            ->get()
            ->map(function (User $user) {
                $items = $user->printfulCartItems;
                $user->cart_items_count = (int) $items->sum('quantity');
                $user->cart_amount = round((float) $items->sum(fn ($item) => $item->lineTotal()), 2);
                $user->cart_currency = strtoupper((string) ($items->first()?->currency ?? 'USD'));
                $user->cart_updated_at = $items->max('updated_at');

                return $user;
            })
            ->sortByDesc('cart_updated_at')
            ->values();

        return view('screens.admin.abandoned-carts.index', compact('users'));
    }

    public function show(User $user): View
    {
        $items = $user->printfulCartItems()->latest('updated_at')->get();

        if ($items->isEmpty()) {
            abort(404);
        }

        $cartItemsCount = (int) $items->sum('quantity');
        $cartAmount = round((float) $items->sum(fn ($item) => $item->lineTotal()), 2);
        $cartCurrency = strtoupper((string) ($items->first()?->currency ?? 'USD'));

        $activeCoupons = Coupon::query()
            ->where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title', 'coupon_code', 'discount_in_percent']);

        $sentOffers = AbandonedCartOffer::query()
            ->where('user_id', $user->id)
            ->with(['coupon', 'sender'])
            ->latest('sent_at')
            ->get();

        return view('screens.admin.abandoned-carts.show', compact(
            'user',
            'items',
            'cartItemsCount',
            'cartAmount',
            'cartCurrency',
            'activeCoupons',
            'sentOffers',
        ));
    }

    public function sendOffer(Request $request, User $user): RedirectResponse
    {
        $items = $user->printfulCartItems()->latest('updated_at')->get();

        if ($items->isEmpty()) {
            return redirect()
                ->route('abandoned-carts.index')
                ->with('error', __('This cart is empty.'));
        }

        $validated = $request->validate([
            'coupon_id' => ['required', 'integer', 'exists:coupons,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $coupon = Coupon::query()
            ->whereKey($validated['coupon_id'])
            ->where('status', 'active')
            ->first();

        if (! $coupon) {
            return redirect()
                ->route('abandoned-carts.show', $user)
                ->withInput()
                ->with('error', __('Please select an active coupon.'));
        }

        $sent = $this->abandonedCartEmails->sendOffer(
            $user,
            $items,
            $coupon,
            $validated['message'],
        );

        if (! $sent) {
            return redirect()
                ->route('abandoned-carts.show', $user)
                ->withInput()
                ->with('error', __('Could not send the offer email. Set real SMTP credentials in .env (MAIL_MAILER=smtp, MAIL_USERNAME, MAIL_PASSWORD). Right now mail is not configured for inbox delivery.'));
        }

        AbandonedCartOffer::query()->create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->coupon_code,
            'discount_in_percent' => $coupon->discount_in_percent,
            'message' => $validated['message'],
            'sent_by' => $request->user()->id,
            'sent_at' => now(),
        ]);

        return redirect()
            ->route('abandoned-carts.show', $user)
            ->with('success', __('Offer email sent to :email with coupon :code.', [
                'email' => $user->email,
                'code' => $coupon->coupon_code,
            ]));
    }
}
