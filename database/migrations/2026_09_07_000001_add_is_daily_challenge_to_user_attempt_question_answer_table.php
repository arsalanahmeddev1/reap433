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
        Schema::table('user_attempt_question_answer', function (Blueprint $table) {
            if (! Schema::hasColumn('user_attempt_question_answer', 'is_daily_challenge')) {
                $table->enum('is_daily_challenge', ['0', '1'])->default('0')->after('is_complete');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_attempt_question_answer', function (Blueprint $table) {
            if (Schema::hasColumn('user_attempt_question_answer', 'is_daily_challenge')) {
                $table->dropColumn('is_daily_challenge');
            }
        });
    }
};
