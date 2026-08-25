<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerifyAnswerRequest;
use App\Http\Resources\QuizAnswerVerifyResource;
use App\Models\QuizAnswer;
use Illuminate\Http\JsonResponse;

class AnswerController extends ApiController
{
    public function verify(VerifyAnswerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $answer = QuizAnswer::query()
            ->where('id', $validated['answer_id'])
            ->where('question_id', $validated['question_id'])
            ->first();

        if (! $answer) {
            return $this->error('Answer not found for this question.', 404);
        }

        return $this->success([
            'answer' => new QuizAnswerVerifyResource($answer),
        ], 'Answer verified successfully.');
    }
}
