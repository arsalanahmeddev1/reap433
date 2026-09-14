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
        // Date-only schedule: compare calendar dates, not clock time.
        // App timezone is UTC, while the admin date picker uses the browser's
        // local "today" — allow up to +14h so a post dated "today" locally
        // is not hidden for the rest of the UTC previous day.
        $today = now()->copy()->utc()->addHours(14)->toDateString();

        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->whereDate('published_at', '<=', $today);
    }

    /** Whether this post is visible on the public site right now. */
    public function isLive(): bool
    {
        if (! $this->is_published || $this->published_at === null) {
            return false;
        }

        $today = now()->copy()->utc()->addHours(14)->toDateString();

        return $this->published_at->toDateString() <= $today;
    }

    /** Admin list status: draft | scheduled | published */
    public function publishStatus(): string
    {
        if (! $this->is_published || $this->published_at === null) {
            return 'draft';
        }

        return $this->isLive() ? 'published' : 'scheduled';
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
