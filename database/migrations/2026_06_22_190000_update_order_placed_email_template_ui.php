<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')
            ->where('slug', 'order_placed')
            ->update([
                'body' => $this->orderPlacedBody(),
                'updated_at' => now(),
            ]);

        DB::table('email_templates')
            ->where('slug', 'order_status_changed')
            ->update([
                'body' => $this->orderStatusChangedBody(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('slug', 'order_placed')
            ->update([
                'body' => '<p>Hi {{customer_name}},</p><p>Thank you for your order. We have received it and will process it soon.</p><p><strong>Order number:</strong> {{order_number}}<br><strong>Status:</strong> {{status_label}}<br><strong>Subtotal:</strong> {{currency}} {{order_subtotal}}<br><strong>Date:</strong> {{order_date}}</p><p>{{order_items}}</p><p>Thank you for shopping with REAP433.</p>',
                'updated_at' => now(),
            ]);

        DB::table('email_templates')
            ->where('slug', 'order_status_changed')
            ->update([
                'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> status has been updated.</p><p><strong>Previous status:</strong> {{old_status}}<br><strong>New status:</strong> {{new_status}}</p><p><strong>Subtotal:</strong> {{currency}} {{order_subtotal}}</p><p>Thank you for shopping with REAP433.</p>',
                'updated_at' => now(),
            ]);
    }

    private function orderPlacedBody(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order confirmation</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
<tr>
<td style="background-color:#1a1a1a;padding:28px 32px;text-align:center;">
<span style="font-size:26px;font-weight:700;color:#bf8834;letter-spacing:2px;">REAP433</span>
</td>
</tr>
<tr>
<td style="padding:36px 32px 24px;text-align:center;">
<div style="width:56px;height:56px;line-height:56px;border-radius:50%;background-color:#f0e8d4;color:#bf8834;font-size:28px;font-weight:700;margin:0 auto 16px;">&#10003;</div>
<h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#1a1a1a;">Thank you for your order!</h1>
<p style="margin:0;font-size:15px;line-height:1.6;color:#52525b;">Hi {{customer_name}}, we have received your order and will process it soon.</p>
</td>
</tr>
<tr>
<td style="padding:0 32px 24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa;border:1px solid #e4e4e7;border-radius:6px;">
<tr>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Order number</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{order_number}}</strong>
</td>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Date</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{order_date}}</strong>
</td>
</tr>
<tr>
<td style="padding:14px 18px;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Status</span><br>
<strong style="font-size:15px;color:#bf8834;">{{status_label}}</strong>
</td>
<td style="padding:14px 18px;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Payment</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{payment_status}}</strong>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding:0 32px 8px;">
<h2 style="margin:0 0 12px;font-size:16px;font-weight:700;color:#1a1a1a;">Order items</h2>
{{order_items}}
</td>
</tr>
<tr>
<td style="padding:24px 32px;">
<h2 style="margin:0 0 10px;font-size:16px;font-weight:700;color:#1a1a1a;">Shipping address</h2>
<p style="margin:0;padding:14px 18px;background-color:#fafafa;border:1px solid #e4e4e7;border-radius:6px;font-size:14px;line-height:1.7;color:#3f3f46;">{{shipping_address}}</p>
</td>
</tr>
<tr>
<td style="padding:0 32px 36px;text-align:center;">
<a href="{{order_url}}" style="display:inline-block;padding:14px 32px;background-color:#bf8834;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;border-radius:6px;">View your order</a>
</td>
</tr>
<tr>
<td style="padding:20px 32px;background-color:#fafafa;border-top:1px solid #e4e4e7;text-align:center;">
<p style="margin:0 0 6px;font-size:13px;color:#71717a;">Questions about your order? Reply to this email.</p>
<p style="margin:0;font-size:13px;color:#a1a1aa;">&copy; REAP433. All rights reserved.</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    private function orderStatusChangedBody(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order status update</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
<tr>
<td style="background-color:#1a1a1a;padding:28px 32px;text-align:center;">
<span style="font-size:26px;font-weight:700;color:#bf8834;letter-spacing:2px;">REAP433</span>
</td>
</tr>
<tr>
<td style="padding:36px 32px 24px;">
<h1 style="margin:0 0 12px;font-size:22px;font-weight:700;color:#1a1a1a;">Order status updated</h1>
<p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#52525b;">Hi {{customer_name}}, your order <strong>{{order_number}}</strong> has a new status.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa;border:1px solid #e4e4e7;border-radius:6px;">
<tr>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;width:50%;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Previous status</span><br>
<strong style="font-size:15px;color:#71717a;">{{old_status}}</strong>
</td>
<td style="padding:14px 18px;border-bottom:1px solid #e4e4e7;width:50%;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">New status</span><br>
<strong style="font-size:15px;color:#bf8834;">{{new_status}}</strong>
</td>
</tr>
<tr>
<td colspan="2" style="padding:14px 18px;">
<span style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:#71717a;">Subtotal</span><br>
<strong style="font-size:15px;color:#1a1a1a;">{{currency}} {{order_subtotal}}</strong>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding:0 32px 36px;text-align:center;">
<a href="{{order_url}}" style="display:inline-block;padding:14px 32px;background-color:#bf8834;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;border-radius:6px;">View your order</a>
</td>
</tr>
<tr>
<td style="padding:20px 32px;background-color:#fafafa;border-top:1px solid #e4e4e7;text-align:center;">
<p style="margin:0;font-size:13px;color:#a1a1aa;">&copy; REAP433. All rights reserved.</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }
};
