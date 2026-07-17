<?php

namespace App\Services;

use App\Models\PrintfulProduct;
use App\Models\PrintfulVariant;
use App\Models\ProductCategory;

class PrintfulProductSyncService
{
    private const BATCH_SIZE = 100;

    private ?string $lastFetchError = null;

    /** @var array<int, string|null> */
    private array $categoryCache = [];

    /** @var array<int, array<string, mixed>|null> */
    private array $catalogProductCache = [];

    public function __construct(
        private readonly PrintfulService $printful,
    ) {}

    /**
     * @return array{
     *     success: bool,
     *     message: string,
     *     synced_products: int,
     *     synced_variants: int,
     *     failed_products: int
     * }
     */
    public function sync(): array
    {
        $this->lastFetchError = null;

        $summaries = $this->fetchAllProductSummaries();

        if ($summaries === null) {
            return [
                'success' => false,
                'message' => $this->lastFetchError ?? 'Failed to fetch products from Printful.',
                'synced_products' => 0,
                'synced_variants' => 0,
                'failed_products' => 0,
            ];
        }

        if ($summaries === []) {
            return [
                'success' => true,
                'message' => 'No products found in Printful store.',
                'synced_products' => 0,
                'synced_variants' => 0,
                'failed_products' => 0,
            ];
        }

        $syncedProducts = 0;
        $syncedVariants = 0;
        $failedProducts = 0;

        foreach ($summaries as $summary) {
            $printfulProductId = $this->extractPrintfulProductId($summary);

            if ($printfulProductId === null) {
                $failedProducts++;

                continue;
            }

            $detailResponse = $this->printful->getStoreProduct($printfulProductId);

            if (! $detailResponse['success'] || ! is_array($detailResponse['data'])) {
                $failedProducts++;

                continue;
            }

            $product = $this->syncProduct($summary, $detailResponse['data']);
            $variantCount = $this->syncVariants($product, $detailResponse['data']);

            $syncedProducts++;
            $syncedVariants += $variantCount;
        }

        if ($syncedProducts === 0 && $failedProducts > 0) {
            return [
                'success' => false,
                'message' => sprintf('Sync failed. %d product(s) could not be synced.', $failedProducts),
                'synced_products' => 0,
                'synced_variants' => 0,
                'failed_products' => $failedProducts,
            ];
        }

        $message = sprintf(
            'Sync complete. %d product(s) and %d variant(s) synced.',
            $syncedProducts,
            $syncedVariants
        );

        if ($failedProducts > 0) {
            $message .= sprintf(' %d product(s) failed.', $failedProducts);
        }

        return [
            'success' => true,
            'message' => $message,
            'synced_products' => $syncedProducts,
            'synced_variants' => $syncedVariants,
            'failed_products' => $failedProducts,
        ];
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function fetchAllProductSummaries(): ?array
    {
        $summaries = [];
        $offset = 0;

        while (true) {
            $response = $this->printful->getStoreProducts(self::BATCH_SIZE, $offset);

            if (! $response['success']) {
                $this->lastFetchError = $response['message'] ?? 'Failed to fetch products from Printful.';

                return null;
            }

            $batch = $this->normalizeProductList($response['data']);

            if ($batch === []) {
                break;
            }

            array_push($summaries, ...$batch);

            if (count($batch) < self::BATCH_SIZE) {
                break;
            }

            $offset += self::BATCH_SIZE;
        }

        return $summaries;
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return list<array<string, mixed>>
     */
    private function normalizeProductList(?array $data): array
    {
        if ($data === null) {
            return [];
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $data;
        }

        if (isset($data['items']) && is_array($data['items'])) {
            return array_values(array_filter($data['items'], 'is_array'));
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function extractPrintfulProductId(array $item): ?int
    {
        $id = $item['id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $detailData
     */
    private function syncProduct(array $summary, array $detailData): PrintfulProduct
    {
        $syncProduct = is_array($detailData['sync_product'] ?? null)
            ? $detailData['sync_product']
            : $summary;

        $printfulProductId = $this->extractPrintfulProductId($syncProduct)
            ?? $this->extractPrintfulProductId($summary);

        $categoryName = $this->resolveCategoryName($detailData);
        $category = $this->findOrCreateCategory($categoryName);

        return PrintfulProduct::updateOrCreate(
            ['printful_product_id' => $printfulProductId],
            [
                'external_id' => $this->stringOrNull($syncProduct['external_id'] ?? $summary['external_id'] ?? null),
                'name' => $this->stringOrNull($syncProduct['name'] ?? $summary['name'] ?? null) ?? 'Untitled product',
                'category_name' => $categoryName,
                'category_id' => $category?->id,
                'thumbnail_url' => $this->stringOrNull($syncProduct['thumbnail_url'] ?? $summary['thumbnail_url'] ?? null),
                'is_synced' => $this->resolveIsSynced($summary, $syncProduct),
                'raw_data' => $detailData,
            ]
        );
    }

    /**
     * Find an existing product category by name (case-insensitive) or create it.
     */
    private function findOrCreateCategory(?string $categoryName): ?ProductCategory
    {
        if ($categoryName === null) {
            return null;
        }

        $existing = ProductCategory::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($categoryName)])
            ->first();

        if ($existing) {
            return $existing;
        }

        return ProductCategory::create([
            'name' => $categoryName,
            'slug' => ProductCategory::slugFromName($categoryName),
            'parent_id' => 0,
            'status' => 'active',
            'description' => null,
            'image' => null,
        ]);
    }

    /**
     * Resolve a human-readable category from Printful Catalog / Categories APIs.
     *
     * Store product payloads include catalog product_id and main_category_id on variants,
     * but not the category title. We look those up (with in-request caching) so re-sync
     * also backfills categories on existing products.
     *
     * @param  array<string, mixed>  $detailData
     */
    private function resolveCategoryName(array $detailData): ?string
    {
        $catalogProductId = $this->extractCatalogProductId($detailData);
        $categoryId = $this->extractMainCategoryId($detailData);

        if ($catalogProductId !== null) {
            $catalog = $this->fetchCatalogProduct($catalogProductId);

            if ($categoryId === null && $catalog !== null) {
                $categoryId = $this->numericId($catalog['main_category_id'] ?? null);
            }

            if ($categoryId !== null) {
                $title = $this->fetchCategoryTitle($categoryId);

                if ($title !== null) {
                    return $title;
                }
            }

            if ($catalog !== null) {
                $typeName = $this->stringOrNull($catalog['type_name'] ?? $catalog['type'] ?? null);

                if ($typeName !== null) {
                    return $typeName;
                }
            }
        }

        if ($categoryId !== null) {
            return $this->fetchCategoryTitle($categoryId);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $detailData
     */
    private function extractCatalogProductId(array $detailData): ?int
    {
        $variants = $detailData['sync_variants'] ?? [];

        if (! is_array($variants)) {
            return null;
        }

        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $nested = $variant['product'] ?? null;

            if (is_array($nested)) {
                $id = $this->numericId($nested['product_id'] ?? null);

                if ($id !== null) {
                    return $id;
                }
            }

            $id = $this->numericId($variant['product_id'] ?? null);

            if ($id !== null) {
                return $id;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $detailData
     */
    private function extractMainCategoryId(array $detailData): ?int
    {
        $variants = $detailData['sync_variants'] ?? [];

        if (! is_array($variants)) {
            return null;
        }

        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $id = $this->numericId($variant['main_category_id'] ?? null);

            if ($id !== null) {
                return $id;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchCatalogProduct(int $catalogProductId): ?array
    {
        if (array_key_exists($catalogProductId, $this->catalogProductCache)) {
            return $this->catalogProductCache[$catalogProductId];
        }

        $response = $this->printful->getCatalogProduct($catalogProductId);

        if (! $response['success'] || ! is_array($response['data'])) {
            $this->catalogProductCache[$catalogProductId] = null;

            return null;
        }

        $data = $response['data'];
        $product = is_array($data['product'] ?? null) ? $data['product'] : $data;
        $this->catalogProductCache[$catalogProductId] = is_array($product) ? $product : null;

        return $this->catalogProductCache[$catalogProductId];
    }

    private function fetchCategoryTitle(int $categoryId): ?string
    {
        if (array_key_exists($categoryId, $this->categoryCache)) {
            return $this->categoryCache[$categoryId];
        }

        $response = $this->printful->getCatalogCategory($categoryId);

        if (! $response['success'] || ! is_array($response['data'])) {
            $this->categoryCache[$categoryId] = null;

            return null;
        }

        $data = $response['data'];
        $category = is_array($data['category'] ?? null) ? $data['category'] : $data;

        $title = $this->stringOrNull($category['title'] ?? $category['name'] ?? null);
        $this->categoryCache[$categoryId] = $title;

        return $title;
    }

    private function numericId(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $syncProduct
     */
    private function resolveIsSynced(array $summary, array $syncProduct): bool
    {
        $variants = $summary['variants'] ?? $syncProduct['variants'] ?? null;
        $synced = $summary['synced'] ?? $syncProduct['synced'] ?? null;

        if (is_numeric($variants) && is_numeric($synced)) {
            return (int) $variants > 0 && (int) $synced >= (int) $variants;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $detailData
     */
    private function syncVariants(PrintfulProduct $product, array $detailData): int
    {
        $variants = $detailData['sync_variants'] ?? [];

        if (! is_array($variants)) {
            return 0;
        }

        $count = 0;

        foreach ($variants as $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $this->syncVariant($product, $variant);
            $count++;
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $variant
     */
    private function syncVariant(PrintfulProduct $product, array $variant): PrintfulVariant
    {
        $printfulVariantId = isset($variant['id']) && is_numeric($variant['id'])
            ? (int) $variant['id']
            : null;

        $match = ['printful_product_id' => $product->id];

        if ($printfulVariantId !== null) {
            $match['printful_variant_id'] = $printfulVariantId;
        } elseif (($externalId = $this->stringOrNull($variant['external_id'] ?? null)) !== null) {
            $match['external_id'] = $externalId;
        }

        return PrintfulVariant::updateOrCreate($match, [
            'printful_variant_id' => $printfulVariantId,
            'external_id' => $this->stringOrNull($variant['external_id'] ?? null),
            'name' => $this->stringOrNull($variant['name'] ?? null),
            'sku' => $this->stringOrNull($variant['sku'] ?? null),
            'retail_price' => $this->decimalOrNull($variant['retail_price'] ?? null),
            'currency' => $this->stringOrNull($variant['currency'] ?? null),
            'thumbnail_url' => $this->resolveVariantThumbnail($variant),
            'raw_data' => $variant,
        ]);
    }

    /**
     * @param  array<string, mixed>  $variant
     */
    private function resolveVariantThumbnail(array $variant): ?string
    {
        if ($thumbnail = $this->stringOrNull($variant['thumbnail_url'] ?? null)) {
            return $thumbnail;
        }

        $nested = $variant['product'] ?? $variant['variant'] ?? null;

        if (is_array($nested)) {
            return $this->stringOrNull($nested['thumbnail_url'] ?? $nested['image'] ?? null);
        }

        return null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private function decimalOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }
}
