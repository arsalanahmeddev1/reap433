<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Order;

class OrderEmailService
{
    public function __construct(
        private readonly EmailTemplateService $templates,
    ) {}

    public function sendOrderPlaced(Order $order): bool
    {
        $order->loadMissing('items');

        return $this->templates->send(
            EmailTemplate::SLUG_ORDER_PLACED,
            $order->customer_email,
            $this->buildReplacements($order),
        );
    }

    public function sendOrderStatusChanged(Order $order, string $oldStatus): bool
    {
        $order->loadMissing('items');

        return $this->templates->send(
            EmailTemplate::SLUG_ORDER_STATUS_CHANGED,
            $order->customer_email,
            array_merge($this->buildReplacements($order), [
                'old_status' => ucwords(str_replace('_', ' ', $oldStatus)),
                'new_status' => $order->statusLabel(),
            ]),
        );
    }

    /**
     * @return array<string, string>
     */
    private function buildReplacements(Order $order): array
    {
        return [
            'order_number' => $order->publicOrderNumber(),
            'customer_name' => (string) $order->customer_name,
            'customer_email' => (string) $order->customer_email,
            'order_status' => (string) $order->status,
            'status_label' => $order->statusLabel(),
            'order_subtotal' => number_format((float) $order->subtotal, 2),
            'currency' => (string) ($order->currency ?? 'USD'),
            'order_date' => $order->created_at?->format('M j, Y g:i A') ?? now()->format('M j, Y g:i A'),
            'payment_status' => ucfirst((string) $order->payment_status),
            'shipping_address' => $this->formatShippingAddress($order),
            'order_items' => $this->formatOrderItems($order),
            'order_url' => route('profile.orders.show', $order),
        ];
    }

    private function formatShippingAddress(Order $order): string
    {
        $lines = array_filter([
            $order->customer_name,
            $order->address1,
            $order->address2,
            trim($order->city.', '.$order->state_code.' '.$order->zip),
            strtoupper((string) $order->country_code),
        ]);

        return implode('<br>', array_map('e', $lines));
    }

    private function formatOrderItems(Order $order): string
    {
        if ($order->items->isEmpty()) {
            return '';
        }

        $currency = e((string) ($order->currency ?? 'USD'));
        $subtotal = number_format((float) $order->subtotal, 2);

        $rows = $order->items->map(function ($item) use ($currency) {
            $name = e($item->product_name);
            $variant = $item->variant_name
                ? '<br><span style="font-size:12px;color:#71717a;">'.e($item->variant_name).'</span>'
                : '';
            $qty = (int) $item->quantity;
            $total = $currency.' '.number_format((float) $item->total, 2);

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
<td colspan="2" style="padding:14px 16px;text-align:right;font-size:14px;font-weight:600;color:#52525b;">Subtotal</td>
<td style="padding:14px 16px;text-align:right;font-size:15px;font-weight:700;color:#bf8834;">{$currency} {$subtotal}</td>
</tr>
</tfoot>
</table>
HTML;
    }
}
