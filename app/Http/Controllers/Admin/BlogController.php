<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::query()
            ->with('category')
            ->latest()
            ->get();

        return view('screens.admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('screens.admin.blogs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->merge([
            'slug' => Str::slug(trim((string) $request->input('slug', ''))),
        ]);

        $validated = $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('blogs', 'slug')],
            'body' => 'required|string',
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date_format:Y-m-d',
        ], [
            'slug.unique' => __('Slug is already exist in the records.'),
            'slug.required' => __('Slug is required.'),
        ]);

        $isPublished = $request->boolean('is_published');
        // A schedule date means the post is set to publish on that day.
        if (filled($validated['published_at'] ?? null)) {
            $isPublished = true;
        }
        $publishedAt = $this->resolvePublishedAt($validated['published_at'] ?? null, $isPublished);

        $path = null;
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blogs', 'public');
        }

        Blog::create([
            'blog_category_id' => $validated['blog_category_id'],
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'body' => $validated['body'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'featured_image' => $path,
            'is_published' => $isPublished,
            'published_at' => $publishedAt,
            'created_by' => auth()->id(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Blog created.'),
                'redirect' => route('blogs.index'),
            ]);
        }

        return redirect()->route('blogs.index')->with('success', __('Blog created.'));
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('screens.admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse|JsonResponse
    {
        $request->merge([
            'slug' => Str::slug(trim((string) $request->input('slug', ''))),
        ]);

        $validated = $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('blogs', 'slug')->ignore($blog->id)],
            'body' => 'required|string',
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date_format:Y-m-d',
            'remove_featured_image' => 'sometimes|boolean',
        ], [
            'slug.unique' => __('Slug is already exist in the records.'),
            'slug.required' => __('Slug is required.'),
        ]);

        $isPublished = $request->boolean('is_published');
        // A schedule date means the post is set to publish on that day.
        if (filled($validated['published_at'] ?? null)) {
            $isPublished = true;
        }
        $publishedAt = $this->resolvePublishedAt(
            $validated['published_at'] ?? null,
            $isPublished,
            $blog->published_at
        );

        if ($request->boolean('remove_featured_image') && $blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
            $blog->featured_image = null;
        }

        $path = $blog->featured_image;
        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $path = $request->file('featured_image')->store('blogs', 'public');
        }

        $blog->update([
            'blog_category_id' => $validated['blog_category_id'],
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'body' => $validated['body'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'featured_image' => $path,
            'is_published' => $isPublished,
            'published_at' => $publishedAt,
            'updated_by' => auth()->id(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Blog updated.'),
                'redirect' => route('blogs.index'),
            ]);
        }

        return redirect()->route('blogs.index')->with('success', __('Blog updated.'));
    }

    /** Quill / blog body: JSON { url } for in-body images. */
    public function uploadBlogBodyImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'max:8192', 'mimes:jpeg,jpg,png,gif,webp,bmp,avif'],
        ]);

        $path = $request->file('image')->store('blog-editor', 'public');

        /* Root-relative URL so images work on any host/port (e.g. localhost:8000 vs APP_URL localhost). */
        $publicPath = ltrim(str_replace('\\', '/', $path), '/');

        return response()->json([
            'url' => '/storage/'.$publicPath,
        ]);
    }

    public function destroy(Request $request, Blog $blog): RedirectResponse|JsonResponse
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        $blog->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Blog deleted.'),
                'redirect' => route('blogs.index'),
            ]);
        }

        return redirect()->route('blogs.index')->with('success', __('Blog deleted.'));
    }

    private function resolvePublishedAt(mixed $publishedAt, bool $isPublished, mixed $fallback = null): ?Carbon
    {
        $date = is_string($publishedAt) ? trim($publishedAt) : null;

        // Always persist a chosen schedule date (even if not published yet).
        if ($date) {
            $scheduled = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
            // If this calendar day is already "today" for go-live, don't keep a
            // future midnight timestamp that UTC would still treat as not live.
            $today = now()->copy()->utc()->addHours(14)->toDateString();
            if ($date <= $today && $scheduled->greaterThan(now())) {
                return now();
            }

            return $scheduled;
        }

        if (! $isPublished) {
            return null;
        }

        if ($fallback instanceof Carbon) {
            return $fallback->copy()->startOfDay();
        }

        if (is_string($fallback) && trim($fallback) !== '') {
            return Carbon::parse($fallback)->startOfDay();
        }

        return now()->startOfDay();
    }
}
