<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAttemptQuestionAnswer extends Model
{
    use SoftDeletes;

    protected $table = 'user_attempt_question_answer';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'answer_xp' => 'integer',
            'answer_coins' => 'integer',
            'answer_is_right' => 'boolean',
            'is_complete' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizeCategory::class, 'quiz_category_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(QuizType::class, 'quiz_type_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(QuizAnswer::class, 'answer_id');
    }
}
