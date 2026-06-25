{{-- Storefront search results (artifacts, Printful products, journal) --}}
@extends('layouts.web.master')

@section('title', $query !== '' ? 'Search: '.$query : 'Search')

@section('content')
<main id="main" class="search-results-page">
    <section class="shop-section section-pad" aria-labelledby="search-results-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">Search</span>
                <h1 class="section-title" id="search-results-title">
                    @if ($query !== '')
                        Results for &ldquo;{{ $query }}&rdquo;
                    @else
                        Search the Collection
                    @endif
                </h1>
                @if ($query !== '')
                    <p class="section-sub">
                        {{ $total === 1 ? '1 result found' : $total.' results found' }}
                        across artifacts, merchandise, and journal.
                    </p>
                @else
                    <p class="section-sub">Use the search bar in the header to find products and articles.</p>
                @endif
            </div>

            @if ($query === '')
                <p class="search-results-empty" data-scroll-reveal>Enter a keyword above to start searching.</p>
            @elseif ($total === 0)
                <p class="search-results-empty" data-scroll-reveal>
                    No matches for &ldquo;{{ $query }}&rdquo;. Try a different name or topic.
                </p>
            @else
                @if ($artifacts->isNotEmpty())
                    <div class="search-results-group" data-scroll-reveal>
                        <h2 class="search-results-group-title">Artifacts</h2>
                        <div class="product-grid" role="list" aria-label="Artifact search results">
                            @foreach ($artifacts as $product)
                                @include('screens.web.artifacts.partials.product-card', [
                                    'product' => $product,
                                    'delay' => ($loop->index % 4) * 80,
                                ])
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($printfulProducts->isNotEmpty())
                    <div class="search-results-group" data-scroll-reveal>
                        <h2 class="search-results-group-title">Merchandise</h2>
                        <div class="search-printful-grid" role="list" aria-label="Merchandise search results">
                            @foreach ($printfulProducts as $product)
                                <article class="search-printful-card" role="listitem">
                                    <div class="search-printful-card__image-wrap">
                                        @if ($product->thumbnail_url)
                                            <img
                                                src="{{ $product->thumbnail_url }}"
                                                alt="{{ $product->name }}"
                                                class="search-printful-card__image"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="search-printful-card__placeholder" aria-hidden="true">No image</div>
                                        @endif
                                    </div>
                                    <div class="search-printful-card__body">
                                        <h3 class="search-printful-card__name">{{ $product->name }}</h3>
                                        <p class="search-printful-card__meta">
                                            {{ $product->variants_count }} {{ Str::plural('variant', $product->variants_count) }}
                                        </p>
                                        <a href="{{ route('printful-products.show', $product) }}" class="btn btn-gold-sm">
                                            View details
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($blogs->isNotEmpty())
                    <div class="search-results-group" data-scroll-reveal>
                        <h2 class="search-results-group-title">Journal</h2>
                        <div class="blog-grid search-blog-grid" role="list" aria-label="Journal search results">
                            @foreach ($blogs as $blog)
                                @include('screens.web.home.partials.blog-card', [
                                    'blog' => $blog,
                                    'isFeatured' => false,
                                    'delay' => $loop->index * 80,
                                ])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>
</main>
@endsection
