<?php

namespace App\Http\Controllers\Api;

use App\Models\QuizQuestion;
use App\Models\QuizeCategory;
use App\Models\UserAttemptQuestionAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $answered = UserAttemptQuestionAnswer::query()
            ->where('user_id', $request->user()->id)
            ->distinct()
            ->count('question_id');

        $total = QuizQuestion::query()->count();

        $completedDecks = UserAttemptQuestionAnswer::query()
            ->where('user_id', $request->user()->id)
            ->where('is_complete', 1)
            ->distinct()
            ->count('quiz_category_id');

        $totalDecks = QuizeCategory::query()->count();

        $attemptsQuery = UserAttemptQuestionAnswer::query()
            ->where('user_id', $request->user()->id);

        $totalAttempts = (clone $attemptsQuery)->count();
        $correctAnswers = (int) (clone $attemptsQuery)->where('answer_is_right', 1)->count();

        $averageScorePercent = $totalAttempts > 0
            ? (int) round(($correctAnswers / $totalAttempts) * 100)
            : 0;

        $overallProgressPercent = $total > 0
            ? (int) round(($answered / $total) * 100)
            : 0;

        return $this->success([
            'overall_progress_percent' => $overallProgressPercent,
            'questions_answered' => [
                'answered' => $answered,
                'total' => $total,
            ],
            'decks_complete' => [
                'completed' => $completedDecks,
                'total' => $totalDecks,
            ],
            'average_score_percent' => $averageScorePercent,
        ], 'Progress fetched successfully.');
    }
}
