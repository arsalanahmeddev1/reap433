@extends('layouts.web.master')

@section('title', $collectionPage->seo_title ?: $collectionPage->title)

@php
    $pageMetaDescription = filled($collectionPage->seo_description)
        ? (string) $collectionPage->seo_description
        : (filled($collectionPage->description)
            ? \Illuminate\Support\Str::limit(strip_tags($collectionPage->description), 160)
            : '');
@endphp

@section('meta_description', $pageMetaDescription)

@push('meta')
@endpush

@php
    $faqs = $collectionPage->normalizedFaqs();
    $categoryNames = $collectionPage->productCategories->pluck('name')->filter();
@endphp

@section('content')
<main id="main" class="printful-products-page collection-page">
    <section class="section-pad">
        <div class="container printful-products-container">
            <div class="printful-products-header">
                @if ($categoryNames->isNotEmpty())
                    <span class="section-eyebrow">{{ $categoryNames->join(' · ') }}</span>
                @else
                    <span class="section-eyebrow">{{ __('Collection') }}</span>
                @endif

                <h1 class="printful-products-title">{{ $collectionPage->title }}</h1>
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

            @if ($collectionPage->description)
                <div class="collection-page-content">
                    <div class="collection-page-description">
                        {!! $collectionPage->description !!}
                    </div>
                </div>
            @endif

            @if (count($faqs) > 0)
                <div class="collection-page-faqs">
                    <h2 class="collection-page-faqs-heading">Frequently Asked Questions</h2>
                    <div class="collection-faq-accordion" id="collectionFaqAccordion">
                        @foreach ($faqs as $index => $faq)
                            @php
                                $question = trim((string) ($faq['question'] ?? ''));
                                $answer = trim((string) ($faq['answer'] ?? ''));
                                $itemId = 'collection-faq-' . $index;
                            @endphp
                            @if ($question !== '' || $answer !== '')
                                <div class="collection-faq-item{{ $index === 0 ? ' is-open' : '' }}">
                                    <button
                                        type="button"
                                        class="collection-faq-trigger"
                                        id="{{ $itemId }}-trigger"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="{{ $itemId }}-panel"
                                        data-faq-trigger
                                    >
                                        <h3 class="collection-faq-question">{{ $question !== '' ? $question : __('FAQ') }}</h3>
                                        <span class="collection-faq-icon" aria-hidden="true">
                                            <svg class="collection-faq-icon-plus" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                            <svg class="collection-faq-icon-minus" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </span>
                                    </button>
                                    <div
                                        class="collection-faq-panel"
                                        id="{{ $itemId }}-panel"
                                        role="region"
                                        aria-labelledby="{{ $itemId }}-trigger"
                                        @if ($index !== 0) hidden @endif
                                    >
                                        <div class="collection-faq-answer">{{ $answer }}</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>

@push('scripts')
<script>
(function () {
    var root = document.getElementById('collectionFaqAccordion');
    if (!root) return;

    root.querySelectorAll('[data-faq-trigger]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.collection-faq-item');
            var panel = item ? item.querySelector('.collection-faq-panel') : null;
            if (!item || !panel) return;

            var willOpen = !item.classList.contains('is-open');

            root.querySelectorAll('.collection-faq-item.is-open').forEach(function (openItem) {
                openItem.classList.remove('is-open');
                var openBtn = openItem.querySelector('[data-faq-trigger]');
                var openPanel = openItem.querySelector('.collection-faq-panel');
                if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
                if (openPanel) openPanel.hidden = true;
            });

            if (willOpen) {
                item.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
                panel.hidden = false;
            }
        });
    });
}());
</script>
@endpush

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

    .collection-page-content {
        margin-top: var(--space-3xl);
        max-width: 720px;
        margin-left: auto;
        margin-right: auto;
    }

    .collection-page-description {
        color: var(--c-text-secondary);
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

    .collection-page-faqs {
        margin-top: var(--space-3xl);
        max-width: 760px;
        margin-left: auto;
        margin-right: auto;
    }

    .collection-page-faqs-heading {
        font-family: var(--font-display);
        font-size: clamp(1.75rem, 3.5vw, 2.35rem);
        color: var(--c-text-primary);
        text-align: center;
        margin: 0 0 var(--space-xl);
    }

    .collection-faq-accordion {
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-md);
        overflow: hidden;
        background: var(--c-black-soft);
    }

    .collection-faq-item + .collection-faq-item {
        border-top: 1px solid var(--c-black-border);
    }

    .collection-faq-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--space-md);
        padding: 1.1rem 1.25rem;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-align: left;
        color: var(--c-text-primary);
        transition: background var(--t-fast);
    }

    .collection-faq-trigger:hover {
        background: rgba(191, 136, 52, 0.08);
    }

    .collection-faq-question {
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0;
        color: var(--c-text-primary);
        flex: 1;
    }

    .collection-faq-icon {
        flex: 0 0 auto;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        border: 1px solid rgba(191, 136, 52, 0.45);
        color: var(--c-gold);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(191, 136, 52, 0.1);
    }

    .collection-faq-icon-minus {
        display: none;
    }

    .collection-faq-item.is-open .collection-faq-icon-plus {
        display: none;
    }

    .collection-faq-item.is-open .collection-faq-icon-minus {
        display: block;
    }

    .collection-faq-item.is-open .collection-faq-trigger {
        background: rgba(191, 136, 52, 0.1);
    }

    .collection-faq-panel {
        padding: 15px;
    }

    .collection-faq-answer {
        color: var(--c-text-secondary);
        line-height: 1.7;
        white-space: pre-wrap;
        font-size: 0.98rem;
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

    .printful-products-pagination {
        margin-top: var(--space-2xl);
    }
</style>
@endsection
