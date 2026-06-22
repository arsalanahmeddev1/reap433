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
        <div class="profile-order-items-table-wrap">
            <table class="profile-order-items-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Product') }}</th>
                        <th scope="col">{{ __('Variant') }}</th>
                        <th scope="col">{{ __('Qty') }}</th>
                        <th scope="col">{{ __('Price') }}</th>
                        <th scope="col" class="profile-order-items-table__total">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="profile-order-items-table__product">{{ $item->product_name }}</td>
                            <td>{{ $item->variant_name ?? '—' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $order->currency }} {{ number_format((float) $item->price, 2) }}</td>
                            <td class="profile-order-items-table__total">{{ $order->currency }} {{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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

@push('styles')
<style>
    .profile-order-items-table-wrap {
        margin-bottom: var(--space-lg);
        overflow-x: auto;
    }

    .profile-order-items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .profile-order-items-table thead th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-family: var(--font-heading);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--c-gold);
        border-bottom: 1px solid var(--c-black-border);
        white-space: nowrap;
    }

    .profile-order-items-table tbody td {
        padding: 0.85rem 1rem;
        color: var(--c-text-secondary);
        border-bottom: 1px solid var(--c-black-border);
        vertical-align: top;
    }

    .profile-order-items-table__product {
        color: var(--c-text-primary);
        font-family: var(--font-heading);
        min-width: 160px;
    }

    .profile-order-items-table__total {
        text-align: right;
        color: var(--c-gold);
        font-family: var(--font-heading);
        white-space: nowrap;
    }

    .profile-order-items-table thead th.profile-order-items-table__total {
        text-align: right;
    }
</style>
@endpush
