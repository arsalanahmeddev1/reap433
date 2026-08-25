<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAnswer extends Model
{
    use SoftDeletes;

    protected $table = 'quiz_answers';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_right' => 'boolean',
            'xp' => 'integer',
            'coins' => 'integer',
        ];
    }

    public static function slugFromAnswer(string $answer, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $answer, $ignoreId);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
