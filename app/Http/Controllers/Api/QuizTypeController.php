<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\QuizTypeResource;
use App\Models\QuizCategoryType;
use App\Models\QuizType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizTypeController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $quizTypes = QuizType::query()
            ->when($request->filled('quiz_category_id'), function ($query) use ($request) {
                $quizTypeIds = QuizCategoryType::query()
                    ->where('quiz_category_id', $request->integer('quiz_category_id'))
                    ->select('quiz_type_id')
                    ->groupBy('quiz_type_id')
                    ->pluck('quiz_type_id');

                $query->whereIn('id', $quizTypeIds);
            })
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
