<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $validated = $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255'],
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        $slugRaw = trim((string) ($validated['slug'] ?? ''));
        $slug = $slugRaw !== ''
            ? Blog::slugFromTitle($slugRaw)
            : Blog::slugFromTitle($validated['title']);

        $isPublished = $request->boolean('is_published');
        $publishedAt = $validated['published_at'] ?? null;
        if ($isPublished && $publishedAt === null) {
            $publishedAt = now();
        }

        $path = null;
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blogs', 'public');
        }

        Blog::create([
            'blog_category_id' => $validated['blog_category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'body' => $validated['body'],
            'featured_image' => $path,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? $publishedAt : null,
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
        $validated = $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255'],
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
            'remove_featured_image' => 'sometimes|boolean',
        ]);

        $slugRaw = trim((string) ($validated['slug'] ?? ''));
        $slug = $slugRaw !== ''
            ? Blog::slugFromTitle($slugRaw, $blog->id)
            : Blog::slugFromTitle($validated['title'], $blog->id);

        $isPublished = $request->boolean('is_published');
        $publishedAt = $validated['published_at'] ?? $blog->published_at;
        if ($isPublished && $publishedAt === null) {
            $publishedAt = now();
        }

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
            'slug' => $slug,
            'body' => $validated['body'],
            'featured_image' => $path,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? $publishedAt : null,
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
}
