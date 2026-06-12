<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class ProductPublicImage
{
    /**
     * Store a product image on the public disk (products/… → /storage/products/…).
     */
    public static function store(UploadedFile $file): string
    {
        return $file->store('products', 'public');
    }
}
