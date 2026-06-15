<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printful_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('printful_product_id')->nullable()->unique();
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->string('thumbnail_url')->nullable();
            $table->boolean('is_synced')->default(true);
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printful_products');
    }
};
