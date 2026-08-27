<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizType;
use App\Models\QuizeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuizeCategoryController extends Controller
{
    public function index(): View
    {
        $categories = QuizeCategory::query()
            ->with('quizTypes')
            ->orderBy('title')
            ->get();

        $quizTypes = QuizType::query()
            ->orderBy('title')
            ->get();

        return view('screens.admin.quiz-categories.index', compact('categories', 'quizTypes'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizeCategory::slugFromTitle($validated['title']);

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('quiz-categories', 'public');
        }

        $category = QuizeCategory::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'image_url' => $imagePath,
            'description' => $validated['description'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
        ]);

        $category->quizTypes()->sync($validated['quiz_type_ids']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz category created.'),
                'data' => $this->categoryPayload($category),
            ]);
        }

        return redirect()
            ->route('quiz-categories.index')
            ->with('success', __('Quiz category created.'));
    }

    public function update(Request $request, QuizeCategory $quizeCategory): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizeCategory::slugFromTitle($validated['title'], $quizeCategory->id);

        $imagePath = $quizeCategory->image_url;

        if ($request->boolean('remove_image') && $quizeCategory->image_url) {
            if (! preg_match('#^https?://#i', (string) $quizeCategory->image_url)) {
                Storage::disk('public')->delete($quizeCategory->image_url);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image_url')) {
            if ($quizeCategory->image_url && ! preg_match('#^https?://#i', (string) $quizeCategory->image_url)) {
                Storage::disk('public')->delete($quizeCategory->image_url);
            }
            $imagePath = $request->file('image_url')->store('quiz-categories', 'public');
        }

        $quizeCategory->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'image_url' => $imagePath,
            'description' => $validated['description'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
        ]);

        $quizeCategory->quizTypes()->sync($validated['quiz_type_ids']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz category updated.'),
                'data' => $this->categoryPayload($quizeCategory->fresh()),
            ]);
        }

        return redirect()
            ->route('quiz-categories.index')
            ->with('success', __('Quiz category updated.'));
    }

    public function destroy(Request $request, QuizeCategory $quizeCategory): JsonResponse|RedirectResponse
    {
        if ($quizeCategory->image_url && ! preg_match('#^https?://#i', (string) $quizeCategory->image_url)) {
            Storage::disk('public')->delete($quizeCategory->image_url);
        }

        $quizeCategory->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Quiz category deleted.')]);
        }

        return redirect()
            ->route('quiz-categories.index')
            ->with('success', __('Quiz category deleted.'));
    }

    /**
     * @return array{
     *     title: string,
     *     description: ?string,
     *     seo_title: ?string,
     *     seo_description: ?string,
     *     quiz_type_ids: array<int, int>
     * }
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'quiz_type_ids' => ['required', 'array', 'min:1'],
            'quiz_type_ids.*' => ['integer', 'exists:quiz_type,id'],
        ], [
            'image_url.max' => __('Quiz category image upload max size is 2MB.'),
            'quiz_type_ids.required' => __('Please select at least one difficulty.'),
            'quiz_type_ids.min' => __('Please select at least one difficulty.'),
        ]);

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'quiz_type_ids' => array_map('intval', $validated['quiz_type_ids']),
        ];
    }

    private function categoryPayload(QuizeCategory $category): array
    {
        $category->loadMissing('quizTypes');

        return [
            'id' => $category->id,
            'title' => $category->title,
            'slug' => $category->slug,
            'image_url' => $category->imageUrl(),
            'description' => $category->description,
            'seo_title' => $category->seo_title,
            'seo_description' => $category->seo_description,
            'quiz_type_ids' => $category->quizTypes->pluck('id')->values()->all(),
            'difficulty_titles' => $category->quizTypes->pluck('title')->implode(', '),
        ];
    }
}
