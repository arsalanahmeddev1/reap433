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
            ->with('productCategories')
            ->where('slug', $slug)
            ->firstOrFail();

        $categoryIds = $collectionPage->productCategories->pluck('id');

        if ($categoryIds->isEmpty() && $collectionPage->category) {
            $categoryIds = collect([$collectionPage->category]);
        }

        $products = PrintfulProduct::query()
            ->withCount('variants')
            ->when(auth()->check(), function ($query) {
                $query->withExists([
                    'favouriteRecords as is_favourite' => function ($q) {
                        $q->where('user_id', auth()->id());
                    },
                ]);
            })
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->latest()
            ->paginate(12);

        return view('screens.web.collection-pages.show', [
            'collectionPage' => $collectionPage,
            'products' => $products,
        ]);
    }
}
