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
            ->with(['productCategories', 'productCategory'])
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
        $categoryIds = $this->categoryIds($validated);
        $faqs = $this->normalizedFaqs($validated['faqs'] ?? []);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('collection-pages', 'public');
        }

        $collectionPage = CollectionPage::create([
            'title' => $validated['title'],
            'slug' => $this->resolveSlug($validated),
            'category' => $categoryIds[0] ?? null,
            'image' => $path,
            'description' => $validated['description'] ?? null,
            'faqs' => $faqs,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ]);

        $collectionPage->productCategories()->sync($categoryIds);

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
        $collectionPage->load('productCategories');

        return view('screens.admin.collection-pages.edit', compact('collectionPage', 'categories'));
    }

    public function update(Request $request, CollectionPage $collectionPage): RedirectResponse|JsonResponse
    {
        $validated = $this->validatedPayload($request, $collectionPage);
        $categoryIds = $this->categoryIds($validated);
        $faqs = $this->normalizedFaqs($validated['faqs'] ?? []);

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
            'slug' => $this->resolveSlug($validated, $collectionPage->id),
            'category' => $categoryIds[0] ?? null,
            'image' => $path,
            'description' => $validated['description'] ?? null,
            'faqs' => $faqs,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ]);

        $collectionPage->productCategories()->sync($categoryIds);

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
    private function validatedPayload(Request $request, ?CollectionPage $collectionPage = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($collectionPage): void {
                    $slug = trim((string) $value);
                    if ($slug === '') {
                        return;
                    }

                    $normalized = \Illuminate\Support\Str::slug($slug);
                    if ($normalized === '') {
                        $fail(__('Enter a valid slug.'));

                        return;
                    }

                    $exists = CollectionPage::query()
                        ->where('slug', $normalized)
                        ->when($collectionPage, fn ($q) => $q->where('id', '!=', $collectionPage->id))
                        ->exists();

                    if ($exists) {
                        $fail(__('This slug is already in use.'));
                    }
                },
            ],
            'categories' => 'required|array|min:1',
            'categories.*' => 'integer|exists:product_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:4096',
            'remove_image' => 'sometimes|boolean',
            'description' => 'nullable|string',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveSlug(array $validated, ?int $ignoreId = null): string
    {
        $raw = trim((string) ($validated['slug'] ?? ''));

        if ($raw === '') {
            return CollectionPage::slugFromTitle($validated['title'], $ignoreId);
        }

        $slug = \Illuminate\Support\Str::slug($raw);

        if ($slug === '') {
            return CollectionPage::slugFromTitle($validated['title'], $ignoreId);
        }

        return $slug;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return list<int>
     */
    private function categoryIds(array $validated): array
    {
        return array_values(array_unique(array_map('intval', $validated['categories'] ?? [])));
    }

    /**
     * @param  array<int, array{question?: string|null, answer?: string|null}>  $faqs
     * @return list<array{question: string, answer: string}>
     */
    private function normalizedFaqs(array $faqs): array
    {
        $normalized = [];

        foreach ($faqs as $faq) {
            $question = trim((string) ($faq['question'] ?? ''));
            $answer = trim((string) ($faq['answer'] ?? ''));

            if ($question === '' && $answer === '') {
                continue;
            }

            $normalized[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $normalized;
    }
}
