@extends('layouts.web.master')

@section('title', $collectionPage->seo_title ?: $collectionPage->title)

@push('meta')
    @if ($collectionPage->seo_description)
        <meta name="description" content="{{ $collectionPage->seo_description }}">
    @elseif ($collectionPage->description)
        <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($collectionPage->description), 160) }}">
    @endif
@endpush

@section('content')
<main id="main" class="printful-products-page collection-page">
    <section class="section-pad">
        <div class="container printful-products-container">
            <div class="printful-products-header">
                @if ($collectionPage->productCategory)
                    <span class="section-eyebrow">{{ $collectionPage->productCategory->name }}</span>
                @else
                    <span class="section-eyebrow">{{ __('Collection') }}</span>
                @endif

                <h1 class="printful-products-title">{{ $collectionPage->title }}</h1>

                @if ($collectionPage->description)
                    <div class="printful-products-subtitle collection-page-description">
                        {!! $collectionPage->description !!}
                    </div>
                @endif
            </div>

            @if ($products->isEmpty())
                <div class="printful-products-empty">
                    <p>{{ __('No products found in this collection yet.') }}</p>
                    <p class="printful-products-empty-hint">
                        <a href="{{ route('printful-products.index') }}" class="btn btn-gold-sm">{{ __('Browse all products') }}</a>
                    </p>
                </div>
            @else
                <div class="printful-products-grid">
                    @foreach ($products as $product)
                        <article class="printful-product-card">
                            <div class="printful-product-card__image-wrap">
                                @include('screens.web.partials.favourite-button', [
                                    'product' => $product,
                                    'class' => 'favourite-btn--on-card',
                                    'isFavourite' => (bool) ($product->is_favourite ?? false),
                                ])
                                @if ($product->thumbnail_url)
                                    <img
                                        src="{{ $product->thumbnail_url }}"
                                        alt="{{ $product->name }}"
                                        class="printful-product-card__image"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="printful-product-card__placeholder" aria-hidden="true">No image</div>
                                @endif
                            </div>

                            <div class="printful-product-card__body">
                                <h2 class="printful-product-card__name">{{ $product->name }}</h2>
                                <p class="printful-product-card__meta">
                                    {{ $product->variants_count }} {{ Str::plural('variant', $product->variants_count) }}
                                </p>
                                <a href="{{ route('printful-products.show', $product->id) }}" class="printful-product-card__btn">
                                    View details
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if (method_exists($products, 'hasPages') && $products->hasPages())
                    <div class="printful-products-pagination">
                        {{ $products->links('vendor.pagination.storefront') }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</main>

<style>
    .printful-products-page .printful-products-container {
        max-width: var(--container-max);
        margin: 0 auto;
        padding: 0 var(--container-pad);
    }

    .printful-products-header {
        margin-bottom: var(--space-2xl);
        text-align: center;
    }

    .printful-products-title {
        font-family: var(--font-display);
        font-size: clamp(2rem, 4vw, 3rem);
        color: var(--c-text-primary);
        margin: var(--space-sm) 0;
    }

    .printful-products-subtitle {
        color: var(--c-text-secondary);
        max-width: 560px;
        margin: 0 auto;
    }

    .collection-page-description {
        max-width: 640px;
        line-height: 1.6;
    }

    .collection-page-description p {
        margin: 0 0 0.75rem;
    }

    .collection-page-description p:last-child {
        margin-bottom: 0;
    }

    .collection-page-description img {
        max-width: 100%;
        height: auto;
        border-radius: var(--radius-sm);
    }

    .printful-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: var(--space-lg);
    }

    .printful-product-card {
        background: var(--c-black-soft);
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform var(--t-fast), box-shadow var(--t-fast);
    }

    .printful-product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .printful-product-card__image-wrap {
        position: relative;
        aspect-ratio: 1;
        background: #fff;
        overflow: hidden;
    }

    .printful-product-card__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .printful-product-card__placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-text-muted);
        font-size: 0.875rem;
        background: var(--c-black-mid);
    }

    .printful-product-card__body {
        padding: var(--space-lg);
        display: flex;
        flex-direction: column;
        gap: var(--space-sm);
        flex: 1;
    }

    .printful-product-card__name {
        font-family: var(--font-heading);
        font-size: 1.125rem;
        color: var(--c-text-primary);
        line-height: 1.3;
    }

    .printful-product-card__meta {
        color: var(--c-text-secondary);
        font-size: 0.875rem;
    }

    .printful-product-card__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: auto;
        padding: 0.65rem 1rem;
        border-radius: var(--radius-sm);
        background: var(--c-gold);
        color: var(--c-text-on-gold);
        font-weight: 600;
        text-decoration: none;
        transition: opacity var(--t-fast);
    }

    .printful-product-card__btn:hover {
        opacity: 0.9;
    }

    .printful-products-empty {
        text-align: center;
        padding: var(--space-3xl) var(--space-lg);
        border: 1px dashed var(--c-black-border);
        border-radius: var(--radius-md);
        color: var(--c-text-secondary);
    }

    .printful-products-empty-hint {
        margin-top: var(--space-md);
        font-size: 0.875rem;
    }
</style>
@endsection
