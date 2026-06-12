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

            <div class="cart-layout" data-scroll-reveal>
                <div class="cart-items-panel">
                    <div class="cart-items-list" data-cart-list>
                        @forelse ($cart->items as $item)
                            @php
                                $product = $item->product;
                                $image = $product?->images->firstWhere('is_primary', 1) ?? $product?->images->first();
                                $imageUrl = $image?->publicUrl() ?: asset('assets/images/placeholders/img-not-available.png');
                            @endphp
                            <article class="cart-item" id="cart-item-{{ $item->id }}" data-cart-item-id="{{ $item->id }}">
                                <a href="{{ $product ? route('artifacts.show', $product) : '#' }}" class="cart-item-image">
                                    <img src="{{ $imageUrl }}" alt="{{ $product?->name }}" loading="lazy" />
                                </a>
                                <div class="cart-item-body">
                                    <div class="cart-item-top">
                                        <div>
                                            <p class="cart-item-category">{{ $product?->category?->name }}</p>
                                            <h2 class="cart-item-name">{{ $product?->name }}</h2>
                                        </div>
                                        <button type="button" class="cart-remove-item" data-id="{{ $item->id }}" aria-label="Remove {{ $product?->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                    <div class="cart-item-bottom">
                                        <div class="cart-qty-control" aria-label="Quantity">
                                            <button type="button" class="cart-qty-btn cart-qty-minus" data-id="{{ $item->id }}" aria-label="Decrease quantity">−</button>
                                            <span class="cart-qty-value" id="qty-{{ $item->id }}" data-cart-qty>{{ $item->qty }}</span>
                                            <button type="button" class="cart-qty-btn cart-qty-plus" data-id="{{ $item->id }}" aria-label="Increase quantity">+</button>
                                        </div>
                                        <div class="cart-item-pricing">
                                            <span class="cart-item-unit">{{ '$' . number_format((float) $item->price, 2) }} each</span>
                                            <strong class="cart-item-total" id="item-total-{{ $item->id }}" data-line-total>{{ '$' . number_format((float) $item->subtotal, 2) }}</strong>
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
                        <strong data-cart-subtotal>{{ '$' . number_format((float) $cart->total(), 2) }}</strong>
                    </div>
                    <div class="cart-summary-row cart-summary-total">
                        <span>Total</span>
                        <strong data-cart-total>{{ '$' . number_format((float) $cart->total(), 2) }}</strong>
                    </div>
                    <a href="{{ route('artifacts.index') }}" class="btn btn-outline-sm cart-continue-btn">Continue Shopping</a>
                    @if ($cart->items->isNotEmpty())
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
