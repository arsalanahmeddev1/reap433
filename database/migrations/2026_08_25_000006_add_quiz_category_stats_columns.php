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
        Schema::table('quize_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('quize_categories', 'estimated_time')) {
                $table->string('estimated_time')->nullable()->after('seo_description');
            }
            if (! Schema::hasColumn('quize_categories', 'difficulty')) {
                $table->string('difficulty')->nullable()->after('estimated_time');
            }
            if (! Schema::hasColumn('quize_categories', 'best_score')) {
                $table->unsignedInteger('best_score')->nullable()->after('difficulty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quize_categories', function (Blueprint $table) {
            $columns = collect(['estimated_time', 'difficulty', 'best_score'])
                ->filter(fn (string $column) => Schema::hasColumn('quize_categories', $column))
                ->values()
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
