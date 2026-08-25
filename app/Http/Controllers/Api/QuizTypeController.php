<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\QuizTypeResource;
use App\Models\QuizType;
use Illuminate\Http\JsonResponse;

class QuizTypeController extends ApiController
{
    public function index(): JsonResponse
    {
        $quizTypes = QuizType::query()
            ->orderBy('title')
            ->get();

        return $this->success([
            'quiz_types' => QuizTypeResource::collection($quizTypes),
        ], 'Quiz types fetched successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $quizType = QuizType::query()
            ->where('slug', $slug)
            ->first();

        if (! $quizType) {
            return $this->error('Quiz type not found.', 404);
        }

        return $this->success([
            'quiz_type' => new QuizTypeResource($quizType),
        ], 'Quiz type fetched successfully.');
    }
}
