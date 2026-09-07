<?php

namespace App\Http\Resources;

use App\Models\UserAttemptQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRankingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dateFrom = match ($request->input('filter')) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => null,
        };

        $totals = UserAttemptQuestionAnswer::query()
            ->where('user_id', $this->id)
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
            })
            ->selectRaw('COALESCE(SUM(answer_xp), 0) as total_xp, COALESCE(SUM(answer_coins), 0) as total_coins')
            ->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_image' => $this->profileImageUrl()
                ?? asset('assets/admin/images/user/user.png'),
            'rank' => (int) ($this->rank ?? 0),
            'total_xp' => (int) ($totals->total_xp ?? 0),
            'total_coins' => (int) ($totals->total_coins ?? 0),
            'provider' => $this->provider,
            'current_user' => (int) $this->id === (int) ($request->user()?->id ?? 0),
            'total_streak' => UserAttemptQuestionAnswer::query()
                ->where('user_id', $this->id)
                ->where('is_complete', 1)
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                })
                ->distinct()
                ->count('quiz_category_id'),
        ];
    }
}
