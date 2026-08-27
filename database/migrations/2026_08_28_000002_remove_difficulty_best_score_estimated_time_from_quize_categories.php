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
        if (
            Schema::hasColumn('quize_categories', 'difficulty')
            && Schema::hasTable('quiz_category_type')
        ) {
            $rows = DB::table('quize_categories')
                ->whereNotNull('difficulty')
                ->get(['id', 'difficulty']);

            foreach ($rows as $row) {
                $exists = DB::table('quiz_category_type')
                    ->where('quiz_category_id', $row->id)
                    ->where('quiz_type_id', $row->difficulty)
                    ->exists();

                if (! $exists && DB::table('quiz_type')->where('id', $row->difficulty)->exists()) {
                    DB::table('quiz_category_type')->insert([
                        'quiz_category_id' => $row->id,
                        'quiz_type_id' => $row->difficulty,
                    ]);
                }
            }
        }

        Schema::table('quize_categories', function (Blueprint $table) {
            if (Schema::hasColumn('quize_categories', 'difficulty')) {
                try {
                    $table->dropForeign(['difficulty']);
                } catch (\Throwable $e) {
                    // Foreign key may already be absent on some environments.
                }
            }
        });

        Schema::table('quize_categories', function (Blueprint $table) {
            $columns = collect(['difficulty', 'best_score', 'estimated_time'])
                ->filter(fn (string $column) => Schema::hasColumn('quize_categories', $column))
                ->values()
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quize_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('quize_categories', 'estimated_time')) {
                $table->string('estimated_time')->nullable()->after('seo_description');
            }
            if (! Schema::hasColumn('quize_categories', 'difficulty')) {
                $table->foreignId('difficulty')
                    ->nullable()
                    ->after('estimated_time')
                    ->constrained('quiz_type')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('quize_categories', 'best_score')) {
                $table->unsignedInteger('best_score')->nullable()->after('difficulty');
            }
        });
    }
};
