<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_category_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_category_id')->constrained('quize_categories')->cascadeOnDelete();
            $table->foreignId('quiz_type_id')->constrained('quiz_type')->cascadeOnDelete();
            $table->unique(['quiz_category_id', 'quiz_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_category_type');
    }
};
