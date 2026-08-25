<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuizTypeController extends Controller
{
    public function index(): View
    {
        $quizTypes = QuizType::query()
            ->orderBy('title')
            ->get();

        return view('screens.admin.quiz-types.index', compact('quizTypes'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizType::slugFromTitle($validated['title']);

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('quiz-types', 'public');
        }

        $quizType = QuizType::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'image_url' => $imagePath,
            'slogan_text' => $validated['slogan_text'],
            'description' => $validated['description'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz type created.'),
                'data' => $this->quizTypePayload($quizType),
            ]);
        }

        return redirect()
            ->route('quiz-types.index')
            ->with('success', __('Quiz type created.'));
    }

    public function update(Request $request, QuizType $quizType): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizType::slugFromTitle($validated['title'], $quizType->id);

        $imagePath = $quizType->image_url;

        if ($request->boolean('remove_image') && $quizType->image_url) {
            if (! preg_match('#^https?://#i', (string) $quizType->image_url)) {
                Storage::disk('public')->delete($quizType->image_url);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image_url')) {
            if ($quizType->image_url && ! preg_match('#^https?://#i', (string) $quizType->image_url)) {
                Storage::disk('public')->delete($quizType->image_url);
            }
            $imagePath = $request->file('image_url')->store('quiz-types', 'public');
        }

        $quizType->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'image_url' => $imagePath,
            'slogan_text' => $validated['slogan_text'],
            'description' => $validated['description'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz type updated.'),
                'data' => $this->quizTypePayload($quizType->fresh()),
            ]);
        }

        return redirect()
            ->route('quiz-types.index')
            ->with('success', __('Quiz type updated.'));
    }

    public function destroy(Request $request, QuizType $quizType): JsonResponse|RedirectResponse
    {
        if ($quizType->image_url && ! preg_match('#^https?://#i', (string) $quizType->image_url)) {
            Storage::disk('public')->delete($quizType->image_url);
        }

        $quizType->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Quiz type deleted.')]);
        }

        return redirect()
            ->route('quiz-types.index')
            ->with('success', __('Quiz type deleted.'));
    }

    /**
     * @return array{title: string, slogan_text: ?string, description: ?string, seo_title: ?string, seo_description: ?string}
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slogan_text' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ], [
            'image_url.max' => __('Quiz type image upload max size is 2MB.'),
        ]);

        return [
            'title' => $validated['title'],
            'slogan_text' => $validated['slogan_text'] ?? null,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ];
    }

    private function quizTypePayload(QuizType $quizType): array
    {
        return [
            'id' => $quizType->id,
            'title' => $quizType->title,
            'slug' => $quizType->slug,
            'image_url' => $quizType->imageUrl(),
            'slogan_text' => $quizType->slogan_text,
            'description' => $quizType->description,
            'seo_title' => $quizType->seo_title,
            'seo_description' => $quizType->seo_description,
        ];
    }
}
