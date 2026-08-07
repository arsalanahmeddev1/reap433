<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_page_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_page_id')->constrained('collection_pages')->cascadeOnDelete();
            $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['collection_page_id', 'product_category_id'], 'collection_page_category_unique');
        });

        Schema::table('collection_pages', function (Blueprint $table) {
            $table->json('faqs')->nullable()->after('description');
        });

        $pages = DB::table('collection_pages')->whereNotNull('category')->get(['id', 'category']);
        $now = now();

        foreach ($pages as $page) {
            $exists = DB::table('collection_page_category')
                ->where('collection_page_id', $page->id)
                ->where('product_category_id', $page->category)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('collection_page_category')->insert([
                'collection_page_id' => $page->id,
                'product_category_id' => $page->category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('collection_pages', function (Blueprint $table) {
            $table->dropColumn('faqs');
        });

        Schema::dropIfExists('collection_page_category');
    }
};
