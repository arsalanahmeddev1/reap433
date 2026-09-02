<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;

    protected $table = 'achievement';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'xp' => 'integer',
            'coins' => 'integer',
        ];
    }

    public static function slugFromTitle(string $title, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $title, $ignoreId);
    }

    public function imageUrl(): ?string
    {
        $raw = trim((string) $this->image_url);
        if ($raw === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }

        return asset('storage/'.str_replace('\\', '/', ltrim($raw, '/')));
    }
}
