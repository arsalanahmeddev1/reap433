<?php

namespace App\Http\Controllers;

use App\Models\SitePage;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function show(string $slug): View
    {
        $page = SitePage::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return view('screens.web.site-pages.show', compact('page'));
    }
}
