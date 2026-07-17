<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printful_products', function (Blueprint $table) {
            if (Schema::hasColumn('printful_products', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }

            if (! Schema::hasColumn('printful_products', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('printful_products', function (Blueprint $table) {
            if (Schema::hasColumn('printful_products', 'category')) {
                $table->dropColumn('category');
            }

            if (! Schema::hasColumn('printful_products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('name')
                    ->constrained('product_categories')
                    ->nullOnDelete();
            }
        });
    }
};
