<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintfulProduct;
use App\Services\PrintfulProductSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrintfulController extends Controller
{
    public function index(): View
    {
        $products = PrintfulProduct::query()
            ->with('category')
            ->withCount('variants')
            ->latest()
            ->paginate(12);

        return view('screens.admin.printful.index', compact('products'));
    }

    public function show(PrintfulProduct $printfulProduct): View
    {
        $printfulProduct->load(['variants', 'category']);

        return view('screens.admin.printful.show', [
            'product' => $printfulProduct,
        ]);
    }

    public function edit(PrintfulProduct $printfulProduct): View
    {
        $printfulProduct->load('category');

        return view('screens.admin.printful.edit', [
            'product' => $printfulProduct,
        ]);
    }

    public function update(Request $request, PrintfulProduct $printfulProduct): RedirectResponse
    {
        $validated = $request->validate([
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);

        $printfulProduct->update([
            'seo_title' => filled($validated['seo_title'] ?? null)
                ? trim((string) $validated['seo_title'])
                : null,
            'seo_description' => filled($validated['seo_description'] ?? null)
                ? trim((string) $validated['seo_description'])
                : null,
        ]);

        return redirect()
            ->route('admin.printful.products.edit', $printfulProduct)
            ->with('success', __('Product SEO updated.'));
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
