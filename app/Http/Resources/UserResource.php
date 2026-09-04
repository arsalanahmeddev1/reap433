<?php

namespace App\Http\Resources;

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

        $dailyChallenge = Cache::remember(
            'daily_challenge_user_'.$this->id,
            now()->addDay(),
            function () {
                $question = QuizQuestion::query()
                    ->inRandomOrder()
                    ->first(['quiz_category_id', 'quiz_type_id']);

                if (! $question) {
                    return null;
                }

                return [
                    'quiz_category_id' => (int) $question->quiz_category_id,
                    'quiz_type_id' => (int) $question->quiz_type_id,
                ];
            }
        );

        if ($dailyChallenge) {
            $dailyChallenge['is_played'] = UserAttemptQuestionAnswer::query()
                ->where('user_id', $this->id)
                ->where('quiz_category_id', $dailyChallenge['quiz_category_id'])
                ->where('quiz_type_id', $dailyChallenge['quiz_type_id'])
                ->whereDate('created_at', today())
                ->exists();
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
