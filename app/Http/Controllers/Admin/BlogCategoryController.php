<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('screens.admin.blog-categories.index', compact('categories'));
    }

    public function store(Request $request): JsonResponse|Response
    {
        $validated = $this->validatedPayload($request);

        $slug = $validated['slug'] !== ''
            ? BlogCategory::slugFromName($validated['slug'])
            : BlogCategory::slugFromName($validated['name']);

        $maxOrder = (int) BlogCategory::query()->max('sort_order');

        $category = BlogCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'],
            'sort_order' => $maxOrder + 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Blog category created.'),
                'data' => $this->categoryPayload($category),
            ]);
        }

        return redirect()
            ->route('blog-categories.index')
            ->with('success', __('Blog category created.'));
    }

    public function update(Request $request, BlogCategory $blogCategory): JsonResponse|Response
    {
        $validated = $this->validatedPayload($request);

        $slug = $validated['slug'] !== ''
            ? BlogCategory::slugFromName($validated['slug'], $blogCategory->id)
            : BlogCategory::slugFromName($validated['name'], $blogCategory->id);

        $blogCategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Blog category updated.'),
                'data' => $this->categoryPayload($blogCategory->fresh()),
            ]);
        }

        return redirect()
            ->route('blog-categories.index')
            ->with('success', __('Blog category updated.'));
    }

    public function destroy(Request $request, BlogCategory $blogCategory): JsonResponse|Response
    {
        if ($blogCategory->blogs()->exists()) {
            $message = __('Cannot delete a category that still has posts. Move or delete those posts first.');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()
                ->route('blog-categories.index')
                ->with('error', $message);
        }

        $blogCategory->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Blog category deleted.')]);
        }

        return redirect()
            ->route('blog-categories.index')
            ->with('success', __('Blog category deleted.'));
    }

    /**
     * @return array{name: string, slug: string, status: string}
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255'],
            'status' => 'required|string|in:active,inactive',
        ]);

        return [
            'name' => $validated['name'],
            'slug' => trim((string) ($validated['slug'] ?? '')),
            'status' => $validated['status'],
        ];
    }

    private function categoryPayload(BlogCategory $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'status' => $c->status,
        ];
    }
}
