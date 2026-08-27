<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('quize_categories', 'difficulty')) {
            return;
        }

        $mapped = [];

        $categories = DB::table('quize_categories')
            ->whereNotNull('difficulty')
            ->get(['id', 'difficulty']);

        foreach ($categories as $category) {
            if (is_numeric($category->difficulty)) {
                $quizTypeId = DB::table('quiz_type')
                    ->where('id', (int) $category->difficulty)
                    ->value('id');
            } else {
                $quizTypeId = DB::table('quiz_type')
                    ->where('title', $category->difficulty)
                    ->value('id');
            }

            $mapped[$category->id] = $quizTypeId;
        }

        Schema::table('quize_categories', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });

        Schema::table('quize_categories', function (Blueprint $table) {
            $table->foreignId('difficulty')
                ->nullable()
                ->after('estimated_time')
                ->constrained('quiz_type')
                ->nullOnDelete();
        });

        foreach ($mapped as $categoryId => $quizTypeId) {
            if ($quizTypeId) {
                DB::table('quize_categories')
                    ->where('id', $categoryId)
                    ->update(['difficulty' => $quizTypeId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('quize_categories', 'difficulty')) {
            return;
        }

        Schema::table('quize_categories', function (Blueprint $table) {
            $table->dropForeign(['difficulty']);
        });

        Schema::table('quize_categories', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });

        Schema::table('quize_categories', function (Blueprint $table) {
            $table->string('difficulty')->nullable()->after('estimated_time');
        });
    }
};
