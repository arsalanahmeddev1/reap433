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
            $columns = collect(['xp', 'coins'])
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
            if (! Schema::hasColumn('quize_categories', 'xp')) {
                $table->unsignedInteger('xp')->nullable()->after('best_score');
            }
            if (! Schema::hasColumn('quize_categories', 'coins')) {
                $table->unsignedInteger('coins')->nullable()->after('xp');
            }
        });
    }
};
