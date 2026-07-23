<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCustomization extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_FINALIZED = 'finalized';

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (blank($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'text_settings' => 'array',
            'image_settings' => 'array',
            'print_area' => 'array',
            'customization_fee' => 'decimal:2',
            'printful_sync_product_id' => 'integer',
            'printful_sync_variant_id' => 'integer',
            'catalog_variant_id' => 'integer',
            'catalog_product_id' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PrintfulProduct::class, 'printful_product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(PrintfulVariant::class, 'printful_variant_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProductCustomizationFile::class);
    }

    public function isFinalized(): bool
    {
        return $this->status === self::STATUS_FINALIZED;
    }

    public function previewUrl(): ?string
    {
        return $this->publicUrlIfExists($this->preview_path);
    }

    public function printFileUrl(): ?string
    {
        return $this->publicUrlIfExists($this->print_file_path);
    }

    public function uploadUrl(): ?string
    {
        return $this->publicUrlIfExists($this->upload_path);
    }

    private function publicUrlIfExists(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        // Relative URL so cart/checkout work on both localhost and 127.0.0.1.
        return '/storage/'.ltrim($path, '/');
    }
}
