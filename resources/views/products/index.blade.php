@extends('layouts.web.master')

@section('title', 'Printful Products')

@section('content')
<main id="main" class="printful-products-page">
    <section class="section-pad">
        <div class="container printful-products-container">
            <div class="printful-products-header">
                <span class="section-eyebrow">Printful Catalog</span>
                <h1 class="printful-products-title">Synced Products</h1>
                <p class="printful-products-subtitle">Products imported from Printful and stored locally.</p>
            </div>

            @if ($products->isEmpty())
                <div class="printful-products-empty">
                    <p>No Printful products synced yet.</p>
                    <p class="printful-products-empty-hint">Run <code>php artisan printful:sync-products</code> to import products.</p>
                </div>
            @else
                <div class="printful-products-grid">
                    @foreach ($products as $product)
                        <article class="printful-product-card">
                            <div class="printful-product-card__image-wrap">
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

                @if ($products->hasPages())
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
        aspect-ratio: 1;
        background: var(--c-black-mid);
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

    .printful-products-empty code {
        background: var(--c-black-mid);
        padding: 0.15rem 0.4rem;
        border-radius: var(--radius-sm);
        color: var(--c-gold);
    }
</style>
@endsection
