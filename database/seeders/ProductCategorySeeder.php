<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed example storefront / Printful product categories.
     * Images are uploaded later via Admin → Categories (stored in storage/app/public/categories/).
     */
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Hats',
                'slug' => 'hats',
                'description' => 'Hats, caps, and headwear from Printful.',
            ],
            [
                'name' => 'Hoodies',
                'slug' => 'hoodies',
                'description' => 'Hoodies and sweatshirts from Printful.',
            ],
            [
                'name' => 'Shirts',
                'slug' => 'shirts',
                'description' => 'Polos, tees, and shirts from Printful.',
            ],
            [
                'name' => 'Drinkware',
                'slug' => 'drinkware',
                'description' => 'Mugs and drinkware from Printful.',
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'description' => 'Candles and home products from Printful.',
            ],
        ];

        foreach ($rows as $row) {
            ProductCategory::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'parent_id' => 0,
                    'status' => 'active',
                ]
            );
        }
    }
}
