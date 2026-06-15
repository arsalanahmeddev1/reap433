@extends('layouts.web.master')
@section('title', 'Cart')
@section('content')
<main id="main">
    <section class="shop-section section-pad cart-page" aria-labelledby="cart-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">Your Bag</span>
                <h1 class="section-title" id="cart-title">Shopping Cart</h1>
                <p class="section-sub">Review your pieces before checkout.</p>
            </div>

            @if (session('success'))
                <div class="cart-flash cart-flash--success" role="status">{{ session('success') }}</div>
            @endif

            <div class="cart-layout" data-scroll-reveal>
                <div class="cart-items-panel">
                    <div class="cart-items-list" data-cart-list>
                        @forelse ($items as $item)
                            @php
                                $imageUrl = $item['variant_thumbnail_url']
                                    ?? $item['product_thumbnail_url']
                                    ?? asset('assets/images/placeholders/img-not-available.png');
                                $lineTotal = (float) $item['price'] * (int) $item['quantity'];
                            @endphp
                            <article class="cart-item" id="cart-item-{{ $item['variant_id'] }}" data-cart-item-id="{{ $item['variant_id'] }}">
                                <a href="{{ route('printful-products.show', $item['product_id']) }}" class="cart-item-image">
                                    <img src="{{ $imageUrl }}" alt="{{ $item['product_name'] }}" loading="lazy" />
                                </a>
                                <div class="cart-item-body">
                                    <div class="cart-item-top">
                                        <div>
                                            @if ($item['variant_name'])
                                                <p class="cart-item-category">{{ $item['variant_name'] }}</p>
                                            @endif
                                            <h2 class="cart-item-name">{{ $item['product_name'] }}</h2>
                                        </div>
                                        <form action="{{ route('cart.remove', $item['variant_id']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="cart-remove-item" aria-label="Remove {{ $item['product_name'] }}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="cart-item-bottom">
                                        <form action="{{ route('cart.update', $item['variant_id']) }}" method="POST" class="cart-qty-form">
                                            @csrf
                                            @method('PATCH')
                                            <div class="cart-qty-control" aria-label="Quantity">
                                                <label for="qty-{{ $item['variant_id'] }}" class="visually-hidden">Quantity</label>
                                                <input
                                                    type="number"
                                                    id="qty-{{ $item['variant_id'] }}"
                                                    name="quantity"
                                                    class="cart-qty-input"
                                                    value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    max="99"
                                                    data-cart-qty
                                                >
                                                <button type="submit" class="cart-qty-btn cart-qty-update">Update</button>
                                            </div>
                                        </form>
                                        <div class="cart-item-pricing">
                                            <span class="cart-item-unit">
                                                {{ strtoupper($item['currency'] ?? 'USD') }}
                                                {{ number_format((float) $item['price'], 2) }} each
                                            </span>
                                            <strong class="cart-item-total" data-line-total>
                                                {{ strtoupper($item['currency'] ?? 'USD') }}
                                                {{ number_format($lineTotal, 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="cart-empty">Your cart is empty.</p>
                        @endforelse
                    </div>
                </div>

                <aside class="cart-summary">
                    <h2 class="cart-summary-title">Order Summary</h2>
                    <div class="cart-summary-row">
                        <span>Subtotal</span>
                        <strong data-cart-subtotal>{{ '$' . number_format((float) $subtotal, 2) }}</strong>
                    </div>
                    <div class="cart-summary-row cart-summary-total">
                        <span>Total</span>
                        <strong data-cart-total>{{ '$' . number_format((float) $subtotal, 2) }}</strong>
                    </div>
                    <a href="{{ route('printful-products.index') }}" class="btn btn-outline-sm cart-continue-btn">Continue Shopping</a>
                    @if (count($items) > 0)
                        <form action="{{ route('cart.clear') }}" method="POST" class="cart-clear-form">
                            @csrf
                            <button type="submit" class="btn btn-outline-sm cart-clear-btn">Clear Cart</button>
                        </form>
                        @auth
                            <a href="{{ route('checkout.index') }}" class="btn btn-gold cart-checkout-btn">Proceed to Checkout</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-gold cart-checkout-btn">Sign in to Checkout</a>
                        @endauth
                    @endif
                </aside>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
    .cart-flash {
        margin-bottom: var(--space-lg);
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        font-size: 0.9375rem;
    }

    .cart-flash--success {
        background: rgba(74, 222, 128, 0.12);
        border: 1px solid rgba(74, 222, 128, 0.35);
        color: var(--c-text-primary);
    }

    .cart-qty-form {
        display: flex;
        align-items: center;
    }

    .cart-qty-input {
        width: 64px;
        padding: 0.4rem 0.5rem;
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-sm);
        background: var(--c-black-soft);
        color: var(--c-text-primary);
        text-align: center;
    }

    .cart-qty-update {
        margin-left: 0.5rem;
        padding: 0.4rem 0.75rem;
        font-size: 0.8125rem;
    }

    .cart-clear-form {
        margin-top: 0.75rem;
    }

    .cart-clear-btn {
        width: 100%;
    }

    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>
@endpush
