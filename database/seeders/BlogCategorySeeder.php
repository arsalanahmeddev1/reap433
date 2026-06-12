<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Categories aligned with the journal filter UI (excluding "All Posts", which is a front-end filter).
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Voting Rights', 'slug' => 'voting-rights', 'sort_order' => 1],
            ['name' => 'Education Reform', 'slug' => 'education-reform', 'sort_order' => 2],
            ['name' => 'Community Leadership', 'slug' => 'community-leadership', 'sort_order' => 3],
            ['name' => 'Policy Watch', 'slug' => 'policy-watch', 'sort_order' => 4],
        ];

        foreach ($rows as $row) {
            BlogCategory::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'status' => 'active',
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
