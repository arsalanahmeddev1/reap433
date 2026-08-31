<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CompleteQuizRequest;
use App\Http\Requests\Api\VerifyAnswerRequest;
use App\Http\Resources\QuizAnswerVerifyResource;
use App\Models\QuizAnswer;
use App\Models\UserAttemptQuestionAnswer;
use Illuminate\Http\JsonResponse;

class AnswerController extends ApiController
{
    public function verify(VerifyAnswerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $answer = QuizAnswer::query()
            ->with('question')
            ->where('id', $validated['answer_id'])
            ->where('question_id', $validated['question_id'])
            ->first();

        if (! $answer) {
            return $this->error('Answer not found for this question.', 404);
        }

        if (
            (int) $answer->question?->quiz_category_id !== (int) $validated['quiz_category_id']
            || (int) $answer->question?->quiz_type_id !== (int) $validated['quiz_type_id']
        ) {
            return $this->error('Question does not match the selected category or type.', 404);
        }

        $alreadySubmitted = UserAttemptQuestionAnswer::query()
            ->where('user_id', $request->user()->id)
            ->where('question_id', $validated['question_id'])
            ->where('is_complete', 0)
            ->exists();

        if ($alreadySubmitted) {
            return $this->error('Answer already submited.', 400);
        }

        UserAttemptQuestionAnswer::create([
            'user_id' => $request->user()->id,
            'quiz_category_id' => $validated['quiz_category_id'],
            'quiz_type_id' => $validated['quiz_type_id'],
            'question_id' => $validated['question_id'],
            'answer_id' => $validated['answer_id'],
            'answer_xp' => $answer->xp,
            'answer_coins' => $answer->coins,
            'answer_is_right' => $answer->is_right ? 1 : 0,
            'is_complete' => 0,
        ]);

        return $this->success([
            'answer' => new QuizAnswerVerifyResource($answer),
        ], 'Answer submitted.');
    }

    public function completeQuiz(CompleteQuizRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $attemptsQuery = UserAttemptQuestionAnswer::query()
            ->where('user_id', $request->user()->id)
            ->where('quiz_category_id', $validated['quiz_category_id'])
            ->where('quiz_type_id', $validated['quiz_type_id'])
            ->where('is_complete', 0);

        $totalQuestion = (clone $attemptsQuery)->count();

        if ($totalQuestion === 0) {
            return $this->error('No quiz attempts found to complete.', 404);
        }

        $answerXp = (int) (clone $attemptsQuery)->sum('answer_xp');
        $answerCoins = (int) (clone $attemptsQuery)->sum('answer_coins');
        $correctAnswers = (int) (clone $attemptsQuery)->where('answer_is_right', 1)->count();

        $accuracyPercent = (int) round(($correctAnswers / $totalQuestion) * 100);
        $score = $answerXp / $totalQuestion;

        $attemptsQuery->update(['is_complete' => 1]);

        return $this->success([
            'score' => $answerXp + '/' + $totalQuestion,
            'answer_xp' => $answerXp,
            'answer_coins' => $answerCoins,
            'total_question' => $totalQuestion,
            'accuracy_percent' => $accuracyPercent,
        ], 'Quiz completed.');
    }
}
