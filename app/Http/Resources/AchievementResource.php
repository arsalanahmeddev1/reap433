<?php

namespace App\Http\Resources;

use App\Models\AchievementClaim;
use App\Models\UserAttemptQuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
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
            'xp' => $this->xp,
            'coins' => $this->coins,
            'status' => $this->status,
            'is_completed' => $this->isCompleted($request),
            'is_claim' => $request->user()
                ? AchievementClaim::query()
                    ->where('user_id', $request->user()->id)
                    ->where('achievement_id', $this->id)
                    ->exists()
                : false,
        ];
    }

    private function isCompleted(Request $request): int
    {
        if (! $request->user()) {
            return 0;
        }

        return match ($this->slug) {
            'first-quiz' => (int) UserAttemptQuestionAnswer::query()
                ->where('user_id', $request->user()->id)
                ->where('is_complete', 1)
                ->exists(),
            '7-day-streak' => (int) $this->hasDayStreak($request->user()->id, 7),
            'challenge-champion' => (int) $this->hasDayStreak($request->user()->id, 10),
            'master-of-scripture' => (int) (
                UserAttemptQuestionAnswer::query()
                    ->where('user_id', $request->user()->id)
                    ->where('is_complete', 1)
                    ->distinct()
                    ->count('quiz_category_id') >= 25
            ),
            'bible-scholar' => (int) $this->hasDeckAccuracy($request->user()->id, 90),
            default => 0,
        };
    }

    private function hasDeckAccuracy(int $userId, int $minimumPercent): bool
    {
        $attempts = UserAttemptQuestionAnswer::query()
            ->where('user_id', $userId)
            ->where('is_complete', 1)
            ->get(['quiz_category_id', 'quiz_type_id', 'answer_is_right']);

        if ($attempts->isEmpty()) {
            return false;
        }

        return $attempts
            ->groupBy(fn ($attempt) => $attempt->quiz_category_id.'-'.$attempt->quiz_type_id)
            ->contains(function ($deckAttempts) use ($minimumPercent) {
                $total = $deckAttempts->count();

                if ($total === 0) {
                    return false;
                }

                $correct = $deckAttempts->where('answer_is_right', 1)->count();
                $accuracyPercent = (int) round(($correct / $total) * 100);

                return $accuracyPercent >= $minimumPercent;
            });
    }

    private function hasDayStreak(int $userId, int $requiredDays): bool
    {
        $dates = UserAttemptQuestionAnswer::query()
            ->where('user_id', $userId)
            ->where('is_complete', 1)
            ->whereNotNull('quiz_category_id')
            ->selectRaw('DATE(created_at) as attempt_date')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('attempt_date')
            ->pluck('attempt_date')
            ->map(fn ($date) => (string) $date)
            ->values();

        if ($dates->count() < $requiredDays) {
            return false;
        }

        $streak = 1;

        for ($i = 1; $i < $dates->count(); $i++) {
            $previous = Carbon::parse($dates[$i - 1])->startOfDay();
            $current = Carbon::parse($dates[$i])->startOfDay();

            if ($previous->diffInDays($current) === 1) {
                $streak++;

                if ($streak >= $requiredDays) {
                    return true;
                }

                continue;
            }

            $streak = 1;
        }

        return false;
    }
}
