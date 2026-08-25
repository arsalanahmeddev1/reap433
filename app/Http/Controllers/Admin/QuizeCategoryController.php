<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            ->orderBy('title')
            ->get();

        return view('screens.admin.quiz-categories.index', compact('categories'));
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
            'estimated_time' => $validated['estimated_time'],
            'difficulty' => $validated['difficulty'],
            'best_score' => $validated['best_score'],
            'xp' => $validated['xp'],
            'coins' => $validated['coins'],
        ]);

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
            'estimated_time' => $validated['estimated_time'],
            'difficulty' => $validated['difficulty'],
            'best_score' => $validated['best_score'],
            'xp' => $validated['xp'],
            'coins' => $validated['coins'],
        ]);

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
     *     estimated_time: ?string,
     *     difficulty: ?string,
     *     best_score: ?int,
     *     xp: ?int,
     *     coins: ?int
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
            'estimated_time' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['nullable', 'string', 'in:Easy,Hard'],
            'best_score' => ['nullable', 'integer', 'min:0'],
            'xp' => ['nullable', 'integer', 'min:0'],
            'coins' => ['nullable', 'integer', 'min:0'],
        ], [
            'image_url.max' => __('Quiz category image upload max size is 2MB.'),
        ]);

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'estimated_time' => $validated['estimated_time'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'best_score' => isset($validated['best_score']) ? (int) $validated['best_score'] : null,
            'xp' => isset($validated['xp']) ? (int) $validated['xp'] : null,
            'coins' => isset($validated['coins']) ? (int) $validated['coins'] : null,
        ];
    }

    private function categoryPayload(QuizeCategory $category): array
    {
        return [
            'id' => $category->id,
            'title' => $category->title,
            'slug' => $category->slug,
            'image_url' => $category->imageUrl(),
            'description' => $category->description,
            'seo_title' => $category->seo_title,
            'seo_description' => $category->seo_description,
            'estimated_time' => $category->estimated_time,
            'difficulty' => $category->difficulty,
            'best_score' => $category->best_score,
            'xp' => $category->xp,
            'coins' => $category->coins,
        ];
    }
}
