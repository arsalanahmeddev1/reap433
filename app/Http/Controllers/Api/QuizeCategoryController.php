<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\QuizeCategoryResource;
use App\Models\QuizeCategory;
use App\Models\UserAttemptQuestionAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizeCategoryController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $categories = QuizeCategory::query()
            ->with('quizTypes')
            ->when($request->filled('quiz_type_id'), function ($query) use ($request) {
                $query->whereHas('quizTypes', function ($q) use ($request) {
                    $q->where('quiz_type.id', $request->integer('quiz_type_id'));
                });
            })
            ->orderByDesc('id')
            ->get();

        return $this->success([
            'categories' => QuizeCategoryResource::collection($categories),
        ], 'Categories fetched successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $category = QuizeCategory::query()
            ->with('quizTypes')
            ->where('slug', $slug)
            ->first();

        if (! $category) {
            return $this->error('Category not found.', 404);
        }

        return $this->success([
            'category' => new QuizeCategoryResource($category),
        ], 'Category fetched successfully.');
    }

    public function continueQuizCategory(Request $request): JsonResponse
    {
        $categoryIds = UserAttemptQuestionAnswer::query()
            ->where('user_id', $request->user()->id)
            ->where('is_complete', 0)
            ->distinct()
            ->pluck('quiz_category_id');

        $categories = QuizeCategory::query()
            ->with('quizTypes')
            ->whereIn('id', $categoryIds)
            ->orderByDesc('id')
            ->get();

        return $this->success([
            'categories' => QuizeCategoryResource::collection($categories),
        ], 'Continue quiz categories fetched successfully.');
    }
}
