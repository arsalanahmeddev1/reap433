<?php

namespace App\Models;

use App\Support\UniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CollectionPage extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'faqs' => 'array',
    ];

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category');
    }

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'collection_page_category',
            'collection_page_id',
            'product_category_id'
        )->withTimestamps();
    }

    public function uncategorizedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            PrintfulProduct::class,
            'collection_page_printful_product',
            'collection_page_id',
            'printful_product_id'
        )->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function slugFromTitle(string $title, ?int $ignoreId = null): string
    {
        return UniqueSlug::generate(self::class, 'slug', $title, $ignoreId);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * @return list<array{question: string, answer: string}>
     */
    public function normalizedFaqs(): array
    {
        if (! is_array($this->faqs)) {
            return [];
        }

        $faqs = [];

        foreach ($this->faqs as $faq) {
            $question = trim((string) ($faq['question'] ?? ''));
            $answer = trim((string) ($faq['answer'] ?? ''));

            if ($question === '' && $answer === '') {
                continue;
            }

            $faqs[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $faqs;
    }
}
