<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrintfulProduct extends Model
{
    protected $fillable = [
        'printful_product_id',
        'external_id',
        'name',
        'category_name',
        'category_id',
        'thumbnail_url',
        'is_synced',
        'raw_data',
    ];

    protected $hidden = [
        'raw_data',
    ];

    protected function casts(): array
    {
        return [
            'printful_product_id' => 'integer',
            'is_synced' => 'boolean',
            'raw_data' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(PrintfulVariant::class);
    }

    public function favouriteRecords(): HasMany
    {
        return $this->hasMany(FavouriteProduct::class, 'product_id');
    }

    public function favouritedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'favourite_products',
            'product_id',
            'user_id'
        )->withTimestamps()->whereNull('favourite_products.deleted_at');
    }
}
