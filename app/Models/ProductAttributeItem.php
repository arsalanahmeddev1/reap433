<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeItem extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class, 'product_attribute_item_id');
    }
}
