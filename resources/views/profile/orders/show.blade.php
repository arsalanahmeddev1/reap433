@extends('layouts.profile')
@section('title', $order->publicOrderNumber())

@section('profile_heading', $order->publicOrderNumber())
@section('profile_subheading', __('Placed on :date', ['date' => $order->created_at?->format('M j, Y g:i A')]))

@section('profile_content')
    @php
        $address = $order->addresses->first();
    @endphp

    <section class="profile-panel" aria-labelledby="profile-order-detail-title">
        <header class="profile-panel-header profile-panel-header--row">
            <div>
                <h2 class="profile-panel-title" id="profile-order-detail-title">{{ __('Order details') }}</h2>
                <div class="profile-order-badges profile-order-badges--inline">
                    <span class="profile-order-badge profile-order-badge--status profile-order-badge--{{ $order->order_status }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('profile.orders.index') }}" class="btn btn-outline-sm">{{ __('Back to orders') }}</a>
        </header>

        @if ($address)
            <div class="profile-order-shipping">
                <h3 class="profile-order-section-title">{{ __('Shipping address') }}</h3>
                <p class="profile-address-lines">
                    {{ $address->shipping_name ?? $address->billing_name }}<br>
                    {{ $address->shipping_address ?? $address->billing_address }}<br>
                    {{ $address->shipping_city ?? $address->billing_city }}@if ($address->shipping_state ?? $address->billing_state), {{ $address->shipping_state ?? $address->billing_state }}@endif {{ $address->shipping_zip ?? $address->billing_zip }}<br>
                    {{ $address->shipping_country ?? $address->billing_country }}
                </p>
            </div>
        @endif

        <h3 class="profile-order-section-title">{{ __('Items') }}</h3>
        <ul class="checkout-items profile-order-items">
            @foreach ($order->items as $item)
                @php
                    $product = $item->product;
                    $image = $product?->images->firstWhere('is_primary', 1) ?? $product?->images->first();
                    $imageUrl = $image?->publicUrl() ?: asset('assets/images/placeholders/img-not-available.png');
                @endphp
                <li class="checkout-item">
                    <img src="{{ $imageUrl }}" alt="" class="checkout-item-img" loading="lazy" />
                    <div>
                        <p class="checkout-item-name">{{ $product?->name ?? __('Product') }}</p>
                        <p class="checkout-item-meta">{{ $item->qty }} × {{ '$' . number_format((float) $item->price, 2) }}</p>
                    </div>
                    <strong>{{ '$' . number_format((float) $item->total, 2) }}</strong>
                </li>
            @endforeach
        </ul>

        <div class="profile-order-totals">
            <div class="cart-summary-row">
                <span>{{ __('Items') }}</span>
                <strong>{{ $order->total_qty }}</strong>
            </div>
            <div class="cart-summary-row cart-summary-total">
                <span>{{ __('Total') }}</span>
                <strong>{{ '$' . number_format((float) $order->total, 2) }}</strong>
            </div>
        </div>
    </section>
@endsection
