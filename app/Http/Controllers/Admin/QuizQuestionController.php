<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use App\Models\QuizType;
use App\Models\QuizeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizQuestionController extends Controller
{
    public function index(): View
    {
        $quizQuestions = QuizQuestion::query()
            ->with(['category', 'type'])
            ->orderByDesc('id')
            ->get();

        $categories = QuizeCategory::query()->orderBy('title')->get();
        $quizTypes = QuizType::query()->orderBy('title')->get();

        return view('screens.admin.quiz-questions.index', compact('quizQuestions', 'categories', 'quizTypes'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizQuestion::slugFromQuestion($validated['question']);

        $quizQuestion = QuizQuestion::create([
            'quiz_category_id' => $validated['quiz_category_id'],
            'quiz_type_id' => $validated['quiz_type_id'],
            'question' => $validated['question'],
            'slug' => $slug,
            'description' => $validated['description'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz question created.'),
                'data' => $this->quizQuestionPayload($quizQuestion->load(['category', 'type'])),
            ]);
        }

        return redirect()
            ->route('quiz-questions.index')
            ->with('success', __('Quiz question created.'));
    }

    public function update(Request $request, QuizQuestion $quizQuestion): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizQuestion::slugFromQuestion($validated['question'], $quizQuestion->id);

        $quizQuestion->update([
            'quiz_category_id' => $validated['quiz_category_id'],
            'quiz_type_id' => $validated['quiz_type_id'],
            'question' => $validated['question'],
            'slug' => $slug,
            'description' => $validated['description'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz question updated.'),
                'data' => $this->quizQuestionPayload($quizQuestion->fresh()->load(['category', 'type'])),
            ]);
        }

        return redirect()
            ->route('quiz-questions.index')
            ->with('success', __('Quiz question updated.'));
    }

    public function destroy(Request $request, QuizQuestion $quizQuestion): JsonResponse|RedirectResponse
    {
        $quizQuestion->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Quiz question deleted.')]);
        }

        return redirect()
            ->route('quiz-questions.index')
            ->with('success', __('Quiz question deleted.'));
    }

    /**
     * @return array{quiz_category_id: int, quiz_type_id: int, question: string, description: ?string, seo_title: ?string, seo_description: ?string}
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'quiz_category_id' => ['required', 'integer', 'exists:quize_categories,id'],
            'quiz_type_id' => ['required', 'integer', 'exists:quiz_type,id'],
            'question' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);

        return [
            'quiz_category_id' => (int) $validated['quiz_category_id'],
            'quiz_type_id' => (int) $validated['quiz_type_id'],
            'question' => $validated['question'],
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ];
    }

    private function quizQuestionPayload(QuizQuestion $quizQuestion): array
    {
        return [
            'id' => $quizQuestion->id,
            'quiz_category_id' => $quizQuestion->quiz_category_id,
            'quiz_type_id' => $quizQuestion->quiz_type_id,
            'category_title' => $quizQuestion->category?->title,
            'type_title' => $quizQuestion->type?->title,
            'question' => $quizQuestion->question,
            'slug' => $quizQuestion->slug,
            'description' => $quizQuestion->description,
            'seo_title' => $quizQuestion->seo_title,
            'seo_description' => $quizQuestion->seo_description,
        ];
    }
}
