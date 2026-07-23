<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printful_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('printful_variant_id')->nullable();
            $table->unsignedBigInteger('printful_product_id')->nullable();
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'variant_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printful_cart_items');
    }
};
