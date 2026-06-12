@extends('layouts.web.master')
@section('title', 'Collection')
@section('content')
<main id="main">
    <section class="shop-section section-pad" id="shop" aria-labelledby="shop-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">The Collection</span>
                <h1 class="section-title" id="shop-title">All Premium<br />Merchandise</h1>
                <p class="section-sub">Browse every REAP433 piece — trademarked identity, purpose, and pride.</p>
            </div>

            <div class="product-filters" role="group" aria-label="Filter products">
                <button class="filter-btn active" data-filter="all" aria-pressed="true">All Pieces</button>
                @foreach ($categories as $category)
                    <button class="filter-btn" data-filter="{{ $category->slug }}" aria-pressed="false">{{ $category->name }}</button>
                @endforeach
            </div>

            <div class="product-grid" role="list" aria-label="Products">
                @forelse ($products as $product)
                    @include('screens.web.artifacts.partials.product-card', [
                        'product' => $product,
                        'delay' => ($loop->index % 4) * 80,
                    ])
                @empty
                    <p class="product-grid-empty">No products available yet.</p>
                @endforelse
            </div>

            @if ($products->hasPages())
                <div class="shop-cta-row" data-scroll-reveal>
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
