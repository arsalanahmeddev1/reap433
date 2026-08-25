<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quiz_category_id' => $this->quiz_category_id,
            'quiz_type_id' => $this->quiz_type_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'title' => $this->category?->title,
                'slug' => $this->category?->slug,
            ]),
            'type' => $this->whenLoaded('type', fn () => [
                'id' => $this->type?->id,
                'title' => $this->type?->title,
                'slug' => $this->type?->slug,
            ]),
            'question' => $this->question,
            'slug' => $this->slug,
            'description' => $this->description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'options' => QuizAnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
