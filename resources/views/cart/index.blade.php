@extends('layouts.web.master')

@section('title', 'Cart')

@section('content')
<main id="main" class="cart-index-page">
    <section class="shop-section section-pad cart-page" aria-labelledby="cart-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">Your Bag</span>
                <h1 class="section-title" id="cart-title">Shopping Cart</h1>
                <p class="section-sub">Review your items before checkout.</p>
            </div>

            @if (session('success'))
                <div class="cart-index-flash" role="status">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="cart-index-flash cart-index-flash--error" role="alert">{{ session('error') }}</div>
            @endif

            @if (count($items) === 0)
                <div class="cart-index-empty" data-scroll-reveal>
                    <p>Your cart is empty.</p>
                    <a href="{{ route('printful-products.index') }}" class="btn btn-gold">Continue Shopping</a>
                </div>
            @else
                @php
                    $wholesalerMinQty = (int) ($wholesalerMinQty ?? 1);
                    $isWholesalerCheckout = (bool) ($isWholesalerCheckout ?? false);
                    $wholesalerQtyMessage = $isWholesalerCheckout
                        ? \App\Models\WholeSellerSetting::cartQuantityValidationMessage($items)
                        : null;
                @endphp
                <div class="cart-layout cart-index-layout" data-scroll-reveal>
                    <div class="cart-items-panel">
                        @if ($isWholesalerCheckout)
                            <div class="cart-index-flash {{ $wholesalerQtyMessage ? 'cart-index-flash--error' : '' }}" role="status">
                                {{ __('Wholesale minimum: at least :min of each product to checkout.', ['min' => $wholesalerMinQty]) }}
                            </div>
                        @endif
                        <div class="cart-items-list">
                            @foreach ($items as $cartKey => $item)
                                @php
                                    $cartKey = $item['cart_key'] ?? (string) $cartKey;
                                    $imageUrl = $item['preview_image']
                                        ?? $item['variant_thumbnail_url']
                                        ?? $item['product_thumbnail_url']
                                        ?? asset('assets/images/placeholders/img-not-available.png');
                                    $lineTotal = (float) $item['price'] * (int) $item['quantity'];
                                    $currency = strtoupper($item['currency'] ?? 'USD');
                                    $qtyBelowMin = $isWholesalerCheckout && (int) $item['quantity'] < $wholesalerMinQty;
                                    $originalPrice = (float) ($item['original_price'] ?? $item['price']);
                                    $unitPrice = (float) $item['price'];
                                    $showUnitDiscount = $isWholesalerCheckout
                                        && $originalPrice > $unitPrice
                                        && (int) ($item['wholesale_discount_percent'] ?? 0) > 0;
                                    $originalLineTotal = $originalPrice * (int) $item['quantity'];
                                @endphp
                                <article class="cart-item cart-index-item">
                                    <a href="{{ route('printful-products.show', $item['product_id']) }}" class="cart-item-image">
                                        <img src="{{ $imageUrl }}" alt="{{ $item['product_name'] }}" loading="lazy">
                                    </a>

                                    <div class="cart-item-body cart-index-item__body">
                                        <div class="cart-index-item__info">
                                            <h2 class="cart-item-name">{{ $item['product_name'] }}</h2>
                                            @if (! empty($item['is_customized']))
                                                <p class="cart-index-item__variant">Customized{{ ! empty($item['color']) ? ' · '.$item['color'] : '' }}{{ ! empty($item['size']) ? ' / '.$item['size'] : '' }}</p>
                                            @elseif ($item['variant_name'])
                                                <p class="cart-index-item__variant">{{ $item['variant_name'] }}</p>
                                            @endif
                                            @if (! empty($item['custom_text_summary']))
                                                <p class="cart-index-item__sku">Text: {{ $item['custom_text_summary'] }}</p>
                                            @endif
                                            <p class="cart-index-item__sku">
                                                <span>SKU</span> {{ $item['sku'] ?? '—' }}
                                            </p>
                                            <p class="cart-index-item__price">
                                                @if ($showUnitDiscount)
                                                    <span class="cart-price-compare">{{ $currency }} {{ number_format($originalPrice, 2) }}</span>
                                                    <span class="cart-price-sale">{{ $currency }} {{ number_format($unitPrice, 2) }}</span>
                                                    <span class="cart-price-off">{{ (int) $item['wholesale_discount_percent'] }}% off</span>
                                                @else
                                                    {{ $currency }} {{ number_format($unitPrice, 2) }}
                                                @endif
                                            </p>
                                            @if ($qtyBelowMin)
                                                <p class="cart-index-item__qty-error">{{ __('Minimum quantity for wholesale: :min', ['min' => $wholesalerMinQty]) }}</p>
                                            @endif
                                        </div>

                                        <div class="cart-index-item__actions">
                                            <form action="{{ route('cart.update', $cartKey) }}" method="POST" class="cart-index-item__update-form">
                                                @csrf
                                                @method('PATCH')
                                                <label for="qty-{{ md5($cartKey) }}" class="cart-index-item__qty-label">Quantity</label>
                                                <div class="cart-index-item__qty-row">
                                                    <input
                                                        type="number"
                                                        id="qty-{{ md5($cartKey) }}"
                                                        name="quantity"
                                                        class="cart-index-item__qty-input{{ $qtyBelowMin ? ' is-invalid' : '' }}"
                                                        value="{{ $item['quantity'] }}"
                                                        min="{{ $wholesalerMinQty }}"
                                                        max="99"
                                                        required
                                                    >
                                                    <button type="submit" class="btn btn-outline-sm">Update</button>
                                                </div>
                                            </form>

                                            <div class="cart-index-item__line-total">
                                                <span>Line total</span>
                                                @if ($showUnitDiscount)
                                                    <span class="cart-price-compare">{{ $currency }} {{ number_format($originalLineTotal, 2) }}</span>
                                                @endif
                                                <strong>{{ $currency }} {{ number_format($lineTotal, 2) }}</strong>
                                            </div>

                                            <form action="{{ route('cart.remove', $cartKey) }}" method="POST" class="cart-index-item__remove-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-sm cart-index-item__remove-btn">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <aside class="cart-summary cart-index-summary">
                        <h2 class="cart-summary-title">Order Summary</h2>
                        @php
                            $wholesaleSummary = $wholesaleSummary ?? [
                                'original_subtotal' => (float) $subtotal,
                                'discount_amount' => 0,
                                'discount_percent' => 0,
                                'subtotal' => (float) $subtotal,
                            ];
                        @endphp
                        @if ($isWholesalerCheckout && ($wholesaleSummary['discount_amount'] ?? 0) > 0)
                            <div class="cart-summary-row">
                                <span>{{ __('Retail subtotal') }}</span>
                                <strong class="cart-price-compare">{{ '$' . number_format((float) $wholesaleSummary['original_subtotal'], 2) }}</strong>
                            </div>
                            <div class="cart-summary-row cart-summary-discount">
                                <span>{{ __('Wholesale discount') }} ({{ (int) $wholesaleSummary['discount_percent'] }}%)</span>
                                <strong>−{{ '$' . number_format((float) $wholesaleSummary['discount_amount'], 2) }}</strong>
                            </div>
                            <div class="cart-summary-row cart-summary-total">
                                <span>{{ __('Subtotal') }}</span>
                                <strong>{{ '$' . number_format((float) $wholesaleSummary['subtotal'], 2) }}</strong>
                            </div>
                        @else
                            <div class="cart-summary-row cart-summary-total">
                                <span>Subtotal</span>
                                <strong>{{ '$' . number_format((float) $subtotal, 2) }}</strong>
                            </div>
                        @endif

                        <a href="{{ route('printful-products.index') }}" class="btn btn-outline-sm cart-continue-btn">Continue Shopping</a>
                        <a href="{{ route('checkout.index') }}" class="btn btn-gold cart-checkout-btn">Proceed to Checkout</a>

                        <form action="{{ route('cart.clear') }}" method="POST" class="cart-index-clear-form">
                            @csrf
                            <button type="submit" class="btn btn-outline-sm cart-index-clear-btn">Clear Cart</button>
                        </form>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
    .cart-index-flash {
        margin-bottom: var(--space-lg);
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        font-size: 0.9375rem;
        background: rgba(74, 222, 128, 0.12);
        border: 1px solid rgba(74, 222, 128, 0.35);
        color: var(--c-text-primary);
    }

    .cart-index-flash--error {
        background: rgba(248, 113, 113, 0.12);
        border: 1px solid rgba(248, 113, 113, 0.35);
    }

    .cart-index-item__qty-error {
        margin: 0.35rem 0 0;
        font-size: 0.8125rem;
        color: #f87171;
    }

    .cart-index-item__qty-input.is-invalid {
        border-color: #f87171;
    }

    .cart-price-compare {
        text-decoration: line-through;
        color: var(--c-text-muted);
        font-weight: 500;
        margin-right: 0.35rem;
    }

    .cart-price-sale {
        font-weight: 700;
        color: var(--c-cream, var(--c-text-primary));
        margin-right: 0.35rem;
    }

    .cart-price-off {
        font-size: 0.8em;
        color: var(--c-gold);
        font-weight: 600;
    }

    .cart-summary-discount strong {
        color: #4ade80;
    }

    .cart-index-item__line-total .cart-price-compare {
        display: block;
        font-size: 0.85em;
        margin: 0 0 0.15rem;
    }

    .cart-index-empty {
        text-align: center;
        padding: var(--space-3xl) var(--space-lg);
        border: 1px dashed var(--c-black-border);
        border-radius: var(--radius-md);
        color: var(--c-text-secondary);
    }

    .cart-index-empty p {
        margin-bottom: var(--space-lg);
        font-size: 1.125rem;
    }

    .cart-index-item__body {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: var(--space-lg);
        width: 100%;
    }

    .cart-index-item__info {
        flex: 1 1 220px;
    }

    .cart-index-item__variant {
        margin: 0.25rem 0;
        color: var(--c-text-secondary);
        font-size: 0.9375rem;
    }

    .cart-index-item__sku,
    .cart-index-item__price {
        margin: 0.35rem 0 0;
        color: var(--c-text-secondary);
        font-size: 0.875rem;
    }

    .cart-index-item__sku span,
    .cart-index-item__line-total span,
    .cart-index-item__qty-label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--c-text-muted);
        margin-bottom: 0.2rem;
    }

    .cart-index-item__actions {
        flex: 1 1 240px;
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
        align-items: flex-start;
    }

    .cart-index-item__update-form {
        width: 100%;
    }

    .cart-index-item__qty-row {
        display: flex;
        gap: var(--space-sm);
        align-items: center;
    }

    .cart-index-item__qty-input {
        width: 72px;
        padding: 0.45rem 0.5rem;
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-sm);
        background: var(--c-black-soft);
        color: var(--c-text-primary);
        text-align: center;
    }

    .cart-index-item__qty-input:focus {
        outline: none;
        border-color: var(--c-gold);
        box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.15);
    }

    .cart-index-item__line-total strong {
        color: var(--c-text-primary);
        font-size: 1.0625rem;
    }

    .cart-index-item__remove-btn {
        color: var(--c-text-secondary);
    }

    .cart-index-summary .cart-continue-btn,
    .cart-index-summary .cart-checkout-btn,
    .cart-index-clear-btn {
        width: 100%;
        text-align: center;
    }

    .cart-index-summary .cart-checkout-btn {
        margin-top: 0.75rem;
    }

    .cart-index-clear-form {
        margin-top: 0.75rem;
        width: 100%;
    }

    @media (max-width: 768px) {
        .cart-index-item {
            flex-direction: column;
        }

        .cart-index-item__actions {
            width: 100%;
        }

        .cart-index-item__qty-row {
            flex-wrap: wrap;
        }
    }
</style>
@endpush
