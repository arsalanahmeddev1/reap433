<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAnswerVerifyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'slug' => $this->slug,
            'answers' => $this->answers,
            'bible_title' => $this->bible_title,
            'description' => $this->description,
            'xp' => $this->xp,
            'coins' => $this->coins,
            'is_right' => $this->is_right ? 1 : 0,
        ];
    }
}
