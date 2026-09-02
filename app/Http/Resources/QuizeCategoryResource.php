<?php

namespace App\Http\Resources;

use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\UserAttemptQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizeCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $quizTypeId = $request->filled('quiz_type_id')
            ? $request->integer('quiz_type_id')
            : null;

        $totalQuestions = QuizQuestion::query()
            ->where('quiz_category_id', $this->id)
            ->when($quizTypeId, function ($query) use ($quizTypeId) {
                $query->where('quiz_type_id', $quizTypeId);
            })
            ->count();

        $reward = QuizAnswer::query()
            ->where('is_right', 1)
            ->whereHas('question', function ($query) use ($quizTypeId) {
                $query->where('quiz_category_id', $this->id)
                    ->when($quizTypeId, function ($q) use ($quizTypeId) {
                        $q->where('quiz_type_id', $quizTypeId);
                    });
            })
            ->selectRaw('COALESCE(SUM(xp), 0) as xp, COALESCE(SUM(coins), 0) as coins')
            ->first();

        $rewardXp = (int) ($reward->xp ?? 0);
        $rewardCoins = (int) ($reward->coins ?? 0);

        $completedQuestions = 0;

        if ($request->user()) {
            $completedQuestions = UserAttemptQuestionAnswer::query()
                ->where('user_id', $request->user()->id)
                ->where('quiz_category_id', $this->id)
                ->where('is_complete', 0)
                ->when($quizTypeId, function ($query) use ($quizTypeId) {
                    $query->where('quiz_type_id', $quizTypeId);
                })
                ->distinct()
                ->count('question_id');
        }

        $progressPercent = $totalQuestions > 0
            ? (int) round(($completedQuestions / $totalQuestions) * 100)
            : 0;

        $lastQuestion = null;

        if ($request->user()) {
            $lastAttempt = UserAttemptQuestionAnswer::query()
                ->with('question')
                ->where('user_id', $request->user()->id)
                ->where('quiz_category_id', $this->id)
                ->when($quizTypeId, function ($query) use ($quizTypeId) {
                    $query->where('quiz_type_id', $quizTypeId);
                })
                ->latest()
                ->first();

            $lastQuestion = $lastAttempt?->question;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image_url' => $this->imageUrl()
                ?? asset('assets/images/placeholders/img-not-available.png'),
            'description' => $this->description,
            'total_questions' => $totalQuestions,
            'reward' => [
                'xp' => $rewardXp,
                'coins' => $rewardCoins,
            ],
            'difficulty' => $this->whenLoaded('quizTypes', fn () => $this->quizTypes->map(fn ($type) => [
                'id' => $type->id,
                'title' => $type->title,
                'slug' => $type->slug,
                'slogan_text' => $type->slogan_text,
            ])->values()),
            'progress' => [
                'completed_questions' => $completedQuestions,
                'progress_percent' => $progressPercent,
            ],
            'last_question' => $lastQuestion ? [
                'id' => $lastQuestion->id,
                'title' => $lastQuestion->question,
                'slug' => $lastQuestion->slug,
            ] : null,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
        ];
    }
}
