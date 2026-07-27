@extends('layouts.profile')
@section('title', __('Favourite Products'))

@section('profile_heading', __('Favourite Products'))
@section('profile_subheading', __('Products you have saved for later.'))

@section('profile_content')
    <section class="profile-panel" aria-labelledby="profile-favourites-title">
        <header class="profile-panel-header">
            <h2 class="profile-panel-title" id="profile-favourites-title">{{ __('Your favourites') }}</h2>
            <p class="profile-panel-sub">{{ __('Manage products you have marked as favourites.') }}</p>
        </header>

        @if (session('success'))
            <p class="profile-flash profile-flash--success" data-profile-flash>{{ session('success') }}</p>
        @endif

        @if ($favourites->isNotEmpty())
            <div class="profile-favourites-table-wrap">
                <table class="profile-favourites-table">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Product Image') }}</th>
                            <th scope="col">{{ __('Product Name') }}</th>
                            <th scope="col">{{ __('Price') }}</th>
                            <th scope="col">{{ __('Date Added') }}</th>
                            <th scope="col">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($favourites as $favourite)
                            @php
                                $product = $favourite->product;
                                $imageUrl = $product?->thumbnail_url
                                    ?: 'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=600&q=80&auto=format&fit=crop';
                                $retailMinPrice = $product?->variants
                                    ?->whereNotNull('retail_price')
                                    ?->min('retail_price');
                                $minPrice = wholesaler_product_price($retailMinPrice);
                                $currency = strtoupper($product?->variants?->first()?->currency ?? 'USD');
                            @endphp
                            <tr>
                                <td data-label="{{ __('Product Image') }}">
                                    @if ($product)
                                        <a href="{{ route('printful-products.show', $product) }}" class="profile-favourite-thumb">
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                                        </a>
                                    @else
                                        <span class="profile-favourite-thumb profile-favourite-thumb--empty" aria-hidden="true">—</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('Product Name') }}">
                                    @if ($product)
                                        <a href="{{ route('printful-products.show', $product) }}" class="profile-favourite-name">{{ $product->name }}</a>
                                    @else
                                        <span>{{ __('Product unavailable') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('Price') }}">
                                    @if ($minPrice !== null)
                                        {{ $currency }} {{ number_format((float) $minPrice, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td data-label="{{ __('Date Added') }}">
                                    {{ $favourite->created_at?->format('M j, Y') }}
                                </td>
                                <td data-label="{{ __('Actions') }}">
                                    <div class="profile-address-actions">
                                        @if ($product)
                                            <a href="{{ route('printful-products.show', $product) }}" class="profile-address-action">{{ __('View Product') }}</a>
                                            <form method="post" action="{{ route('favourites.destroy', $product) }}" onsubmit="return confirm(@json(__('Remove this product from favourites?')))">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="profile-address-action profile-address-action--danger">{{ __('Remove') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($favourites->hasPages())
                <div class="printful-products-pagination">
                    {{ $favourites->links('vendor.pagination.storefront') }}
                </div>
            @endif
        @else
            <p class="profile-address-empty">{{ __('You have not added any favourite products yet.') }}</p>
            <a href="{{ route('printful-products.index') }}" class="btn btn-gold">{{ __('Browse Products') }}</a>
        @endif
    </section>
@endsection
