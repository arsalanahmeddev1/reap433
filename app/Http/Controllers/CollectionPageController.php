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
            ->with('productCategory')
            ->where('slug', $slug)
            ->firstOrFail();

        $products = PrintfulProduct::query()
            ->withCount('variants')
            ->when(auth()->check(), function ($query) {
                $query->withExists([
                    'favouriteRecords as is_favourite' => function ($q) {
                        $q->where('user_id', auth()->id());
                    },
                ]);
            })
            ->where('category_id', $collectionPage->category)
            ->latest()
            ->paginate(12);

        return view('screens.web.collection-pages.show', [
            'collectionPage' => $collectionPage,
            'products' => $products,
        ]);
    }
}
