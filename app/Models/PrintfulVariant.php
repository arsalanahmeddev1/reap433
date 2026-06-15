<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintfulVariant extends Model
{
    protected $fillable = [
        'printful_product_id',
        'printful_variant_id',
        'external_id',
        'name',
        'sku',
        'retail_price',
        'currency',
        'thumbnail_url',
        'raw_data',
    ];

    protected $hidden = [
        'raw_data',
    ];

    protected function casts(): array
    {
        return [
            'printful_product_id' => 'integer',
            'printful_variant_id' => 'integer',
            'retail_price' => 'decimal:2',
            'raw_data' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PrintfulProduct::class, 'printful_product_id');
    }
}
