<?php

namespace App\Http\Controllers\Api;

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
        ], 'Answer verified successfully.');
    }
}
