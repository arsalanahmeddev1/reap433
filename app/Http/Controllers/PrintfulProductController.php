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
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show(PrintfulProduct $printfulProduct): View
    {
        $printfulProduct->load('variants');

        return view('products.show', ['product' => $printfulProduct]);
    }
}
