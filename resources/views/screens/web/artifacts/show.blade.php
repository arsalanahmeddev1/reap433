@extends('layouts.web.master')
@section('title', $product->name)
@section('content')
<main id="main">
    <section class="shop-section section-pad" aria-labelledby="product-title">
        <div class="container">
            <a href="{{ route('home') }}#shop" class="product-detail-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15,18 9,12 15,6"/></svg>
                Back to collection
            </a>

            <div
                id="product-detail-root"
                class="product-detail product-detail-layout"
                data-scroll-reveal
                @if ($isVariable) data-matrix='@json($matrixPayload)' @endif
            >
                <div class="product-detail-gallery">
                    <img
                        id="product-main-image"
                        src="{{ $defaultMainImage }}"
                        alt="REAP433 {{ $product->name }}"
                        loading="eager"
                    />
                    @if (count($galleryImageUrls) > 1)
                        <div class="product-detail-thumbs" role="list" aria-label="Product images">
                            @foreach ($galleryImageUrls as $url)
                                <img
                                    src="{{ $url }}"
                                    alt=""
                                    role="listitem"
                                    data-main-src="{{ $url }}"
                                    loading="lazy"
                                />
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="product-detail-info">
                    @if ($product->category)
                        <span class="product-category">{{ $product->category->name }}</span>
                    @endif
                    @if ($product->productType)
                        <span class="product-type product-type-detail">{{ $product->productType->name }}</span>
                    @endif
                    <h1 class="product-detail-title" id="product-title">{{ $product->name }}</h1>
                    <p
                        class="product-detail-price"
                        id="product-detail-price"
                        data-default-price="{{ $product->listingPriceLabel() }}"
                    >{{ $product->listingPriceLabel() }}</p>

                    @if ($isVariable)
                        @php
                            $attributeGroups = $matrixPayload['attributeGroups'] ?? [];
                            $hasColorSizeSelects = ! empty($attributeGroups['colors']) && ! empty($attributeGroups['sizes']);
                        @endphp
                        <div class="product-variation-matrix" aria-label="Product options">
                            @if ($hasColorSizeSelects)
                                <div class="variation-dimension">
                                    <label class="variation-dimension-label" for="var-color-select">Color</label>
                                    <select id="var-color-select" class="variation-select" data-dimension-id="{{ $attributeGroups['colorAttrId'] }}">
                                        <option value="">Choose color</option>
                                        @foreach ($attributeGroups['colors'] as $color)
                                            <option value="{{ $color['value'] }}">{{ $color['value'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="variation-dimension">
                                    <label class="variation-dimension-label" for="var-size-select">Size</label>
                                    <select id="var-size-select" class="variation-select" data-dimension-id="{{ $attributeGroups['sizeAttrId'] }}" disabled>
                                        <option value="">Choose size</option>
                                        @foreach ($attributeGroups['sizes'] as $size)
                                            <option value="{{ $size['value'] }}">{{ $size['value'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                @foreach ($matrixPayload['dimensions'] as $dimension)
                                    @php
                                        $values = collect($matrixPayload['variations'])
                                            ->map(fn ($v) => $v['options'][(string) $dimension['id']] ?? null)
                                            ->filter(fn ($v) => $v !== null && trim((string) $v) !== '')
                                            ->unique()
                                            ->values();
                                    @endphp
                                    <div class="variation-dimension" data-dimension-id="{{ $dimension['id'] }}">
                                        <label class="variation-dimension-label" for="var-dim-{{ $dimension['id'] }}">{{ $dimension['name'] }}</label>
                                        <select id="var-dim-{{ $dimension['id'] }}" class="variation-select" data-dimension-id="{{ $dimension['id'] }}">
                                            <option value="">Choose {{ strtolower($dimension['name']) }}</option>
                                            @foreach ($values as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            @endif
                            <p class="variation-hint" id="variation-hint">Select color and size to see price</p>
                        </div>
                        <input type="hidden" id="selected-variation-id" name="product_variation_id" value="">
                    @endif

                    @if ($product->description)
                        <p class="product-detail-desc">{{ $product->description }}</p>
                    @endif
                    <button
                        type="button"
                        id="product-add-to-cart"
                        class="btn btn-gold add-to-cart"
                        data-id="{{ $product->id }}"
                        @if ($isVariable) disabled @endif
                        aria-label="Add {{ $product->name }} to cart"
                    >
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/artifact-product.js') }}"></script>
@endpush
