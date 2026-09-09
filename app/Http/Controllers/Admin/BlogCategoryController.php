<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $maxOrder = (int) BlogCategory::query()->max('sort_order');

        $category = BlogCategory::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'status' => $validated['status'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
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

    public function update(Request $request, BlogCategory $blogCategory): JsonResponse|RedirectResponse
    {
        $validated = $this->validatedPayload($request, $blogCategory->id);

        $blogCategory->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'status' => $validated['status'],
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
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

    public function destroy(Request $request, BlogCategory $blogCategory): JsonResponse|RedirectResponse
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
     * @return array{name: string, slug: string, status: string, seo_title: ?string, seo_description: ?string}
     */
    private function validatedPayload(Request $request, ?int $ignoreId = null): array
    {
        $request->merge([
            'slug' => Str::slug(trim((string) $request->input('slug', ''))),
        ]);

        $uniqueRule = Rule::unique('blog_categories', 'slug');
        if ($ignoreId !== null) {
            $uniqueRule = $uniqueRule->ignore($ignoreId);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', $uniqueRule],
            'status' => 'required|string|in:active,inactive',
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ], [
            'slug.unique' => __('Slug is already exist in the records.'),
            'slug.required' => __('Slug is required.'),
        ]);

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'status' => $validated['status'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ];
    }

    private function categoryPayload(BlogCategory $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'status' => $c->status,
            'seo_title' => $c->seo_title,
            'seo_description' => $c->seo_description,
        ];
    }
}
