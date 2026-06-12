<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::query()
            ->orderByDesc('id')
            ->get();

        return view('screens.admin.product-categories.index', get_defined_vars());
    }

    public function store(Request $request): JsonResponse|Response
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:4096',
            'status' => 'required|string|in:active,inactive',
        ]);

        $parentId = $this->resolveParentId((int) ($validated['parent_id'] ?? 0));

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-categories', 'public');
        }

        $category = ProductCategory::create([
            'name' => $validated['name'],
            'parent_id' => $parentId,
            'slug' => ProductCategory::slugFromName($validated['name']),
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $html = view('screens.admin.product-categories.partials.table-row', [
                'category' => $category->fresh(),
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('Category created successfully.'),
                'data' => $this->categoryPayload($category),
                'html' => $html,
            ]);
        }

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Category created successfully.'));
    }

    public function update(Request $request, ProductCategory $category): JsonResponse|Response
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:4096',
            'remove_image' => 'sometimes|boolean',
            'status' => 'required|string|in:active,inactive',
        ]);

        $parentId = $this->resolveParentId((int) ($validated['parent_id'] ?? 0), $category->id);

        $imagePath = $category->image;

        if ($request->boolean('remove_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('product-categories', 'public');
        }

        $category->update([
            'name' => $validated['name'],
            'parent_id' => $parentId,
            'slug' => ProductCategory::slugFromName($validated['name'], $category->id),
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Category updated successfully.'),
                'data' => $this->categoryPayload($category->fresh()),
            ]);
        }

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    public function destroy(Request $request, ProductCategory $category): JsonResponse|Response
    {
        if ($category->products()->exists()) {
            $message = __('Cannot delete this category while it still has products. Reassign or remove those products first.');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('product-categories.index')
                ->with('error', $message);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Category deleted successfully.'),
            ]);
        }

        return redirect()
            ->route('product-categories.index')
            ->with('success', __('Category deleted successfully.'));
    }

    protected function categoryPayload(ProductCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => (int) $category->parent_id,
            'description' => $category->description,
            'status' => $category->status,
            'image_url' => $category->imageUrl(),
        ];
    }

    protected function resolveParentId(int $parentId, ?int $ignoreCategoryId = null): int
    {
        if ($parentId <= 0) {
            return 0;
        }

        if ($ignoreCategoryId !== null && $parentId === $ignoreCategoryId) {
            return 0;
        }

        $parent = ProductCategory::query()
            ->where('id', $parentId)
            ->where('parent_id', 0)
            ->when($ignoreCategoryId, fn ($query) => $query->where('id', '!=', $ignoreCategoryId))
            ->first();

        return $parent ? (int) $parent->id : 0;
    }
}
