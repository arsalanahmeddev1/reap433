@extends('layouts.profile')
@section('title', $order->publicOrderNumber())

@section('profile_heading', $order->publicOrderNumber())
@section('profile_subheading', __('Placed on :date', ['date' => $order->created_at?->format('M j, Y g:i A')]))

@section('profile_content')
    <section class="profile-panel" aria-labelledby="profile-order-detail-title">
        <header class="profile-panel-header profile-panel-header--row">
            <div>
                <h2 class="profile-panel-title" id="profile-order-detail-title">{{ __('Order details') }}</h2>
                <div class="profile-order-badges profile-order-badges--inline">
                    <span class="profile-order-badge profile-order-badge--status profile-order-badge--{{ $order->status }}">
                        {{ $order->statusLabel() }}
                    </span>
                </div>
            </div>
            <a href="{{ route('profile.orders.index') }}" class="btn btn-outline-sm">{{ __('Back to orders') }}</a>
        </header>

        <div class="profile-order-shipping">
            <h3 class="profile-order-section-title">{{ __('Shipping address') }}</h3>
            <p class="profile-address-lines">
                {{ $order->customer_name }}<br>
                {{ $order->address1 }}<br>
                @if ($order->address2)
                    {{ $order->address2 }}<br>
                @endif
                {{ $order->city }}, {{ $order->state_code }} {{ $order->zip }}<br>
                {{ strtoupper($order->country_code) }}
            </p>
        </div>

        <h3 class="profile-order-section-title">{{ __('Items') }}</h3>
        <ul class="checkout-items profile-order-items">
            @foreach ($order->items as $item)
                <li class="checkout-item">
                    <div>
                        <p class="checkout-item-name">{{ $item->product_name }}</p>
                        @if ($item->variant_name)
                            <p class="checkout-item-meta">{{ $item->variant_name }}</p>
                        @endif
                        <p class="checkout-item-meta">
                            {{ $item->quantity }} × {{ $order->currency }} {{ number_format((float) $item->price, 2) }}
                        </p>
                    </div>
                    <strong>{{ $order->currency }} {{ number_format((float) $item->total, 2) }}</strong>
                </li>
            @endforeach
        </ul>

        <div class="profile-order-totals">
            <div class="cart-summary-row">
                <span>{{ __('Items') }}</span>
                <strong>{{ $order->items->sum('quantity') }}</strong>
            </div>
            <div class="cart-summary-row cart-summary-total">
                <span>{{ __('Subtotal') }}</span>
                <strong>{{ $order->currency }} {{ number_format((float) $order->subtotal, 2) }}</strong>
            </div>
        </div>
    </section>
@endsection
