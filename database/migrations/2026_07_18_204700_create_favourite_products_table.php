<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favourite_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Storefront catalogue is printful_products (see PrintfulProduct), not artifacts `products`.
            $table->foreignId('product_id')
                ->constrained('printful_products')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourite_products');
    }
};
