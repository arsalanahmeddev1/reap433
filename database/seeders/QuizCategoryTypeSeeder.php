<?php

namespace Database\Seeders;

use App\Models\QuizCategoryType;
use App\Models\QuizType;
use App\Models\QuizeCategory;
use Illuminate\Database\Seeder;

class QuizCategoryTypeSeeder extends Seeder
{
    /**
     * Seed quiz category and quiz type relations.
     */
    public function run(): void
    {
        $categories = QuizeCategory::query()->orderBy('id')->get();
        $typeIds = QuizType::query()->orderBy('id')->pluck('id')->all();

        if ($categories->isEmpty() || $typeIds === []) {
            return;
        }

        foreach ($categories as $index => $category) {
            // Attach all types so each category supports every difficulty.
            foreach ($typeIds as $typeId) {
                QuizCategoryType::updateOrCreate(
                    [
                        'quiz_category_id' => $category->id,
                        'quiz_type_id' => $typeId,
                    ],
                    []
                );
            }
        }
    }
}
