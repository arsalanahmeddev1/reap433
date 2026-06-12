<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(ProductAttributeItem::class, 'product_attribute_id');
    }
}
