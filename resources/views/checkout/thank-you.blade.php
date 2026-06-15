@extends('layouts.web.master')

@section('title', __('Thank you'))

@section('content')
<main id="main" class="checkout-thank-you-page">
    <section class="shop-section section-pad checkout-page" aria-labelledby="thank-you-title">
        <div class="container">
            <div class="checkout-thank-you-card" data-scroll-reveal>
                <span class="section-eyebrow">{{ __('Order received') }}</span>
                <h1 class="section-title" id="thank-you-title">{{ __('Thank you! Your order has been received.') }}</h1>

                <dl class="checkout-thank-you-meta">
                    <div class="checkout-thank-you-meta__row">
                        <dt>{{ __('Order number') }}</dt>
                        <dd>{{ $order->publicOrderNumber() }}</dd>
                    </div>
                    <div class="checkout-thank-you-meta__row">
                        <dt>{{ __('Email') }}</dt>
                        <dd>{{ $order->customer_email }}</dd>
                    </div>
                    <div class="checkout-thank-you-meta__row">
                        <dt>{{ __('Status') }}</dt>
                        <dd>{{ ucwords(str_replace('_', ' ', $order->status)) }}</dd>
                    </div>
                </dl>

                <div class="checkout-thank-you-items">
                    <h2 class="checkout-panel-title">{{ __('Order items') }}</h2>
                    <ul class="checkout-items checkout-thank-you-items__list">
                        @foreach ($order->items as $item)
                            <li class="checkout-item checkout-thank-you-item">
                                <div class="checkout-thank-you-item__details">
                                    <p class="checkout-item-name">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="checkout-item-meta">{{ $item->variant_name }}</p>
                                    @endif
                                    @if ($item->sku)
                                        <p class="checkout-item-meta">{{ __('SKU') }}: {{ $item->sku }}</p>
                                    @endif
                                    <p class="checkout-item-meta">
                                        {{ __('Qty') }}: {{ $item->quantity }}
                                        ·
                                        {{ $order->currency }} {{ number_format((float) $item->price, 2) }}
                                    </p>
                                </div>
                                <strong>{{ $order->currency }} {{ number_format((float) $item->total, 2) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="checkout-success-total checkout-thank-you-subtotal">
                    <span>{{ __('Subtotal') }}</span>
                    <strong>{{ $order->currency }} {{ number_format((float) $order->subtotal, 2) }}</strong>
                </div>

                <div class="checkout-success-actions">
                    <a href="{{ route('printful-products.index') }}" class="btn btn-gold">{{ __('Back to products') }}</a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
    .checkout-thank-you-card {
        max-width: 720px;
        margin: 0 auto;
        padding: var(--space-2xl);
        background: var(--secondary-theme);
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-gold);
    }

    .checkout-thank-you-meta {
        margin: var(--space-xl) 0;
        padding: var(--space-lg);
        border: 1px solid var(--c-black-border);
        border-radius: var(--radius-md);
        background: var(--c-black-mid);
    }

    .checkout-thank-you-meta__row {
        display: flex;
        justify-content: space-between;
        gap: var(--space-md);
        padding: 0.35rem 0;
        font-size: 0.9375rem;
    }

    .checkout-thank-you-meta__row + .checkout-thank-you-meta__row {
        border-top: 1px solid var(--c-black-border);
        margin-top: 0.35rem;
        padding-top: 0.65rem;
    }

    .checkout-thank-you-meta__row dt {
        color: var(--c-text-secondary);
        font-weight: 400;
    }

    .checkout-thank-you-meta__row dd {
        margin: 0;
        color: var(--c-text-primary);
        text-align: right;
        font-family: var(--font-heading);
    }

    .checkout-thank-you-items {
        text-align: left;
        margin-bottom: var(--space-lg);
    }

    .checkout-thank-you-items .checkout-panel-title {
        margin-bottom: var(--space-md);
    }

    .checkout-thank-you-item {
        grid-template-columns: 1fr auto;
        align-items: start;
    }

    .checkout-thank-you-subtotal {
        justify-content: space-between;
        border-top: 1px solid var(--c-black-border);
        padding-top: var(--space-lg);
        margin-top: 0;
    }

    .checkout-thank-you-page .checkout-success-actions {
        justify-content: flex-start;
        margin-top: var(--space-xl);
    }
</style>
@endpush
