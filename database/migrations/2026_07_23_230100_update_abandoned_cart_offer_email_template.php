<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_ABANDONED_CART)
            ->update([
                'name' => 'Abandoned cart offer',
                'subject' => 'Special offer for your {{site_name}} cart: {{coupon_code}}',
                'body' => <<<'HTML'
<p>Hi {{customer_name}},</p>
<p>{{offer_message}}</p>
<p><strong>Your exclusive coupon:</strong> <code>{{coupon_code}}</code> ({{discount_in_percent}}% off)</p>
<p><strong>Items in your cart:</strong> {{cart_items_count}}<br><strong>Cart total:</strong> {{currency}} {{cart_amount}}</p>
<p>{{cart_items}}</p>
<p><a href="{{checkout_url}}" style="display:inline-block;padding:12px 20px;background:#bf8834;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:600;">Checkout with this offer</a></p>
<p>Or <a href="{{cart_url}}">view your cart</a>.</p>
<p>Thank you for shopping with {{site_name}}.</p>
HTML,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_ABANDONED_CART)
            ->update([
                'name' => 'Abandoned cart reminder',
                'subject' => 'You left items in your {{site_name}} cart',
                'body' => <<<'HTML'
<p>Hi {{customer_name}},</p>
<p>You still have items waiting in your cart. Complete your order whenever you're ready.</p>
<p><strong>Items:</strong> {{cart_items_count}}<br><strong>Cart total:</strong> {{currency}} {{cart_amount}}</p>
<p>{{cart_items}}</p>
<p><a href="{{checkout_url}}" style="display:inline-block;padding:12px 20px;background:#bf8834;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:600;">Complete checkout</a></p>
<p>Or <a href="{{cart_url}}">view your cart</a>.</p>
<p>Thank you for shopping with {{site_name}}.</p>
HTML,
                'updated_at' => now(),
            ]);
    }
};
