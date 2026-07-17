<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printful_products', function (Blueprint $table) {
            if (! Schema::hasColumn('printful_products', 'category_name')) {
                $table->string('category_name')->nullable()->after('name');
            }
        });

        if (Schema::hasColumn('printful_products', 'category')) {
            DB::table('printful_products')
                ->whereNotNull('category')
                ->whereNull('category_name')
                ->update([
                    'category_name' => DB::raw('category'),
                ]);

            Schema::table('printful_products', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        Schema::table('printful_products', function (Blueprint $table) {
            if (! Schema::hasColumn('printful_products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('category_name')
                    ->constrained('product_categories')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('printful_products', function (Blueprint $table) {
            if (Schema::hasColumn('printful_products', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::table('printful_products', function (Blueprint $table) {
            if (! Schema::hasColumn('printful_products', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
        });

        if (Schema::hasColumn('printful_products', 'category_name')) {
            DB::table('printful_products')
                ->whereNotNull('category_name')
                ->whereNull('category')
                ->update([
                    'category' => DB::raw('category_name'),
                ]);

            Schema::table('printful_products', function (Blueprint $table) {
                $table->dropColumn('category_name');
            });
        }
    }
};
