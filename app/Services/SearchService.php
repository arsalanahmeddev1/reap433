<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\PrintfulProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Storefront search — queries artifacts, Printful products, and journal posts.
 */
class SearchService
{
    private const RESULT_LIMIT = 12;

    /**
     * @return array{
     *     artifacts: Collection<int, Product>,
     *     printfulProducts: Collection<int, PrintfulProduct>,
     *     blogs: Collection<int, Blog>
     * }
     */
    public function search(string $term): array
    {
        $like = '%'.addcslashes(trim($term), '%_\\').'%';

        return [
            'artifacts' => $this->searchArtifacts($like),
            'printfulProducts' => $this->searchPrintfulProducts($like),
            'blogs' => $this->searchBlogs($like),
        ];
    }

    /** @return Collection<int, Product> */
    private function searchArtifacts(string $like): Collection
    {
        return Product::query()
            ->with(['category', 'productType', 'images', 'variations'])
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            })
            ->latest()
            ->limit(self::RESULT_LIMIT)
            ->get();
    }

    /** @return Collection<int, PrintfulProduct> */
    private function searchPrintfulProducts(string $like): Collection
    {
        return PrintfulProduct::query()
            ->withCount('variants')
            ->where('name', 'like', $like)
            ->latest()
            ->limit(self::RESULT_LIMIT)
            ->get();
    }

    /** @return Collection<int, Blog> */
    private function searchBlogs(string $like): Collection
    {
        return Blog::query()
            ->with('category')
            ->published()
            ->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('body', 'like', $like);
            })
            ->latest('published_at')
            ->limit(self::RESULT_LIMIT)
            ->get();
    }
}
