<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whole_seller_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('product_discount')->default(1);
            $table->unsignedInteger('order_quantity')->default(1);
            $table->timestamps();
        });

        DB::table('whole_seller_settings')->insert([
            'product_discount' => 1,
            'order_quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('whole_seller_settings');
    }
};
