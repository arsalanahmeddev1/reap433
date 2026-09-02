<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        $achievements = Achievement::query()
            ->orderBy('title')
            ->get();

        return view('screens.admin.achievements.index', compact('achievements'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = Achievement::slugFromTitle($validated['title']);

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('achievements', 'public');
        }

        $achievement = Achievement::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'xp' => $validated['xp'],
            'coins' => $validated['coins'],
            'image_url' => $imagePath,
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Achievement created.'),
                'data' => $this->achievementPayload($achievement),
            ]);
        }

        return redirect()
            ->route('achievements.index')
            ->with('success', __('Achievement created.'));
    }

    public function update(Request $request, Achievement $achievement): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $slug = Achievement::slugFromTitle($validated['title'], $achievement->id);

        $imagePath = $achievement->image_url;

        if ($request->boolean('remove_image') && $achievement->image_url) {
            if (! preg_match('#^https?://#i', (string) $achievement->image_url)) {
                Storage::disk('public')->delete($achievement->image_url);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image_url')) {
            if ($achievement->image_url && ! preg_match('#^https?://#i', (string) $achievement->image_url)) {
                Storage::disk('public')->delete($achievement->image_url);
            }
            $imagePath = $request->file('image_url')->store('achievements', 'public');
        }

        $achievement->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'xp' => $validated['xp'],
            'coins' => $validated['coins'],
            'image_url' => $imagePath,
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Achievement updated.'),
                'data' => $this->achievementPayload($achievement->fresh()),
            ]);
        }

        return redirect()
            ->route('achievements.index')
            ->with('success', __('Achievement updated.'));
    }

    public function destroy(Request $request, Achievement $achievement): JsonResponse|RedirectResponse
    {
        if ($achievement->image_url && ! preg_match('#^https?://#i', (string) $achievement->image_url)) {
            Storage::disk('public')->delete($achievement->image_url);
        }

        $achievement->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Achievement deleted.')]);
        }

        return redirect()
            ->route('achievements.index')
            ->with('success', __('Achievement deleted.'));
    }

    /**
     * @return array{title: string, xp: int, coins: int, description: ?string, status: string}
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'xp' => ['required', 'integer', 'min:0'],
            'coins' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'image_url.max' => __('Achievement image upload max size is 2MB.'),
        ]);

        return [
            'title' => $validated['title'],
            'xp' => (int) $validated['xp'],
            'coins' => (int) $validated['coins'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ];
    }

    private function achievementPayload(Achievement $achievement): array
    {
        return [
            'id' => $achievement->id,
            'title' => $achievement->title,
            'slug' => $achievement->slug,
            'xp' => $achievement->xp,
            'coins' => $achievement->coins,
            'image_url' => $achievement->imageUrl(),
            'description' => $achievement->description,
            'status' => $achievement->status,
        ];
    }
}
