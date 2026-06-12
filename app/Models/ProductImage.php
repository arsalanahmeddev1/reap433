<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::deleting(function (ProductImage $productImage) {
            $raw = trim((string) $productImage->image);
            if ($raw === '' || preg_match('#^https?://#i', $raw)) {
                return;
            }
            $path = str_replace('\\', '/', ltrim($raw, '/'));
            if (str_starts_with($path, 'uploads/')) {
                $full = public_path($path);
                if (is_file($full)) {
                    @unlink($full);
                }

                return;
            }
            Storage::disk('public')->delete($path);
        });
    }

    /**
     * Public URL: legacy `uploads/…` under `public/`; new rows use `public` disk paths (`products/…` → /storage/…).
     */
    public function publicUrl(): string
    {
        $raw = trim((string) $this->image);
        if ($raw === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }
        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if ($path === '') {
            return '';
        }
        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        return '/storage/'.$path;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttributeItem()
    {
        return $this->belongsTo(ProductAttributeItem::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
