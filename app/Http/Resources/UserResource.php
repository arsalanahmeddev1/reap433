<?php

namespace App\Http\Resources;

use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\UserAttemptQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totals = UserAttemptQuestionAnswer::query()
            ->where('user_id', $this->id)
            ->selectRaw('COALESCE(SUM(answer_xp), 0) as total_xp, COALESCE(SUM(answer_coins), 0) as total_coins')
            ->first();

        $cacheKey = 'daily_challenge_user_'.$this->id;
        $dailyChallenge = Cache::get($cacheKey);

        if (! is_array($dailyChallenge) || empty($dailyChallenge['resets_at'])) {
            $question = QuizQuestion::query()
                ->inRandomOrder()
                ->first(['id', 'quiz_category_id', 'quiz_type_id']);

            if ($question) {
                $answerTotals = QuizAnswer::query()
                    ->where('question_id', $question->id)
                    ->selectRaw('COALESCE(SUM(xp), 0) as total_xp, COALESCE(SUM(coins), 0) as total_coins')
                    ->first();

                $resetsAt = now()->addDay();

                $dailyChallenge = [
                    'quiz_category_id' => (int) $question->quiz_category_id,
                    'quiz_type_id' => (int) $question->quiz_type_id,
                    'total_xp' => (int) ($answerTotals->total_xp ?? 0),
                    'total_coins' => (int) ($answerTotals->total_coins ?? 0),
                    'resets_at' => $resetsAt->getTimestamp(),
                ];

                Cache::put($cacheKey, $dailyChallenge, $resetsAt);
            } else {
                $dailyChallenge = null;
            }
        }

        if ($dailyChallenge) {
            $remainingSeconds = max(0, (int) $dailyChallenge['resets_at'] - now()->getTimestamp());

            $responseChallenge = [
                'quiz_category_id' => (int) $dailyChallenge['quiz_category_id'],
                'quiz_type_id' => (int) $dailyChallenge['quiz_type_id'],
                'total_xp' => (int) $dailyChallenge['total_xp'],
                'total_coins' => (int) $dailyChallenge['total_coins'],
                'challenge_reset' => sprintf(
                    '%02d:%02d:%02d',
                    intdiv($remainingSeconds, 3600),
                    intdiv($remainingSeconds % 3600, 60),
                    $remainingSeconds % 60
                ),
                'is_played' => UserAttemptQuestionAnswer::query()
                    ->where('user_id', $this->id)
                    ->where('quiz_category_id', $dailyChallenge['quiz_category_id'])
                    ->where('quiz_type_id', $dailyChallenge['quiz_type_id'])
                    ->where('is_daily_challenge', '1')
                    ->whereDate('created_at', today())
                    ->exists(),
            ];

            $dailyChallenge = $responseChallenge;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_image' => $this->profileImageUrl()
                ?? asset('assets/admin/images/user/user.png'),
            'role' => $this->role,
            'total_xp' => (int) ($totals->total_xp ?? 0),
            'total_coins' => (int) ($totals->total_coins ?? 0),
            'provider' => $this->provider,
            'total_streak' => UserAttemptQuestionAnswer::query()
                ->where('user_id', $this->id)
                ->where('is_complete', 1)
                ->distinct()
                ->count('quiz_category_id'),
            'daily_challenge' => $dailyChallenge,
        ];
    }
}
