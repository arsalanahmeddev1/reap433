<?php

namespace Database\Seeders;

use App\Models\QuizType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizTypeSeeder extends Seeder
{
    /**
     * Seed the quiz types.
     */
    public function run(): void
    {
        $rows = [
            [
                'title' => 'Beginner',
                'slogan_text' => 'Easy',
                'description' => 'Easy Bible questions for new learners',
            ],
            [
                'title' => 'Intermediate',
                'slogan_text' => 'Medium',
                'description' => 'Moderate challenge for growing learners.',
            ],
            [
                'title' => 'Expert',
                'slogan_text' => 'Hard',
                'description' => 'Deep Scripture knowledge for advanced learners.',
            ],
        ];

        foreach ($rows as $row) {
            $slug = Str::slug($row['title']);

            QuizType::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $row['title'],
                    'image_url' => null,
                    'slogan_text' => $row['slogan_text'],
                    'description' => $row['description'],
                    'seo_title' => $row['title'],
                    'seo_description' => $row['description'],
                ]
            );
        }
    }
}
