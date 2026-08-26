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
        Schema::create('user_attempt_question_answer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('quiz_category_id')->constrained('quize_categories')->cascadeOnDelete();
            $table->foreignId('quiz_type_id')->constrained('quiz_type')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('quiz_question')->cascadeOnDelete();
            $table->foreignId('answer_id')->constrained('quiz_answers')->cascadeOnDelete();
            $table->unsignedInteger('answer_xp')->nullable();
            $table->unsignedInteger('answer_coins')->nullable();
            $table->boolean('answer_is_right')->default(0);
            $table->boolean('is_complete')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_attempt_question_answer');
    }
};
