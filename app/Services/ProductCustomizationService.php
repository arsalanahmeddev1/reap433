<?php

namespace App\Services;

use App\Models\PrintfulProduct;
use App\Models\PrintfulVariant;
use App\Models\ProductCustomization;
use App\Models\ProductCustomizationFile;
use App\Models\User;
use App\Support\PrintfulVariantOptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class ProductCustomizationService
{
    public function feeAmount(): float
    {
        return round((float) config('services.printful.customization_fee', 0), 2);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function saveDraft(User $user, PrintfulProduct $product, array $payload, ?UploadedFile $upload = null): ProductCustomization
    {
        $variant = $this->resolveVariant($product, $payload);

        return DB::transaction(function () use ($user, $product, $payload, $upload, $variant) {
            $customization = null;

            if (! empty($payload['uuid'])) {
                $customization = ProductCustomization::query()
                    ->where('uuid', $payload['uuid'])
                    ->where('user_id', $user->id)
                    ->first();
            }

            $meta = PrintfulVariantOptions::extractMeta($variant);

            $data = [
                'user_id' => $user->id,
                'printful_product_id' => $product->id,
                'printful_sync_product_id' => $product->printful_product_id,
                'printful_variant_id' => $variant->id,
                'printful_sync_variant_id' => $variant->printful_variant_id,
                'catalog_variant_id' => $meta['catalog_variant_id'],
                'catalog_product_id' => $meta['catalog_product_id'],
                'color' => $payload['color'] ?? $meta['color'],
                'size' => $payload['size'] ?? $meta['size'],
                'placement' => $payload['placement'] ?? ($meta['placements'][0]['type'] ?? 'default'),
                'canvas_json' => $payload['canvas_json'] ?? null,
                'text_settings' => $payload['text_settings'] ?? null,
                'image_settings' => $payload['image_settings'] ?? null,
                'print_area' => $payload['print_area'] ?? $this->defaultPrintArea(),
                'customization_fee' => $this->feeAmount(),
                'status' => ProductCustomization::STATUS_DRAFT,
            ];

            if ($customization) {
                $customization->update($data);
            } else {
                $customization = ProductCustomization::query()->create($data);
            }

            if ($upload) {
                $this->storeUpload($customization, $upload);
            }

            if (! empty($payload['preview_data_url']) && is_string($payload['preview_data_url'])) {
                $this->storeDataUrl($customization, $payload['preview_data_url'], 'preview');
                $this->composeMockupPreview($customization->fresh(), $variant);
            }

            if (! empty($payload['print_data_url']) && is_string($payload['print_data_url'])) {
                $this->storeDataUrl($customization, $payload['print_data_url'], 'print');
            }

            return $customization->fresh(['files', 'variant', 'product']);
        });
    }

    public function finalize(ProductCustomization $customization): ProductCustomization
    {
        if (! $customization->print_file_path && ! $customization->upload_path) {
            throw new InvalidArgumentException('A print-ready or uploaded design file is required before finalizing.');
        }

        $customization->update([
            'status' => ProductCustomization::STATUS_FINALIZED,
            'customization_fee' => $this->feeAmount(),
        ]);

        return $customization->fresh(['files', 'variant', 'product']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function resolveVariant(PrintfulProduct $product, array $payload): PrintfulVariant
    {
        $variant = PrintfulVariantOptions::findVariant(
            $product,
            isset($payload['color']) ? (string) $payload['color'] : null,
            isset($payload['size']) ? (string) $payload['size'] : null,
            isset($payload['printful_variant_id']) ? (int) $payload['printful_variant_id'] : null,
        );

        if (! $variant) {
            throw new InvalidArgumentException('Selected color/size combination is not available for this product.');
        }

        return $variant;
    }

    private function storeUpload(ProductCustomization $customization, UploadedFile $upload): void
    {
        $path = $upload->store('customizations/'.$customization->uuid.'/uploads', 'public');

        $width = null;
        $height = null;
        try {
            $size = @getimagesize($upload->getRealPath());
            if (is_array($size)) {
                $width = $size[0] ?? null;
                $height = $size[1] ?? null;
            }
        } catch (Throwable) {
            // ignore
        }

        if ($customization->upload_path) {
            Storage::disk('public')->delete($customization->upload_path);
        }

        $customization->update(['upload_path' => $path]);

        ProductCustomizationFile::query()->create([
            'product_customization_id' => $customization->id,
            'type' => 'upload',
            'path' => $path,
            'disk' => 'public',
            'original_name' => $upload->getClientOriginalName(),
            'mime' => $upload->getMimeType(),
            'width' => $width,
            'height' => $height,
            'bytes' => $upload->getSize(),
        ]);
    }

    private function storeDataUrl(ProductCustomization $customization, string $dataUrl, string $type): void
    {
        if (! preg_match('#^data:image/(png|jpeg|jpg);base64,#i', $dataUrl)) {
            throw new InvalidArgumentException('Invalid image data for '.$type.'.');
        }

        $binary = base64_decode(Str::after($dataUrl, 'base64,'), true);
        if ($binary === false || $binary === '') {
            throw new InvalidArgumentException('Could not decode '.$type.' image.');
        }

        $ext = str_contains(strtolower($dataUrl), 'jpeg') || str_contains(strtolower($dataUrl), 'jpg')
            ? 'jpg'
            : 'png';

        $relative = 'customizations/'.$customization->uuid.'/'.$type.'.'.$ext;
        $column = $type === 'preview' ? 'preview_path' : 'print_file_path';
        $previous = $customization->{$column};

        Storage::disk('public')->put($relative, $binary);

        // Only remove a prior file when the path actually changed — deleting the
        // same path after put() wipes the preview we just saved (e.g. re-finalize).
        if ($previous && $previous !== $relative) {
            Storage::disk('public')->delete($previous);
        }

        $customization->update([$column => $relative]);

        ProductCustomizationFile::query()->create([
            'product_customization_id' => $customization->id,
            'type' => $type,
            'path' => $relative,
            'disk' => 'public',
            'mime' => $ext === 'jpg' ? 'image/jpeg' : 'image/png',
            'bytes' => strlen($binary),
        ]);
    }

    /**
     * Overlay the design layer onto the Printful variant mockup for cart/checkout thumbs.
     */
    private function composeMockupPreview(ProductCustomization $customization, PrintfulVariant $variant): void
    {
        if (! $customization->preview_path || ! function_exists('imagecreatetruecolor')) {
            return;
        }

        $thumbUrl = $variant->thumbnail_url ?: $customization->product?->thumbnail_url;
        if (! $thumbUrl || ! Storage::disk('public')->exists($customization->preview_path)) {
            return;
        }

        try {
            $designBinary = Storage::disk('public')->get($customization->preview_path);
            $design = @imagecreatefromstring($designBinary);
            if ($design === false) {
                return;
            }

            $response = Http::timeout(20)->withHeaders([
                'User-Agent' => 'REAP433-Customizer/1.0',
            ])->get($thumbUrl);

            if (! $response->successful()) {
                imagedestroy($design);

                return;
            }

            $bg = @imagecreatefromstring($response->body());
            if ($bg === false) {
                imagedestroy($design);

                return;
            }

            $width = 500;
            $height = 560;
            $out = imagecreatetruecolor($width, $height);
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
            imagefilledrectangle($out, 0, 0, $width, $height, $transparent);
            imagealphablending($out, true);

            $fill = imagecolorallocate($out, 26, 26, 26);
            imagefilledrectangle($out, 0, 0, $width, $height, $fill);

            $bgW = imagesx($bg);
            $bgH = imagesy($bg);
            $scale = min($width / $bgW, $height / $bgH);
            $drawW = (int) max(1, round($bgW * $scale));
            $drawH = (int) max(1, round($bgH * $scale));
            $drawX = (int) (($width - $drawW) / 2);
            $drawY = (int) (($height - $drawH) / 2);
            imagecopyresampled($out, $bg, $drawX, $drawY, 0, 0, $drawW, $drawH, $bgW, $bgH);

            imagealphablending($design, true);
            imagesavealpha($design, true);
            $designW = imagesx($design);
            $designH = imagesy($design);
            imagecopyresampled($out, $design, 0, 0, 0, 0, $width, $height, $designW, $designH);

            ob_start();
            imagesavealpha($out, true);
            imagepng($out, null, 6);
            $png = ob_get_clean();

            imagedestroy($out);
            imagedestroy($bg);
            imagedestroy($design);

            if (is_string($png) && $png !== '') {
                $path = 'customizations/'.$customization->uuid.'/preview.png';
                Storage::disk('public')->put($path, $png);
                if ($customization->preview_path !== $path) {
                    Storage::disk('public')->delete($customization->preview_path);
                    $customization->update(['preview_path' => $path]);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * @return array{width: int, height: int, top: int, left: int, unit: string}
     */
    private function defaultPrintArea(): array
    {
        return [
            'width' => 300,
            'height' => 360,
            'top' => 80,
            'left' => 100,
            'unit' => 'px',
        ];
    }
}
