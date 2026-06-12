@php
    $primaryImage = $product->images->firstWhere('is_primary', 1) ?? $product->images->first();
    $imageUrl = $primaryImage?->publicUrl() ?: 'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=600&q=80&auto=format&fit=crop';
    $categorySlug = $product->category?->slug ?? '';
@endphp
<article class="product-card{{ $product->isVariable() ? ' product-card--variable' : '' }}" role="listitem" data-category="{{ $categorySlug }}" data-scroll-reveal data-delay="{{ ($delay ?? 0) }}">
    <div class="product-image-wrap">
        <img
            src="{{ $imageUrl }}"
            alt="REAP433 {{ $product->name }}"
            class="product-image"
            loading="lazy"
        />
        <div class="product-overlay" aria-hidden="true">
            <a href="{{ route('artifacts.show', $product) }}" class="product-quick-view" aria-label="Quick view {{ $product->name }}">Quick View</a>
        </div>
    </div>
    <div class="product-info">
        <div class="product-meta">
            <span class="product-category">{{ $product->category?->name }}</span>
            @if ($product->productType)
                <span class="product-type">{{ $product->productType->name }}</span>
            @endif
        </div>
        <h3 class="product-name">{{ $product->name }}</h3>
        <p class="product-desc">{{ $product->description }}</p>
        <div class="product-footer">
            <span class="product-price">
                @if ($product->isVariable() && $product->from_price !== null)
                    From ${{ number_format((float) $product->from_price, 2) }}
                @else
                    {{ $product->listingPriceLabel() }}
                @endif
            </span>
            @if ($product->isVariable())
                <a href="{{ route('artifacts.show', $product) }}" class="btn btn-gold-sm" aria-label="Select options for {{ $product->name }}">Select Options</a>
            @else
                <button type="button" class="btn btn-gold-sm add-to-cart" data-id="{{ $product->id }}" aria-label="Add {{ $product->name }} to cart">Add to Cart</button>
            @endif
        </div>
    </div>
</article>
