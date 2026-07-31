<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollectionPage;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CollectionPageController extends Controller
{
    public function index(): View
    {
        $collectionPages = CollectionPage::query()
            ->with('productCategory')
            ->latest()
            ->get();

        return view('screens.admin.collection-pages.index', compact('collectionPages'));
    }

    public function create(): View
    {
        $categories = $this->categories();

        return view('screens.admin.collection-pages.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validatedPayload($request);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('collection-pages', 'public');
        }

        CollectionPage::create([
            'title' => $validated['title'],
            'slug' => CollectionPage::slugFromTitle($validated['title']),
            'category' => $validated['category'],
            'image' => $path,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Collection page created.'),
                'redirect' => route('collection-pages.index'),
            ]);
        }

        return redirect()
            ->route('collection-pages.index')
            ->with('success', __('Collection page created.'));
    }

    public function edit(CollectionPage $collectionPage): View
    {
        $categories = $this->categories();

        return view('screens.admin.collection-pages.edit', compact('collectionPage', 'categories'));
    }

    public function update(Request $request, CollectionPage $collectionPage): RedirectResponse|JsonResponse
    {
        $validated = $this->validatedPayload($request);

        if ($request->boolean('remove_image') && $collectionPage->image) {
            Storage::disk('public')->delete($collectionPage->image);
            $collectionPage->image = null;
        }

        $path = $collectionPage->image;
        if ($request->hasFile('image')) {
            if ($collectionPage->image) {
                Storage::disk('public')->delete($collectionPage->image);
            }
            $path = $request->file('image')->store('collection-pages', 'public');
        }

        $collectionPage->update([
            'title' => $validated['title'],
            'slug' => CollectionPage::slugFromTitle($validated['title'], $collectionPage->id),
            'category' => $validated['category'],
            'image' => $path,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Collection page updated.'),
                'redirect' => route('collection-pages.index'),
            ]);
        }

        return redirect()
            ->route('collection-pages.index')
            ->with('success', __('Collection page updated.'));
    }

    public function destroy(Request $request, CollectionPage $collectionPage): RedirectResponse|JsonResponse
    {
        if ($collectionPage->image) {
            Storage::disk('public')->delete($collectionPage->image);
        }

        $collectionPage->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Collection page deleted.'),
                'redirect' => route('collection-pages.index'),
            ]);
        }

        return redirect()
            ->route('collection-pages.index')
            ->with('success', __('Collection page deleted.'));
    }

    /**
     * @return \Illuminate\Support\Collection<int, ProductCategory>
     */
    private function categories()
    {
        return ProductCategory::query()
            ->where('status', 'active')
            ->where('slug', '!=', 'all-products')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|exists:product_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:4096',
            'remove_image' => 'sometimes|boolean',
            'description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);
    }
}
