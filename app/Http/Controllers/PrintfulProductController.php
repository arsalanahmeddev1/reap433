<?php

namespace App\Http\Controllers;

use App\Models\PrintfulProduct;
use Illuminate\View\View;

class PrintfulProductController extends Controller
{
    public function index(): View
    {
        $products = PrintfulProduct::query()
            ->withCount('variants')
            ->when(auth()->check(), function ($query) {
                $query->withExists([
                    'favouriteRecords as is_favourite' => function ($q) {
                        $q->where('user_id', auth()->id());
                    },
                ]);
            })
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show(PrintfulProduct $printfulProduct): View
    {
        $printfulProduct->load('variants');

        if (auth()->check()) {
            $printfulProduct->loadExists([
                'favouriteRecords as is_favourite' => function ($q) {
                    $q->where('user_id', auth()->id());
                },
            ]);
        } else {
            $printfulProduct->setAttribute('is_favourite', false);
        }

        return view('products.show', ['product' => $printfulProduct]);
    }
}
