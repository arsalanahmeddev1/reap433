<?php

namespace App\Http\Controllers;

use App\Models\CollectionPage;
use App\Models\PrintfulProduct;
use Illuminate\View\View;

class CollectionPageController extends Controller
{
    public function show(string $slug): View
    {
        $collectionPage = CollectionPage::query()
            ->with(['productCategories', 'uncategorizedProducts:id'])
            ->where('slug', $slug)
            ->firstOrFail();

        $categoryIds = $collectionPage->productCategories->pluck('id');

        if ($categoryIds->isEmpty() && $collectionPage->category) {
            $categoryIds = collect([$collectionPage->category]);
        }

        $uncategorizedProductIds = $collectionPage->uncategorizedProducts->pluck('id');

        $products = PrintfulProduct::query()
            ->withCount('variants')
            ->when(auth()->check(), function ($query) {
                $query->withExists([
                    'favouriteRecords as is_favourite' => function ($q) {
                        $q->where('user_id', auth()->id());
                    },
                ]);
            })
            ->where(function ($query) use ($categoryIds, $uncategorizedProductIds) {
                if ($categoryIds->isNotEmpty()) {
                    $query->whereIn('category_id', $categoryIds);
                }

                if ($uncategorizedProductIds->isNotEmpty()) {
                    $method = $categoryIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                    $query->{$method}('id', $uncategorizedProductIds);
                }

                if ($categoryIds->isEmpty() && $uncategorizedProductIds->isEmpty()) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->latest()
            ->paginate(12);

        return view('screens.web.collection-pages.show', [
            'collectionPage' => $collectionPage,
            'products' => $products,
        ]);
    }
}
