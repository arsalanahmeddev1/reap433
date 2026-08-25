<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'quiz_question';

    protected $guarded = ['id'];

    public static function slugFromQuestion(string $question, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $question, $ignoreId);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizeCategory::class, 'quiz_category_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(QuizType::class, 'quiz_type_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }
}
