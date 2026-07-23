<?php

namespace App\Support;

use App\Models\PrintfulProduct;
use App\Models\PrintfulVariant;

class PrintfulVariantOptions
{
    /**
     * Build color/size/placement maps from synced variant raw_data (no hardcoding).
     *
     * @return array{
     *     colors: list<array{name: string, thumbnail_url: ?string, sizes: list<string>}>,
     *     variants: list<array<string, mixed>>,
     *     placements: list<array{type: string, label: string}>
     * }
     */
    public static function forProduct(PrintfulProduct $product): array
    {
        $product->loadMissing('variants');

        $variants = [];
        $colorMap = [];
        $placements = [];

        foreach ($product->variants as $variant) {
            $meta = self::extractMeta($variant);
            $variants[] = [
                'id' => $variant->id,
                'printful_variant_id' => $variant->printful_variant_id,
                'catalog_variant_id' => $meta['catalog_variant_id'],
                'catalog_product_id' => $meta['catalog_product_id'],
                'name' => $variant->name,
                'color' => $meta['color'],
                'size' => $meta['size'],
                'sku' => $variant->sku,
                'price' => $variant->retail_price !== null ? (float) $variant->retail_price : 0.0,
                'currency' => $variant->currency ?? 'USD',
                'thumbnail_url' => $variant->thumbnail_url ?: $meta['image'],
                'availability_status' => $meta['availability_status'],
            ];

            $color = $meta['color'] ?: 'Default';
            if (! isset($colorMap[$color])) {
                $colorMap[$color] = [
                    'name' => $color,
                    'thumbnail_url' => $variant->thumbnail_url ?: $meta['image'],
                    'sizes' => [],
                ];
            }

            $size = $meta['size'] ?: 'One size';
            if (! in_array($size, $colorMap[$color]['sizes'], true)) {
                $colorMap[$color]['sizes'][] = $size;
            }

            foreach ($meta['placements'] as $placement) {
                $placements[$placement['type']] = $placement;
            }
        }

        if ($placements === []) {
            $placements['default'] = [
                'type' => 'default',
                'label' => 'Default print area',
            ];
        }

        return [
            'colors' => array_values($colorMap),
            'variants' => $variants,
            'placements' => array_values($placements),
        ];
    }

    /**
     * @return array{
     *     color: ?string,
     *     size: ?string,
     *     catalog_variant_id: ?int,
     *     catalog_product_id: ?int,
     *     image: ?string,
     *     availability_status: ?string,
     *     placements: list<array{type: string, label: string}>
     * }
     */
    public static function extractMeta(PrintfulVariant $variant): array
    {
        $raw = is_array($variant->raw_data) ? $variant->raw_data : [];
        $product = is_array($raw['product'] ?? null) ? $raw['product'] : [];

        $color = self::stringOrNull($raw['color'] ?? $product['color'] ?? null);
        $size = self::stringOrNull($raw['size'] ?? $product['size'] ?? null);

        if (($color === null || $size === null) && is_string($variant->name)) {
            $parsed = self::parseNameParts($variant->name);
            $color ??= $parsed['color'];
            $size ??= $parsed['size'];
        }

        $placements = [];
        $files = $raw['files'] ?? [];
        if (is_array($files)) {
            foreach ($files as $file) {
                if (! is_array($file)) {
                    continue;
                }
                $type = self::stringOrNull($file['type'] ?? null);
                if ($type === null || $type === 'preview') {
                    continue;
                }
                $placements[$type] = [
                    'type' => $type,
                    'label' => self::labelForPlacement($type),
                ];
            }
        }

        return [
            'color' => $color,
            'size' => $size,
            'catalog_variant_id' => self::intOrNull($raw['variant_id'] ?? $product['variant_id'] ?? null),
            'catalog_product_id' => self::intOrNull($product['product_id'] ?? null),
            'image' => self::stringOrNull($product['image'] ?? null),
            'availability_status' => self::stringOrNull($raw['availability_status'] ?? null),
            'placements' => array_values($placements),
        ];
    }

    public static function findVariant(
        PrintfulProduct $product,
        ?string $color,
        ?string $size,
        ?int $localVariantId = null,
    ): ?PrintfulVariant {
        $product->loadMissing('variants');

        if ($localVariantId) {
            $match = $product->variants->firstWhere('id', $localVariantId);
            if ($match) {
                return $match;
            }
        }

        return $product->variants->first(function (PrintfulVariant $variant) use ($color, $size) {
            $meta = self::extractMeta($variant);
            $colorOk = $color === null || strcasecmp((string) $meta['color'], (string) $color) === 0
                || ($meta['color'] === null && ($color === 'Default' || $color === null));
            $sizeOk = $size === null || strcasecmp((string) $meta['size'], (string) $size) === 0
                || ($meta['size'] === null && ($size === 'One size' || $size === null));

            return $colorOk && $sizeOk;
        });
    }

    /**
     * @return array{color: ?string, size: ?string}
     */
    private static function parseNameParts(string $name): array
    {
        // Common Printful patterns: "Product / Color" or "Product / Color / Size"
        $parts = array_values(array_filter(array_map('trim', explode('/', $name))));
        if (count($parts) >= 3) {
            return ['color' => $parts[count($parts) - 2], 'size' => $parts[count($parts) - 1]];
        }
        if (count($parts) === 2) {
            return ['color' => $parts[1], 'size' => 'One size'];
        }

        return ['color' => null, 'size' => null];
    }

    private static function labelForPlacement(string $type): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $type));
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private static function intOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
