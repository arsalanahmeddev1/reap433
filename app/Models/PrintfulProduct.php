<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrintfulProduct extends Model
{
    protected $fillable = [
        'printful_product_id',
        'external_id',
        'name',
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

    public function variants(): HasMany
    {
        return $this->hasMany(PrintfulVariant::class);
    }
}
