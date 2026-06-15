<?php

namespace App\Services;

use App\Models\PrintfulProduct;
use App\Models\PrintfulVariant;

class PrintfulProductSyncService
{
    private const BATCH_SIZE = 100;

    private ?string $lastFetchError = null;

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

        return PrintfulProduct::updateOrCreate(
            ['printful_product_id' => $printfulProductId],
            [
                'external_id' => $this->stringOrNull($syncProduct['external_id'] ?? $summary['external_id'] ?? null),
                'name' => $this->stringOrNull($syncProduct['name'] ?? $summary['name'] ?? null) ?? 'Untitled product',
                'thumbnail_url' => $this->stringOrNull($syncProduct['thumbnail_url'] ?? $summary['thumbnail_url'] ?? null),
                'is_synced' => $this->resolveIsSynced($summary, $syncProduct),
                'raw_data' => $detailData,
            ]
        );
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
