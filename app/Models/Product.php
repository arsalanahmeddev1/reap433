<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'from_price' => 'decimal:2',
            'to_price' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function slugFromName(string $name, ?int $ignoreId = null): string
    {
        $source = trim($name);

        return UniqueSlug::generate(self::class, 'slug', $source !== '' ? $source : 'product', $ignoreId);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    /** Variation rows for variable products. */
    public function attributeItems()
    {
        return $this->hasMany(ProductAttributeItem::class, 'product_id');
    }

    /** SKU-level variations (Size + Color + … per row). */
    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->orderBy('sort_order');
    }

    public function isVariable(): bool
    {
        return $this->productType?->slug === 'variable';
    }

    public function isSimple(): bool
    {
        return ! $this->isVariable();
    }

    /** Price label for storefront listings (range for variable, single price for simple). */
    public function listingPriceLabel(): string
    {
        if ($this->isVariable() && $this->from_price !== null && $this->to_price !== null) {
            return '$'.number_format((float) $this->from_price, 2).' - $'.number_format((float) $this->to_price, 2);
        }

        return '$'.number_format((float) $this->price, 2);
    }
}
