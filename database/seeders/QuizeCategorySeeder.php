<?php

namespace Database\Seeders;

use App\Models\QuizeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizeCategorySeeder extends Seeder
{
    /**
     * Seed the quiz categories.
     */
    public function run(): void
    {
        $titles = [
            'Old Testament',
            'New Testament',
            'Characters',
            'Miracles',
            'Prophecy',
            'Bible Knowledge',
            'The Cross',
        ];

        foreach ($titles as $title) {
            $slug = Str::slug($title);

            QuizeCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'image_url' => null,
                    'description' => null,
                    'seo_title' => $title,
                    'seo_description' => null,
                ]
            );
        }
    }
}
