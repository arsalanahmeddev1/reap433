<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AchievementSeeder extends Seeder
{
    /**
     * Seed the achievements.
     */
    public function run(): void
    {
        $rows = [
            [
                'title' => 'First Quiz',
                'description' => 'Complete your first quiz',
                'xp' => 25,
                'coins' => 10,
            ],
            [
                'title' => '7 Day Streak',
                'description' => 'Maintain a 7 day streak',
                'xp' => 25,
                'coins' => 10,
            ],
            [
                'title' => 'Bible Scholar',
                'description' => 'Score 90% in any deck',
                'xp' => 25,
                'coins' => 10,
            ],
            [
                'title' => 'Master of Scripture',
                'description' => 'Complete 25 decks',
                'xp' => 25,
                'coins' => 10,
            ],
            [
                'title' => 'Challenge Champion',
                'description' => 'Win 10 daily challenges',
                'xp' => 25,
                'coins' => 10,
            ],
        ];

        foreach ($rows as $row) {
            $slug = Str::slug($row['title']);

            Achievement::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $row['title'],
                    'xp' => $row['xp'],
                    'coins' => $row['coins'],
                    'image_url' => null,
                    'description' => $row['description'],
                    'status' => 'active',
                ]
            );
        }
    }
}
