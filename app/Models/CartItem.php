<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->qty;
    }
}
