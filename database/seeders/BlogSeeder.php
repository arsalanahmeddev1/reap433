<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['title' => 'Texas Voter Suppression Laws: What Every Grand Prairie Resident Needs to Know Before 2025', 'slug' => 'texas-voter-suppression-laws-what-every-grand-prairie-resident-needs-to-know-before-2025', 'body' => 'A deep-dive analysis of SB1 and its compounding effects on Black and Brown communities across Dallas County — and the grassroots organizations fighting back on every front.', 'featured_image' => 'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?w=900&q=80&auto=format&fit=crop', 'is_published' => true, 'published_at' => now(), 'created_by' => 1, 'updated_by' => 1, 'blog_category_id' => 1],
            ['title' => 'Breaking: Texas Legislatures New Education Voucher Bill — Full Analysis', 'slug' => 'breaking-texas-legislatures-new-education-voucher-bill-full-analysis', 'body' => 'What HB174 means for public education funding in Texas, which communities it targets, and how to make your voice heard before it passes.', 'featured_image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80&auto=format&fit=crop', 'is_published' => true, 'published_at' => now(), 'created_by' => 1, 'updated_by' => 1, 'blog_category_id' => 2],
            ['title' => 'Defunding Public Schools: How GISD and Grand Prairie ISD Are Responding', 'slug' => 'defunding-public-schools-how-gisd-and-grand-prairie-isd-are-responding', 'body' => 'An honest look at budget cuts hitting the Grand Prairie Independent School District and the parent coalitions mobilizing to push back.', 'featured_image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600&q=80&auto=format&fit=crop', 'is_published' => true, 'published_at' => now(), 'created_by' => 1, 'updated_by' => 1, 'blog_category_id' => 3],
            ['title' => 'Rising Leaders: 10 Grand Prairie Changemakers Under 35 You Need to Know', 'slug' => 'rising-leaders-10-grand-prairie-changemakers-under-35-you-need-to-know', 'body' => 'From school board members to nonprofit founders — meet the next generation of civic leaders reshaping the DFW landscape from the ground up.', 'featured_image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=600&q=80&auto=format&fit=crop', 'is_published' => true, 'published_at' => now(), 'created_by' => 1, 'updated_by' => 1, 'blog_category_id' => 4],
        ];

        foreach ($rows as $row) {
            Blog::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'body' => $row['body'],
                    'featured_image' => $row['featured_image'],
                    'is_published' => $row['is_published'],
                    'published_at' => $row['published_at'],
                    'created_by' => $row['created_by'],
                    'updated_by' => $row['updated_by'],
                    'blog_category_id' => $row['blog_category_id'],
                ]
            );
        }
    }
}
