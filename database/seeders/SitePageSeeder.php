<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SitePageSeeder extends Seeder
{
    /**
     * Seed the site pages.
     */
    public function run(): void
    {
        $rows = [
            [
                'title' => 'Privacy Policy',
                'description' => $this->htmlFromFile('privacy-policy.html'),
                'seo_description' => 'REAP433 Privacy Policy explaining how we collect, use, disclose, retain, and protect personal information.',
            ],
            [
                'title' => 'Terms of Service',
                'description' => null,
                'seo_description' => null,
            ],
            [
                'title' => 'Trademark Notice',
                'description' => null,
                'seo_description' => null,
            ],
            [
                'title' => 'Accessibility',
                'description' => null,
                'seo_description' => null,
            ],
        ];

        foreach ($rows as $row) {
            $slug = Str::slug($row['title']);

            SitePage::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $row['title'],
                    'image_url' => null,
                    'description' => $row['description'],
                    'seo_title' => $row['title'],
                    'seo_description' => $row['seo_description'],
                    'status' => 'active',
                ]
            );
        }
    }

    private function htmlFromFile(string $filename): ?string
    {
        $path = database_path('seeders/data/'.$filename);

        if (! is_file($path)) {
            return null;
        }

        $html = trim((string) file_get_contents($path));

        return $html !== '' ? $html : null;
    }
}
