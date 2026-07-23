<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_customizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('printful_product_id')->constrained('printful_products')->cascadeOnDelete();
            $table->unsignedBigInteger('printful_sync_product_id')->nullable()->index();
            $table->foreignId('printful_variant_id')->constrained('printful_variants')->cascadeOnDelete();
            $table->unsignedBigInteger('printful_sync_variant_id')->nullable()->index();
            $table->unsignedBigInteger('catalog_variant_id')->nullable();
            $table->unsignedBigInteger('catalog_product_id')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('placement')->default('default');
            $table->longText('canvas_json')->nullable();
            $table->json('text_settings')->nullable();
            $table->json('image_settings')->nullable();
            $table->json('print_area')->nullable();
            $table->string('status')->default('draft'); // draft|finalized
            $table->string('preview_path')->nullable();
            $table->string('print_file_path')->nullable();
            $table->string('upload_path')->nullable();
            $table->decimal('customization_fee', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('product_customization_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_customization_id')
                ->constrained('product_customizations')
                ->cascadeOnDelete();
            $table->string('type'); // upload|print|preview|temp
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('original_name')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('bytes')->nullable();
            $table->timestamps();

            $table->index(['product_customization_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_customization_files');
        Schema::dropIfExists('product_customizations');
    }
};
