<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizCategoryType extends Model
{
    public $timestamps = false;

    protected $table = 'quiz_category_type';

    protected $guarded = ['id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizeCategory::class, 'quiz_category_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(QuizType::class, 'quiz_type_id');
    }
}
