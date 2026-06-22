<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('email_templates')->insert([
            [
                'slug' => 'order_placed',
                'name' => 'Order placed',
                'subject' => 'Your REAP433 order {{order_number}} has been received',
                'body' => '<p>Hi {{customer_name}},</p><p>Thank you for your order. We have received it and will process it soon.</p><p><strong>Order number:</strong> {{order_number}}<br><strong>Status:</strong> {{status_label}}<br><strong>Subtotal:</strong> {{currency}} {{order_subtotal}}<br><strong>Date:</strong> {{order_date}}</p><p>{{order_items}}</p><p>Thank you for shopping with REAP433.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'order_status_changed',
                'name' => 'Order status changed',
                'subject' => 'Order {{order_number}} status update: {{new_status}}',
                'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>{{order_number}}</strong> status has been updated.</p><p><strong>Previous status:</strong> {{old_status}}<br><strong>New status:</strong> {{new_status}}</p><p><strong>Subtotal:</strong> {{currency}} {{order_subtotal}}</p><p>Thank you for shopping with REAP433.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
