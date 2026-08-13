<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('collection_page_printful_product')) {
            return;
        }

        Schema::create('collection_page_printful_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_page_id')->constrained('collection_pages')->cascadeOnDelete();
            $table->foreignId('printful_product_id')->constrained('printful_products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['collection_page_id', 'printful_product_id'],
                'collection_page_printful_product_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_page_printful_product');
    }
};
