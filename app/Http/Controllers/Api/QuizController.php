<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\QuizQuestionResource;
use App\Models\QuizQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $quizzes = QuizQuestion::query()
            ->with(['category', 'type', 'answers'])
            ->when($request->filled('quiz_category_id'), function ($query) use ($request) {
                $query->where('quiz_category_id', $request->integer('quiz_category_id'));
            })
            ->when($request->filled('quiz_type_id'), function ($query) use ($request) {
                $query->where('quiz_type_id', $request->integer('quiz_type_id'));
            })
            ->orderBy('question')
            ->get();

        return $this->success([
            'total_questions' => $quizzes->count(),
            'questions' => QuizQuestionResource::collection($quizzes),
        ], 'Questions fetched successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $quiz = QuizQuestion::query()
            ->with(['category', 'type', 'answers'])
            ->where('slug', $slug)
            ->first();

        if (! $quiz) {
            return $this->error('Question not found.', 404);
        }

        return $this->success([
            'question' => new QuizQuestionResource($quiz),
        ], 'Question fetched successfully.');
    }
}
