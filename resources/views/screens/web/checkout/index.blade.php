@extends('layouts.web.master')
@section('title', __('Checkout'))

@section('content')
<main id="main">
    <section class="shop-section section-pad checkout-page" aria-labelledby="checkout-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">{{ __('Checkout') }}</span>
                <h1 class="section-title" id="checkout-title">{{ __('Complete your order') }}</h1>
                <p class="section-sub">{{ __('Choose a delivery address and pay securely.') }}</p>
            </div>

            <div class="cart-layout checkout-layout" data-scroll-reveal>
                <div class="checkout-main">
                    <form id="checkout-form" class="checkout-form" novalidate>
                        @csrf

                        @if ($addresses->isNotEmpty())
                            <div class="checkout-panel">
                                <h2 class="checkout-panel-title">{{ __('Saved addresses') }}</h2>
                                <div class="checkout-address-pick">
                                    @foreach ($addresses as $address)
                                        <label class="checkout-address-option">
                                            <input
                                                type="radio"
                                                name="user_address_id"
                                                value="{{ $address->id }}"
                                                data-address="{{ json_encode($address->only([
                                                    'full_name',
                                                    'phone',
                                                    'street_address',
                                                    'street_address_2',
                                                    'city',
                                                    'state',
                                                    'zipcode',
                                                    'country',
                                                ])) }}"
                                                @checked($loop->first)
                                            />
                                            <span class="checkout-address-option-body">
                                                <strong>{{ $address->full_name }}</strong>
                                                @if ($address->label)
                                                    <em>{{ $address->label }}</em>
                                                @endif
                                                <span>{{ $address->street_address }}@if ($address->street_address_2), {{ $address->street_address_2 }}@endif</span>
                                                <span>{{ $address->city }}@if ($address->state), {{ $address->state }}@endif {{ $address->zipcode }}</span>
                                                @if ($address->is_default)
                                                    <span class="profile-address-badge">{{ __('Default') }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <a href="{{ route('profile.addresses.create') }}" class="checkout-add-address-link">{{ __('Add a new address') }}</a>
                            </div>
                        @else
                            <div class="checkout-panel checkout-panel--notice">
                                <p>{{ __('You have no saved addresses yet.') }}</p>
                                <a href="{{ route('profile.addresses.create') }}" class="btn btn-outline-sm">{{ __('Add address') }}</a>
                            </div>
                        @endif

                        <div class="checkout-panel">
                            <h2 class="checkout-panel-title">{{ __('Delivery details') }}</h2>
                            <div class="checkout-fields">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label" for="billing_name">{{ __('Full name') }}</label>
                                        <input id="billing_name" name="billing_name" type="text" class="form-input" value="{{ old('billing_name', $user->name) }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="billing_email">{{ __('Email') }}</label>
                                        <input id="billing_email" name="billing_email" type="email" class="form-input" value="{{ old('billing_email', $user->email) }}" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="billing_phone">{{ __('Phone') }}</label>
                                    <input id="billing_phone" name="billing_phone" type="text" class="form-input" value="{{ old('billing_phone') }}" required />
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="billing_address">{{ __('Street address') }}</label>
                                    <input id="billing_address" name="billing_address" type="text" class="form-input" value="{{ old('billing_address') }}" required />
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label" for="billing_city">{{ __('City') }}</label>
                                        <input id="billing_city" name="billing_city" type="text" class="form-input" value="{{ old('billing_city') }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="billing_state">{{ __('State') }}</label>
                                        <input id="billing_state" name="billing_state" type="text" class="form-input" value="{{ old('billing_state') }}" required />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label" for="billing_zip">{{ __('Zip code') }}</label>
                                        <input id="billing_zip" name="billing_zip" type="text" class="form-input" value="{{ old('billing_zip') }}" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="billing_country">{{ __('Country') }}</label>
                                        <input id="billing_country" name="billing_country" type="text" class="form-input" value="{{ old('billing_country', 'United States') }}" required />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-panel">
                            <h2 class="checkout-panel-title">{{ __('Payment') }}</h2>
                            @if (config('services.stripe.key'))
                                <label class="form-label" for="card-element">{{ __('Card details') }}</label>
                                <div id="card-element" class="checkout-card-element"></div>
                                <p id="card-errors" class="checkout-card-error" role="alert"></p>
                            @else
                                <p class="checkout-stripe-missing">{{ __('Card payment is not configured yet. Your order will be saved with payment status pending.') }}</p>
                            @endif
                        </div>

                        <p id="checkout-form-error" class="checkout-form-error" role="alert" hidden></p>

                        <button type="submit" class="btn btn-gold btn-full checkout-pay-btn">
                            @if (config('services.stripe.key'))
                                {{ __('Pay') }} {{ '$' . number_format((float) $checkout->total(), 2) }}
                            @else
                                {{ __('Place order') }} {{ '$' . number_format((float) $checkout->total(), 2) }}
                            @endif
                        </button>
                    </form>
                </div>

                <aside class="cart-summary checkout-summary">
                    <h2 class="cart-summary-title">{{ __('Order summary') }}</h2>
                    <ul class="checkout-items">
                        @foreach ($checkout->items as $item)
                            @php
                                $product = $item->product;
                                $image = $product?->images->firstWhere('is_primary', 1) ?? $product?->images->first();
                                $imageUrl = $image?->publicUrl() ?: asset('assets/images/placeholders/img-not-available.png');
                            @endphp
                            <li class="checkout-item">
                                <img src="{{ $imageUrl }}" alt="" class="checkout-item-img" loading="lazy" />
                                <div>
                                    <p class="checkout-item-name">{{ $product?->name }}</p>
                                    <p class="checkout-item-meta">{{ $item->qty }} × {{ '$' . number_format((float) $item->price, 2) }}</p>
                                </div>
                                <strong>{{ '$' . number_format((float) $item->subtotal, 2) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                    <div class="cart-summary-row cart-summary-total">
                        <span>{{ __('Total') }}</span>
                        <strong>{{ '$' . number_format((float) $checkout->total(), 2) }}</strong>
                    </div>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-sm cart-continue-btn">{{ __('Back to cart') }}</a>
                </aside>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    window.__checkout = {
        placeOrderUrl: @json(route('checkout.place-order')),
        paymentIntentUrl: @json(route('checkout.payment-intent')),
        completeUrl: @json(route('checkout.complete')),
        successUrl: @json(url('/checkout/success')),
        stripeKey: @json(config('services.stripe.key')),
        total: @json((float) $checkout->total()),
    };
</script>
@if (config('services.stripe.key'))
    <script src="https://js.stripe.com/v3/"></script>
@endif
<script src="{{ asset('assets/web/js/checkout.js') }}"></script>
@endpush
