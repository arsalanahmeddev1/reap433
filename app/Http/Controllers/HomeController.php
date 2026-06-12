<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $blogCategories = BlogCategory::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $hubBlogs = Blog::query()
            ->with(['category', 'author'])
            ->published()
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('screens.web.home.index', compact('blogCategories', 'hubBlogs'));
    }
}
