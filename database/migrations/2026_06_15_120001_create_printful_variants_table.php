<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printful_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printful_product_id')->constrained('printful_products')->cascadeOnDelete();
            $table->unsignedBigInteger('printful_variant_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('name')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printful_variants');
    }
};
