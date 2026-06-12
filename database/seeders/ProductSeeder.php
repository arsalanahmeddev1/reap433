<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $addImages = static function (int $productId, array $images): void {
            ProductImage::query()->where('product_id', $productId)->delete();

            foreach (array_values($images) as $index => $imagePath) {
                ProductImage::create([
                    'product_id' => $productId,
                    'image' => $imagePath,
                    'is_primary' => $index === 0 ? 1 : 0,
                ]);
            }
        };

        $products = [
            [
                'name' => 'Infinity Signature Hoodie',
                'slug' => 'infinity-signature-hoodie',
                'category_id' => 4,
                'price' => 20.50,
                'description' => 'Premium heavyweight fleece, embroidered infinity mark, oversized silhouette.',
                'status' => 'active',
                'images' => [
                    'uploads/products/photo-1575428652377-a2d80e2277fc.jfif'
                ],
            ],
            [
                'name' => 'Legacy Structured Cap',
                'slug' => 'legacy-structured-cap',
                'category_id' => 3,
                'price' => 20.50,
                'description' => 'Legacy structured cap, made from 100% cotton, with a snap closure and a adjustable strap.',
                'status' => 'active',
                'images' => [
                    'uploads/products/photo-1588850561407-ed78c282e89b.jfif'
                ],
            ],
            [
                'name' => 'Civic Statement Tee',
                'slug' => 'civic-statement-tee',
                'category_id' => 2,
                'price' => 20.50,
                'description' => 'Civic statement tee, made from 100% cotton, with a snap closure and a adjustable strap.',
                'status' => 'active',
                'images' => [
                    'uploads/products/photo-1620799140408-edc6dcb6d633.jfif'
                ],
            ],
            [
                'name' => 'Infinity Runner',
                'slug' => 'infinity-runner',
                'category_id' => 2,
                'price' => 20.50,
                'description' => 'Infinity runner, made from 100% cotton, with a snap closure and a adjustable strap.',
                'status' => 'active',
                'images' => [
                    'uploads/products/photo-1542291026-7eec264c27ff.jfif'
                ],
            ],
            [
                'name' => 'Movement Crew Sweater',
                'slug' => 'movement-crew-sweater',
                'category_id' => 1,
                'price' => 20.50,
                'description' => 'Movement crew sweater, made from 100% cotton, with a snap closure and a adjustable strap.',
                'status' => 'active',
                'images' => [
                    'uploads/products/photo-1607522370275-f14206abe5d3.jfif'
                ],
            ],
            [
                'name' => 'Gold Label Snapback',
                'slug' => 'gold-label-snapback',
                'category_id' => 4,
                'price' => 20.50,
                'description' => 'Gold label snapback, made from 100% cotton, with a snap closure and a adjustable strap.',
                'status' => 'active',
                'images' => [
                    'uploads/products/photo-1575428652377-a2d80e2277fc.jfif'
                ],
            ],
        ];

        foreach ($products as $payload) {
            $images = $payload['images'] ?? [];
            unset($payload['images']);

            $product = Product::updateOrCreate(
                ['slug' => $payload['slug']],
                $payload + ['product_type_id' => 1]
            );

            $addImages((int) $product->id, $images);
        }
    }
}
