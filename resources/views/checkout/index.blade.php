@extends('layouts.web.master')

@section('title', 'Checkout')

@section('content')
<main id="main" class="checkout-index-page">
    <section class="shop-section section-pad checkout-page" aria-labelledby="checkout-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">Checkout</span>
                <h1 class="section-title" id="checkout-title">Complete Your Order</h1>
                <p class="section-sub">Review your cart and enter your details.</p>
            </div>

            @if (session('success'))
                <div class="checkout-flash checkout-flash--success" role="status">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="checkout-flash checkout-flash--error" role="alert">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="checkout-flash checkout-flash--error" role="alert">
                    <p>Please fix the errors below and try again.</p>
                </div>
            @endif

            <div class="cart-layout checkout-index-layout" data-scroll-reveal>
                <div class="checkout-index-main">
                    @guest
                        <div class="checkout-panel checkout-login-panel">
                            <h2 class="checkout-panel-title">Sign in required</h2>
                            <p class="checkout-login-message">Please log in or create an account to place your order.</p>
                            <div class="checkout-login-actions">
                                <a href="{{ route('login') }}" class="btn btn-gold">Log in</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-sm">Create account</a>
                            </div>
                        </div>
                    @else
                    <form action="{{ route('checkout.store') }}" method="POST" class="checkout-index-form" id="checkout-form" novalidate>
                        @csrf

                        <div class="checkout-panel">
                            <h2 class="checkout-panel-title">Customer Information</h2>
                            <div class="checkout-field-grid">
                                <div class="checkout-field checkout-field--full">
                                    <label for="full_name">Full name</label>
                                    <input
                                        type="text"
                                        id="full_name"
                                        name="full_name"
                                        value="{{ old('full_name', auth()->user()->name) }}"
                                        required
                                    >
                                    @error('full_name')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="email">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', auth()->user()->email) }}"
                                        readonly
                                        required
                                    >
                                    @error('email')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="phone">Phone <span class="checkout-optional">(optional)</span></label>
                                    <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                    >
                                    @error('phone')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="checkout-panel">
                            <h2 class="checkout-panel-title">Shipping Address</h2>
                            <div class="checkout-field-grid">
                                <div class="checkout-field checkout-field--full">
                                    <label for="address1">Address line 1</label>
                                    <input
                                        type="text"
                                        id="address1"
                                        name="address1"
                                        value="{{ old('address1') }}"
                                        required
                                    >
                                    @error('address1')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field checkout-field--full">
                                    <label for="address2">Address line 2 <span class="checkout-optional">(optional)</span></label>
                                    <input
                                        type="text"
                                        id="address2"
                                        name="address2"
                                        value="{{ old('address2') }}"
                                    >
                                    @error('address2')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="city">City</label>
                                    <input
                                        type="text"
                                        id="city"
                                        name="city"
                                        value="{{ old('city') }}"
                                        required
                                    >
                                    @error('city')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="state_code">State</label>
                                    <input
                                        type="text"
                                        id="state_code"
                                        name="state_code"
                                        value="{{ old('state_code') }}"
                                        maxlength="2"
                                        placeholder="CA"
                                        pattern="[A-Za-z]{2}"
                                        title="Use a 2-letter US state code, e.g. CA or NY"
                                        required
                                    >
                                    @error('state_code')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="country_code">Country</label>
                                    <input
                                        type="text"
                                        id="country_code_display"
                                        value="United States"
                                        readonly
                                    >
                                    <input type="hidden" name="country_code" value="US">
                                    @error('country_code')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="checkout-field">
                                    <label for="zip">ZIP / Postal code</label>
                                    <input
                                        type="text"
                                        id="zip"
                                        name="zip"
                                        value="{{ old('zip') }}"
                                        required
                                    >
                                    @error('zip')
                                        <span class="checkout-field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="checkout-panel">
                            <h2 class="checkout-panel-title">Payment</h2>
                            @if ($stripeEnabled)
                                <label class="checkout-card-label" for="card-element">Card details</label>
                                <div id="card-element" class="checkout-card-element"></div>
                                <p id="card-errors" class="checkout-field-error" role="alert"></p>
                            @else
                                <p class="checkout-stripe-missing">Card payment is not configured yet. Your order will be saved with payment status pending.</p>
                            @endif
                        </div>

                        <p id="checkout-form-error" class="checkout-field-error checkout-form-error" role="alert" hidden></p>

                        <button type="submit" class="btn btn-gold checkout-index-submit checkout-pay-btn">
                            @if ($stripeEnabled)
                                Pay {{ '$' . number_format((float) $subtotal, 2) }}
                            @else
                                Place Order
                            @endif
                        </button>
                    </form>
                    @endguest
                </div>

                <aside class="cart-summary checkout-index-summary">
                    <h2 class="cart-summary-title">Order Summary</h2>

                    <ul class="checkout-summary-items">
                        @foreach ($items as $item)
                            @php
                                $imageUrl = $item['variant_thumbnail_url']
                                    ?? $item['product_thumbnail_url']
                                    ?? asset('assets/images/placeholders/img-not-available.png');
                                $lineTotal = (float) $item['price'] * (int) $item['quantity'];
                                $currency = strtoupper($item['currency'] ?? 'USD');
                            @endphp
                            <li class="checkout-summary-item">
                                <img src="{{ $imageUrl }}" alt="" class="checkout-summary-item__thumb" loading="lazy">
                                <div class="checkout-summary-item__details">
                                    <strong>{{ $item['product_name'] }}</strong>
                                    @if ($item['variant_name'])
                                        <span>{{ $item['variant_name'] }}</span>
                                    @endif
                                    <span>Qty {{ $item['quantity'] }} × {{ $currency }} {{ number_format((float) $item['price'], 2) }}</span>
                                </div>
                                <strong class="checkout-summary-item__total">
                                    {{ $currency }} {{ number_format($lineTotal, 2) }}
                                </strong>
                            </li>
                        @endforeach
                    </ul>

                    <div class="cart-summary-row cart-summary-total">
                        <span>Subtotal</span>
                        <strong>{{ '$' . number_format((float) $subtotal, 2) }}</strong>
                    </div>

                    <a href="{{ route('cart.index') }}" class="btn btn-outline-sm checkout-index-back">Back to Cart</a>
                </aside>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
    .checkout-flash {
        margin-bottom: var(--space-lg);
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        font-size: 0.9375rem;
    }

    .checkout-flash--success {
        background: rgba(74, 222, 128, 0.12);
        border: 1px solid rgba(74, 222, 128, 0.35);
        color: var(--c-text-primary);
    }

    .checkout-flash--error {
        background: rgba(248, 113, 113, 0.12);
        border: 1px solid rgba(248, 113, 113, 0.35);
        color: var(--c-text-primary);
    }

    .checkout-index-layout {
        align-items: start;
    }

    .checkout-index-form {
        display: flex;
        flex-direction: column;
        gap: var(--space-lg);
    }

    .checkout-login-panel {
        text-align: center;
    }

    .checkout-login-message {
        color: var(--c-text-secondary);
        margin-bottom: var(--space-lg);
    }

    .checkout-login-actions {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-md);
        justify-content: center;
    }

    .checkout-panel {
        background: var(--c-black-soft);
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-md);
        padding: var(--space-lg);
    }

    .checkout-panel-title {
        font-family: var(--font-heading);
        font-size: 1.125rem;
        color: var(--c-text-primary);
        margin-bottom: var(--space-lg);
    }

    .checkout-field-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: var(--space-md);
    }

    .checkout-field--full {
        grid-column: 1 / -1;
    }

    .checkout-field label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.8125rem;
        color: var(--c-text-secondary);
    }

    .checkout-optional {
        color: var(--c-text-muted);
        font-weight: 400;
    }

    .checkout-field input {
        width: 100%;
        padding: 0.65rem 0.75rem;
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-sm);
        background: var(--c-black-mid);
        color: var(--c-text-primary);
        font-family: var(--font-body);
    }

    .checkout-field input:focus {
        outline: none;
        border-color: var(--c-gold);
        box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.15);
    }

    .checkout-field input[readonly] {
        cursor: default;
        color: var(--c-text-secondary);
    }

    .checkout-field-error {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.8125rem;
        color: #f87171;
    }

    .checkout-index-submit {
        width: 100%;
        max-width: 320px;
    }

    .checkout-card-label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.8125rem;
        color: var(--c-text-secondary);
    }

    .checkout-card-element {
        padding: 0.75rem;
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-sm);
        background: var(--c-black-mid);
    }

    .checkout-stripe-missing {
        margin: 0;
        font-size: 0.875rem;
        color: var(--c-text-secondary);
    }

    .checkout-form-error {
        margin: 0;
    }

    .checkout-summary-items {
        list-style: none;
        margin: 0 0 var(--space-lg);
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
    }

    .checkout-summary-item {
        display: grid;
        grid-template-columns: 48px 1fr auto;
        gap: var(--space-sm);
        align-items: start;
    }

    .checkout-summary-item__thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: var(--radius-sm);
    }

    .checkout-summary-item__details {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        font-size: 0.8125rem;
        color: var(--c-text-secondary);
    }

    .checkout-summary-item__details strong {
        color: var(--c-text-primary);
        font-size: 0.875rem;
    }

    .checkout-summary-item__total {
        font-size: 0.875rem;
        color: var(--c-text-primary);
        white-space: nowrap;
    }

    .checkout-index-summary .checkout-index-back {
        width: 100%;
        text-align: center;
        margin-top: var(--space-md);
    }

    @media (max-width: 768px) {
        .checkout-field-grid {
            grid-template-columns: 1fr;
        }

        .checkout-index-submit {
            max-width: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    window.__checkout = {
        paymentIntentUrl: @json(route('checkout.payment-intent')),
        stripeKey: @json($stripeEnabled ? config('services.stripe.key') : null),
        stripeEnabled: @json($stripeEnabled),
    };
</script>
@if ($stripeEnabled)
    <script src="https://js.stripe.com/v3/"></script>
@endif
<script src="{{ asset('assets/web/js/checkout-index.js') }}"></script>
@endpush
