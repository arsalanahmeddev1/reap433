<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Simple Product
        ProductType::updateOrCreate(
            ['name' => 'Simple Product'], // unique key
            [
                'slug' => 'simple',
            ]
        );

        // Variable Product
        ProductType::updateOrCreate(
            ['name' => 'Variable Product'],
            [
                'slug' => 'variable',
            ]
        );
    }
}
