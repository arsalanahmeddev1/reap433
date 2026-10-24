<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UniqueSlug
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function generate(string $modelClass, string $column, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim($source));
        $base = $base !== '' ? $base : 'item';

        // MySQL unique indexes still apply to soft-deleted rows; keep slugs short enough for suffixes.
        $base = Str::limit($base, 240, '');

        $slug = $base;
        $suffix = 1;

        while (static::exists($modelClass, $column, $slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected static function exists(string $modelClass, string $column, string $slug, ?int $ignoreId): bool
    {
        $query = $modelClass::query()->where($column, $slug);

        // Soft-deleted rows still occupy unique DB indexes.
        if (in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            $query->withTrashed();
        }

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
