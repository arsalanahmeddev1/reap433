@extends('layouts.web.master')

@section('title', $product->name)

@section('content')
<main id="main" class="printful-product-detail-page">
    <section class="section-pad">
        <div class="container printful-product-detail-container">
            <a href="{{ route('printful-products.index') }}" class="printful-product-back">&larr; Back to products</a>

            <div class="printful-product-detail">
                <div class="printful-product-detail__media">
                    @if ($product->thumbnail_url)
                        <img
                            src="{{ $product->thumbnail_url }}"
                            alt="{{ $product->name }}"
                            class="printful-product-detail__image"
                        >
                    @else
                        <div class="printful-product-detail__placeholder" aria-hidden="true">No image</div>
                    @endif
                </div>

                <div class="printful-product-detail__info">
                    <h1 class="printful-product-detail__title">{{ $product->name }}</h1>
                    <p class="printful-product-detail__meta">
                        {{ $product->variants->count() }} {{ Str::plural('variant', $product->variants->count()) }}
                    </p>
                </div>
            </div>

            <div class="printful-variants-section">
                <h2 class="printful-variants-title">Choose a variant</h2>

                @if ($product->variants->isEmpty())
                    <p class="printful-variants-empty">No variants available</p>
                @else
                    <div class="printful-variants-list">
                        @foreach ($product->variants as $variant)
                            <article class="printful-variant-row">
                                <div class="printful-variant-row__thumb">
                                    @if ($variant->thumbnail_url)
                                        <img
                                            src="{{ $variant->thumbnail_url }}"
                                            alt="{{ $variant->name ?? 'Variant image' }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="printful-variant-row__placeholder" aria-hidden="true">—</div>
                                    @endif
                                </div>

                                <div class="printful-variant-row__details">
                                    <h3 class="printful-variant-row__name">{{ $variant->name ?? 'Unnamed variant' }}</h3>
                                    <dl class="printful-variant-row__meta">
                                        <div>
                                            <dt>SKU</dt>
                                            <dd>{{ $variant->sku ?? '—' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Price</dt>
                                            <dd>
                                                @if ($variant->retail_price !== null)
                                                    {{ strtoupper($variant->currency ?? 'USD') }}
                                                    {{ number_format((float) $variant->retail_price, 2) }}
                                                @else
                                                    —
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <form action="{{ route('cart.add', $variant) }}" method="POST" class="printful-variant-row__form">
                                    @csrf
                                    <div class="printful-variant-row__qty">
                                        <label for="quantity-{{ $variant->id }}" class="printful-variant-row__qty-label">Qty</label>
                                        <input
                                            type="number"
                                            id="quantity-{{ $variant->id }}"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="99"
                                            class="printful-variant-row__qty-input"
                                            required
                                        >
                                    </div>
                                    <button type="submit" class="btn btn-gold-sm printful-variant-row__add-btn">
                                        Add to Cart
                                    </button>
                                </form>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
    .printful-product-detail-page .printful-product-detail-container {
        max-width: var(--container-max);
        margin: 0 auto;
        padding: 0 var(--container-pad);
    }

    .printful-product-back {
        display: inline-block;
        margin-bottom: var(--space-xl);
        color: var(--c-text-secondary);
        text-decoration: none;
        transition: color var(--t-fast);
    }

    .printful-product-back:hover {
        color: var(--c-gold);
    }

    .printful-product-detail {
        display: grid;
        grid-template-columns: minmax(280px, 420px) 1fr;
        gap: var(--space-2xl);
        align-items: start;
        margin-bottom: var(--space-3xl);
    }

    .printful-product-detail__media {
        background: var(--c-black-soft);
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .printful-product-detail__image {
        width: 100%;
        display: block;
        aspect-ratio: 1;
        object-fit: cover;
    }

    .printful-product-detail__placeholder {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-text-muted);
        background: var(--c-black-mid);
    }

    .printful-product-detail__title {
        font-family: var(--font-display);
        font-size: clamp(2rem, 4vw, 2.75rem);
        color: var(--c-text-primary);
        margin-bottom: var(--space-md);
    }

    .printful-product-detail__meta {
        color: var(--c-text-secondary);
    }

    .printful-variants-title {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        color: var(--c-text-primary);
        margin-bottom: var(--space-lg);
    }

    .printful-variants-empty {
        color: var(--c-text-secondary);
    }

    .printful-variants-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
    }

    .printful-variant-row {
        display: grid;
        grid-template-columns: 80px 1fr auto;
        gap: var(--space-lg);
        align-items: center;
        padding: var(--space-lg);
        background: var(--c-black-soft);
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-md);
    }

    .printful-variant-row__thumb img,
    .printful-variant-row__placeholder {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        display: block;
    }

    .printful-variant-row__placeholder {
        background: var(--c-black-mid);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-text-muted);
    }

    .printful-variant-row__name {
        font-family: var(--font-heading);
        font-size: 1rem;
        color: var(--c-text-primary);
        margin-bottom: var(--space-sm);
    }

    .printful-variant-row__meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-lg);
    }

    .printful-variant-row__meta div {
        min-width: 120px;
    }

    .printful-variant-row__meta dt {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--c-text-muted);
        margin-bottom: 0.15rem;
    }

    .printful-variant-row__meta dd {
        color: var(--c-text-secondary);
        margin: 0;
    }

    .printful-variant-row__form {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: var(--space-sm);
        min-width: 140px;
    }

    .printful-variant-row__qty {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .printful-variant-row__qty-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--c-text-muted);
    }

    .printful-variant-row__qty-input {
        width: 100%;
        padding: 0.5rem 0.65rem;
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-sm);
        background: var(--c-black-mid);
        color: var(--c-text-primary);
        text-align: center;
        font-family: var(--font-body);
    }

    .printful-variant-row__qty-input:focus {
        outline: none;
        border-color: var(--c-gold);
        box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.15);
    }

    .printful-variant-row__add-btn {
        width: 100%;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .printful-product-detail {
            grid-template-columns: 1fr;
        }

        .printful-variant-row {
            grid-template-columns: 64px 1fr;
            grid-template-areas:
                "thumb details"
                "form form";
        }

        .printful-variant-row__thumb {
            grid-area: thumb;
        }

        .printful-variant-row__details {
            grid-area: details;
        }

        .printful-variant-row__form {
            grid-area: form;
            flex-direction: row;
            align-items: flex-end;
            min-width: 0;
        }

        .printful-variant-row__qty {
            flex: 1;
        }

        .printful-variant-row__add-btn {
            flex: 1;
        }

        .printful-variant-row__thumb img,
        .printful-variant-row__placeholder {
            width: 64px;
            height: 64px;
        }
    }
</style>
@endpush
