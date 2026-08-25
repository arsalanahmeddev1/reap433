<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\QuizeCategoryResource;
use App\Models\QuizeCategory;
use Illuminate\Http\JsonResponse;

class QuizeCategoryController extends ApiController
{
    public function index(): JsonResponse
    {
        $categories = QuizeCategory::query()
            ->orderBy('title')
            ->get();

        return $this->success([
            'categories' => QuizeCategoryResource::collection($categories),
        ], 'Categories fetched successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $category = QuizeCategory::query()
            ->where('slug', $slug)
            ->first();

        if (! $category) {
            return $this->error('Category not found.', 404);
        }

        return $this->success([
            'category' => new QuizeCategoryResource($category),
        ], 'Category fetched successfully.');
    }
}
