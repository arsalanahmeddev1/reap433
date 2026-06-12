<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('blog_category_id')
                ->nullable()
                ->after('id')
                ->constrained('blog_categories')
                ->nullOnDelete();
            $table->string('title')->after('blog_category_id');
            $table->string('slug')->unique()->after('title');
            $table->longText('body')->nullable()->after('slug');
            $table->string('featured_image')->nullable()->after('body');
            $table->boolean('is_published')->default(false)->after('featured_image');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->foreignId('created_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign(['blog_category_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'blog_category_id',
                'title',
                'slug',
                'body',
                'featured_image',
                'is_published',
                'published_at',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
