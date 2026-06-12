<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    protected $guarded = ['id'];

    protected $table = 'product_categories';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public static function slugFromName(string $name, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $name, $ignoreId);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('public')->url($this->image);
    }
}
