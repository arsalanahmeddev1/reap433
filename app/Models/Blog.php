<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Blog extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public static function slugFromTitle(string $title, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $title, $ignoreId);
    }

    public function featuredImageUrl(): ?string
    {
        $raw = trim((string) $this->featured_image);
        if ($raw === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }
        $path = str_replace('\\', '/', ltrim($raw, '/'));
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        return '/storage/'.$path;
    }
}
