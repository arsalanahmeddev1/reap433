<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizAnswerController extends Controller
{
    public function index(): View
    {
        $quizAnswers = QuizAnswer::query()
            ->with('question')
            ->orderByDesc('id')
            ->get();

        $categories = QuizeCategory::query()
            ->with('quizTypes')
            ->orderBy('title')
            ->get();

        $questions = QuizQuestion::query()
            ->orderBy('question')
            ->get(['id', 'question', 'quiz_category_id', 'quiz_type_id']);

        return view('screens.admin.quiz-answers.index', compact('quizAnswers', 'categories', 'questions'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizAnswer::slugFromAnswer($validated['answers']);

        $quizAnswer = QuizAnswer::create([
            'question_id' => $validated['question_id'],
            'slug' => $slug,
            'answers' => $validated['answers'],
            'bible_title' => $validated['bible_title'],
            'description' => $validated['description'],
            'xp' => $validated['xp'],
            'coins' => $validated['coins'],
            'is_right' => $validated['is_right'],
        ]);

        if ($validated['is_right']) {
            $this->clearOtherRightAnswers($quizAnswer);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz answer created.'),
                'data' => $this->quizAnswerPayload($quizAnswer->load('question')),
            ]);
        }

        return redirect()
            ->route('quiz-answers.index')
            ->with('success', __('Quiz answer created.'));
    }

    public function update(Request $request, QuizAnswer $quizAnswer): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = QuizAnswer::slugFromAnswer($validated['answers'], $quizAnswer->id);

        $quizAnswer->update([
            'question_id' => $validated['question_id'],
            'slug' => $slug,
            'answers' => $validated['answers'],
            'bible_title' => $validated['bible_title'],
            'description' => $validated['description'],
            'xp' => $validated['xp'],
            'coins' => $validated['coins'],
            'is_right' => $validated['is_right'],
        ]);

        if ($validated['is_right']) {
            $this->clearOtherRightAnswers($quizAnswer);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Quiz answer updated.'),
                'data' => $this->quizAnswerPayload($quizAnswer->fresh()->load('question')),
            ]);
        }

        return redirect()
            ->route('quiz-answers.index')
            ->with('success', __('Quiz answer updated.'));
    }

    public function destroy(Request $request, QuizAnswer $quizAnswer): JsonResponse|RedirectResponse
    {
        $quizAnswer->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Quiz answer deleted.')]);
        }

        return redirect()
            ->route('quiz-answers.index')
            ->with('success', __('Quiz answer deleted.'));
    }

    /**
     * @return array{question_id: int, answers: string, bible_title: ?string, description: ?string, xp: ?int, coins: ?int, is_right: int}
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:quiz_question,id'],
            'answers' => ['required', 'string', 'max:255'],
            'bible_title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'xp' => ['nullable', 'integer', 'min:0'],
            'coins' => ['nullable', 'integer', 'min:0'],
            'is_right' => ['required', 'in:0,1'],
        ]);

        return [
            'question_id' => (int) $validated['question_id'],
            'answers' => $validated['answers'],
            'bible_title' => $validated['bible_title'] ?? null,
            'description' => $validated['description'] ?? null,
            'xp' => isset($validated['xp']) ? (int) $validated['xp'] : null,
            'coins' => isset($validated['coins']) ? (int) $validated['coins'] : null,
            'is_right' => (int) $validated['is_right'],
        ];
    }

    private function clearOtherRightAnswers(QuizAnswer $quizAnswer): void
    {
        QuizAnswer::query()
            ->where('question_id', $quizAnswer->question_id)
            ->where('id', '!=', $quizAnswer->id)
            ->update(['is_right' => 0]);
    }

    private function quizAnswerPayload(QuizAnswer $quizAnswer): array
    {
        $quizAnswer->loadMissing('question');

        return [
            'id' => $quizAnswer->id,
            'question_id' => $quizAnswer->question_id,
            'quiz_category_id' => $quizAnswer->question?->quiz_category_id,
            'quiz_type_id' => $quizAnswer->question?->quiz_type_id,
            'question_text' => $quizAnswer->question?->question,
            'slug' => $quizAnswer->slug,
            'answers' => $quizAnswer->answers,
            'bible_title' => $quizAnswer->bible_title,
            'description' => $quizAnswer->description,
            'xp' => $quizAnswer->xp,
            'coins' => $quizAnswer->coins,
            'is_right' => $quizAnswer->is_right ? 1 : 0,
        ];
    }
}
