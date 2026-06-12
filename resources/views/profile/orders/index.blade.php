@extends('layouts.profile')
@section('title', __('My Orders'))

@section('profile_heading', __('My Orders'))
@section('profile_subheading', __('View your order history and track status.'))

@section('profile_content')
    <section class="profile-panel" aria-labelledby="profile-orders-title">
        <header class="profile-panel-header">
            <h2 class="profile-panel-title" id="profile-orders-title">{{ __('Order history') }}</h2>
            <p class="profile-panel-sub">{{ __('All purchases placed with your account.') }}</p>
        </header>

        @if ($orders->isNotEmpty())
            <div class="profile-order-list">
                @foreach ($orders as $order)
                    <article class="profile-order-card">
                        <div class="profile-order-card-top">
                            <div>
                                <p class="profile-order-number">{{ $order->publicOrderNumber() }}</p>
                                <p class="profile-order-date">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="profile-order-badges">
                                <span class="profile-order-badge profile-order-badge--status profile-order-badge--{{ $order->order_status }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="profile-order-card-meta">
                            <span>{{ trans_choice(':count item|:count items', $order->items_count, ['count' => $order->items_count]) }}</span>
                            <strong>{{ '$' . number_format((float) $order->total, 2) }}</strong>
                        </div>
                        <a href="{{ route('profile.orders.show', $order) }}" class="profile-order-view-link">{{ __('View details') }}</a>
                    </article>
                @endforeach
            </div>
        @else
            <p class="profile-address-empty">{{ __('You have not placed any orders yet.') }}</p>
            <a href="{{ route('artifacts.index') }}" class="btn btn-gold">{{ __('Start shopping') }}</a>
        @endif
    </section>
@endsection
