<?php

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Remove the fake "All Products" product category (not a real taxonomy bucket).
     * Reassign affected products to another active category before delete.
     */
    public function up(): void
    {
        $bad = ProductCategory::query()->where('slug', 'all-products')->first();
        if (! $bad) {
            return;
        }

        $replacementId = ProductCategory::query()
            ->where('id', '!=', $bad->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->value('id');

        if ($replacementId) {
            Product::query()->where('category_id', $bad->id)->update(['category_id' => $replacementId]);
        }

        $bad->delete();
    }

    public function down(): void
    {
        // Intentionally empty: do not recreate a non-taxonomy category.
    }
};
