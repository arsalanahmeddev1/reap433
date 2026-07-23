<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\EmailTemplate;
use App\Models\PrintfulCartItem;
use App\Models\User;
use Illuminate\Support\Collection;

class AbandonedCartEmailService
{
    public function __construct(
        private readonly EmailTemplateService $templates,
    ) {}

    /**
     * @param  Collection<int, PrintfulCartItem>  $items
     */
    public function sendOffer(User $user, Collection $items, Coupon $coupon, string $message): bool
    {
        if ($items->isEmpty()) {
            return false;
        }

        return $this->templates->send(
            EmailTemplate::SLUG_ABANDONED_CART,
            $user->email,
            $this->buildReplacements($user, $items, $coupon, $message),
        );
    }

    /**
     * @param  Collection<int, PrintfulCartItem>  $items
     * @return array<string, string>
     */
    private function buildReplacements(User $user, Collection $items, Coupon $coupon, string $message): array
    {
        $currency = strtoupper((string) ($items->first()?->currency ?? 'USD'));
        $cartAmount = number_format(
            round((float) $items->sum(fn (PrintfulCartItem $item) => $item->lineTotal()), 2),
            2
        );
        $itemCount = (string) (int) $items->sum('quantity');
        $offerMessage = nl2br(e(trim($message)));

        return [
            'customer_name' => (string) $user->name,
            'customer_email' => (string) $user->email,
            'cart_items_count' => $itemCount,
            'cart_amount' => $cartAmount,
            'currency' => $currency,
            'cart_items' => $this->formatCartItems($items, $currency, $cartAmount),
            'cart_url' => route('cart.index'),
            'checkout_url' => route('checkout.index'),
            'site_name' => config('app.name', 'REAP433'),
            'offer_message' => $offerMessage,
            'coupon_code' => (string) $coupon->coupon_code,
            'discount_in_percent' => (string) $coupon->discount_in_percent,
            'coupon_title' => (string) $coupon->title,
        ];
    }

    /**
     * @param  Collection<int, PrintfulCartItem>  $items
     */
    private function formatCartItems(Collection $items, string $currency, string $cartAmount): string
    {
        $currencyEscaped = e($currency);

        $rows = $items->map(function (PrintfulCartItem $item) use ($currencyEscaped) {
            $name = e($item->product_name);
            $variant = $item->variant_name
                ? '<br><span style="font-size:12px;color:#71717a;">'.e($item->variant_name).'</span>'
                : '';
            $qty = (int) $item->quantity;
            $total = $currencyEscaped.' '.number_format($item->lineTotal(), 2);

            return <<<HTML
<tr>
<td style="padding:14px 16px;border-bottom:1px solid #e4e4e7;font-size:14px;color:#1a1a1a;">
<strong style="font-weight:600;">{$name}</strong>{$variant}
</td>
<td style="padding:14px 16px;border-bottom:1px solid #e4e4e7;font-size:14px;color:#3f3f46;text-align:center;">{$qty}</td>
<td style="padding:14px 16px;border-bottom:1px solid #e4e4e7;font-size:14px;color:#1a1a1a;text-align:right;font-weight:600;">{$total}</td>
</tr>
HTML;
        })->implode('');

        return <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:1px solid #e4e4e7;border-radius:6px;overflow:hidden;">
<thead>
<tr style="background-color:#1a1a1a;">
<th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:#ffffff;text-transform:uppercase;letter-spacing:0.5px;">Product</th>
<th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:600;color:#ffffff;text-transform:uppercase;letter-spacing:0.5px;">Qty</th>
<th style="padding:12px 16px;text-align:right;font-size:12px;font-weight:600;color:#ffffff;text-transform:uppercase;letter-spacing:0.5px;">Total</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
<tfoot>
<tr style="background-color:#fafafa;">
<td colspan="2" style="padding:14px 16px;text-align:right;font-size:14px;font-weight:600;color:#52525b;">Cart total</td>
<td style="padding:14px 16px;text-align:right;font-size:15px;font-weight:700;color:#bf8834;">{$currencyEscaped} {$cartAmount}</td>
</tr>
</tfoot>
</table>
HTML;
    }
}
