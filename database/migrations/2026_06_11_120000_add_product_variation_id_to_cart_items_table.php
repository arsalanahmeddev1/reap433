<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'product_variation_id')) {
                $table->foreignId('product_variation_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('product_variations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'product_variation_id')) {
                $table->dropConstrainedForeignId('product_variation_id');
            }
        });
    }
};
