<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printful_products', function (Blueprint $table) {
            if (! Schema::hasColumn('printful_products', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('name');
            }

            if (! Schema::hasColumn('printful_products', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('printful_products', function (Blueprint $table) {
            if (Schema::hasColumn('printful_products', 'seo_description')) {
                $table->dropColumn('seo_description');
            }

            if (Schema::hasColumn('printful_products', 'seo_title')) {
                $table->dropColumn('seo_title');
            }
        });
    }
};
