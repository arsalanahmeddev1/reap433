<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintfulCartItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'variant_id' => 'integer',
            'printful_variant_id' => 'integer',
            'printful_product_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lineTotal(): float
    {
        return round((float) $this->price * (int) $this->quantity, 2);
    }
}
