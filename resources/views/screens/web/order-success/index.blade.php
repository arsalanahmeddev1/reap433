@extends('layouts.web.master')
@section('title', __('Order confirmed'))

@section('content')
<main id="main">
    <section class="shop-section section-pad checkout-page" aria-labelledby="order-success-title">
        <div class="container">
            <div class="checkout-success-card" data-scroll-reveal>
                <span class="section-eyebrow">{{ __('Thank you') }}</span>
                <h1 class="section-title" id="order-success-title">{{ __('Order confirmed') }}</h1>
                <p class="section-sub">
                    {{ __('Your order :number has been placed successfully.', ['number' => $order->publicOrderNumber()]) }}
                </p>
                <div class="checkout-success-total">
                    <span>{{ __('Total paid') }}</span>
                    <strong>{{ '$' . number_format((float) $order->total, 2) }}</strong>
                </div>
                <div class="checkout-success-actions">
                    <a href="{{ route('home') }}" class="btn btn-gold">{{ __('Continue shopping') }}</a>
                    <a href="{{ route('artifacts.index') }}" class="btn btn-outline-sm">{{ __('View collection') }}</a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
