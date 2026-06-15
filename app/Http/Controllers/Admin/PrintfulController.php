<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintfulProduct;
use App\Services\PrintfulProductSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrintfulController extends Controller
{
    public function index(): View
    {
        $products = PrintfulProduct::query()
            ->withCount('variants')
            ->latest()
            ->paginate(12);

        return view('screens.admin.printful.index', compact('products'));
    }

    public function show(PrintfulProduct $printfulProduct): View
    {
        $printfulProduct->load('variants');

        return view('screens.admin.printful.show', [
            'product' => $printfulProduct,
        ]);
    }

    public function syncProducts(PrintfulProductSyncService $syncService): RedirectResponse
    {
        $result = $syncService->sync();

        return redirect()
            ->back(fallback: route('admin.printful.products.index'))
            ->with(
                $result['success'] ? 'success' : 'error',
                $result['message']
            );
    }
}
