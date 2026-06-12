<?php

namespace Database\Seeders;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        
        $rows = [
            ['name' => 'Tops', 'slug' => 'tops', 'description' => 'Tops are the upper part of the body that covers the chest and shoulders. They are typically made of a light material and are designed to be worn over other clothing.', 'image' => 'tops.jpg'],
            ['name' => 'Headwear', 'slug' => 'headwear', 'description' => 'Headwear is the clothing that covers the head. It is typically made of a light material and is designed to be worn over other clothing.', 'image' => 'headwear.jpg'],
            ['name' => 'Footwear', 'slug' => 'footwear', 'description' => 'Footwear is the clothing that covers the feet. It is typically made of a light material and is designed to be worn over other clothing.', 'image' => 'footwear.jpg'],
            ['name' => 'Limited Edition', 'slug' => 'limited-edition', 'description' => 'Limited Edition is a collection of products that are limited in quantity. They are typically made of a light material and are designed to be worn over other clothing.', 'image' => 'limited-edition.jpg'],
        ];

        foreach ($rows as $row) {
            ProductCategory::updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 
                'description' => $row['description'],
                'image' => $row['image'],
                'status' => 'active',
                ]
            );
        }
    }
}
