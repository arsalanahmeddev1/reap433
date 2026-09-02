<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ClaimAchievementRequest;
use App\Http\Resources\AchievementResource;
use App\Models\Achievement;
use App\Models\AchievementClaim;
use Illuminate\Http\JsonResponse;

class AchievementController extends ApiController
{
    public function index(): JsonResponse
    {
        $achievements = Achievement::query()
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        return $this->success([
            'achievements' => AchievementResource::collection($achievements),
        ], 'Achievements fetched successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $achievement = Achievement::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (! $achievement) {
            return $this->error('Achievement not found.', 404);
        }

        return $this->success([
            'achievement' => new AchievementResource($achievement),
        ], 'Achievement fetched successfully.');
    }

    public function claim(ClaimAchievementRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $achievement = Achievement::query()
            ->where('id', $validated['achievement_id'])
            ->where('status', 'active')
            ->first();

        if (! $achievement) {
            return $this->error('Achievement not found.', 404);
        }

        $alreadyClaimed = AchievementClaim::query()
            ->where('user_id', $request->user()->id)
            ->where('achievement_id', $validated['achievement_id'])
            ->exists();

        if ($alreadyClaimed) {
            return $this->error('Achievement already Achieve', 400);
        }

        $claim = AchievementClaim::create([
            'user_id' => $request->user()->id,
            'achievement_id' => $validated['achievement_id'],
            'status' => 'active',
        ]);

        return $this->success([
            'claim' => [
                'id' => $claim->id,
                'user_id' => $claim->user_id,
                'achievement_id' => $claim->achievement_id,
                'status' => $claim->status,
            ],
        ], 'Achievement claimed successfully.');
    }
}
