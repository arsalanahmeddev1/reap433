<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CollectionPage extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function slugFromTitle(string $title, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $title, $ignoreId);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('public')->url($this->image);
    }
}
