<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    protected $guarded = ['id'];

    public static function current(): self
    {
        $sitemap = static::query()->first();

        if ($sitemap) {
            return $sitemap;
        }

        return static::query()->create([
            'content' => null,
        ]);
    }

    public function xmlContent(): ?string
    {
        $content = trim((string) $this->content);

        return $content !== '' ? $content : null;
    }
}
