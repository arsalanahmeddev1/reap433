<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $categories = $this->activeCategories();
        $activeCategory = $this->normalizeCategoryQuery($request);
        $blogs = $this->paginatedBlogs($request, route('blog.index'));

        return view('screens.web.blogs.index', [
            'categories' => $categories,
            'blogs' => $blogs,
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * AJAX fragment for blog listing + pagination (category filter without full page reload).
     */
    public function posts(Request $request): JsonResponse
    {
        $blogs = $this->paginatedBlogs($request, route('blog.posts'));
        $activeCategory = $this->normalizeCategoryQuery($request);

        $html = view('screens.web.blogs.partials.posts-results', [
            'blogs' => $blogs,
        ])->render();

        return response()->json([
            'html' => $html,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(string $slug): View
    {
        $blog = Blog::query()
            ->with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('screens.web.blogs.show', compact('blog'));
    }

    private function activeCategories()
    {
        return BlogCategory::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function normalizeCategoryQuery(Request $request): ?string
    {
        $activeCategory = $request->query('category');

        return is_string($activeCategory) && $activeCategory !== '' ? $activeCategory : null;
    }

    private function paginatedBlogs(Request $request, string $path): LengthAwarePaginator
    {
        $activeCategory = $this->normalizeCategoryQuery($request);

        $blogsQuery = Blog::query()
            ->with('category')
            ->published()
            ->latest('published_at');

        if ($activeCategory !== null && $activeCategory !== 'all') {
            $blogsQuery->whereHas('category', fn ($q) => $q->where('slug', $activeCategory));
        }

        $blogs = $blogsQuery->paginate(9)->withPath($path);

        $append = [];
        if ($activeCategory !== null && $activeCategory !== 'all') {
            $append['category'] = $activeCategory;
        }

        return $append === [] ? $blogs : $blogs->appends($append);
    }
}
