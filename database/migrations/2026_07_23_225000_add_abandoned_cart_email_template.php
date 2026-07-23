<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_ABANDONED_CART)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('email_templates')->insert([
            'slug' => EmailTemplate::SLUG_ABANDONED_CART,
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
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->where('slug', EmailTemplate::SLUG_ABANDONED_CART)
            ->delete();
    }
};
