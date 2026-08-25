<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizeCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image_url' => $this->imageUrl()
                ?? asset('assets/images/placeholders/img-not-available.png'),
            'description' => $this->description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'estimated_time' => $this->estimated_time,
            'difficulty' => $this->difficulty,
            'best_score' => $this->best_score,
            'xp' => $this->xp,
            'coins' => $this->coins,
        ];
    }
}
