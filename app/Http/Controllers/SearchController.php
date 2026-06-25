<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Storefront search results — artifacts, Printful merch, and journal.
     */
    public function index(Request $request, SearchService $searchService): View
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:120',
        ]);

        $query = trim((string) ($validated['q'] ?? ''));

        $artifacts = collect();
        $printfulProducts = collect();
        $blogs = collect();

        if ($query !== '') {
            $results = $searchService->search($query);
            $artifacts = $results['artifacts'];
            $printfulProducts = $results['printfulProducts'];
            $blogs = $results['blogs'];
        }

        return view('screens.web.search.index', [
            'query' => $query,
            'artifacts' => $artifacts,
            'printfulProducts' => $printfulProducts,
            'blogs' => $blogs,
            'total' => $artifacts->count() + $printfulProducts->count() + $blogs->count(),
        ]);
    }
}
