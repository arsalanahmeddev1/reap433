@section('title', 'Order Details')
@extends('layouts.admin.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header card-no-border d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5>Order {{ $order->publicOrderNumber() }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <strong>Order Status:</strong> <span
                                class="badge {{ $order->order_status === 'completed' || $order->order_status === 'delivered' ? 'badge-light-success' : ($order->order_status === 'cancelled' ? 'badge-light-danger' : 'badge-light-info') }}">{{ ucfirst($order->order_status) }}</span>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm"><i
                                    class="fa-solid fa-arrow-left pe-1"></i> Back to Orders</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Order Summary --}}
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Order Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2"><strong>Order ID:</strong>
                                            {{ $order->publicOrderNumber() }}</div>
                                        <div class="mb-2"><strong>Order Date:</strong>
                                            {{ $order->created_at->format('d M Y, h:i A') }}</div>
                                        <div class="mb-2"><strong>Payment Method:</strong>
                                            {{ ucfirst($order->payment_method ?? '-') }}</div>
                                        <div class="mb-0"><strong>Total Items:</strong> {{ $order->total_qty }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Customer Info --}}
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Customer Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2"><strong>Name:</strong> {{ $order->user?->name ?? 'Guest' }}
                                        </div>
                                        <div class="mb-2"><strong>Email:</strong> {{ $order->user?->email ?? '-' }}</div>
                                        @if ($order->user?->phone)
                                            <div class="mb-0"><strong>Phone:</strong> {{ $order->user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Billing Info --}}
                            @php $address = $order->addresses->first(); @endphp
                            @if ($address)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Billing Address</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2"><strong> Billing Name:</strong>
                                                {{ $address->billing_name }}</div>
                                            <div class="mb-2"><strong> Billing Email:</strong>
                                                {{ $address->billing_email }}</div>
                                            @if ($address->billing_phone)
                                                <div class="mb-2"><strong> Billing Phone:</strong>
                                                    {{ $address->billing_phone }}</div>
                                            @endif
                                            <div class="mb-0">
                                                <strong> Billing Address:</strong>
                                                {{ $address->billing_address }},<br>
                                                {{ $address->billing_city }}, {{ $address->billing_state }}
                                                {{ $address->billing_zip }},<br>
                                                {{ $address->billing_country }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Shipping Info --}}
                                @if ($address->shipping_address || $address->shipping_name)
                                    <div class="col-md-6 mb-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Shipping Address</h6>
                                            </div>
                                            <div class="card-body">
                                                @if ($address->shipping_name)
                                                    <strong>Shipping Address</strong>
                                                    <div class="mb-2">{{ $address->shipping_name }}</div>
                                                @endif
                                                @if ($address->shipping_email)
                                                    <strong>Shipping Email</strong>
                                                    <div class="mb-2">{{ $address->shipping_email }}</div>
                                                @endif
                                                @if ($address->shipping_phone)
                                                    <strong>Shipping Phone</strong>
                                                    <div class="mb-2">{{ $address->shipping_phone }}</div>
                                                @endif
                                                @if ($address->shipping_address)
                                                    <strong>Shipping Address</strong>
                                                    <div class="mb-0">
                                                        {{ $address->shipping_address }},<br>
                                                        @if ($address->shipping_city)
                                                            {{ $address->shipping_city }}
                                                        @endif
                                                        @if ($address->shipping_state)
                                                            {{ $address->shipping_state }}
                                                        @endif
                                                        @if ($address->shipping_zip)
                                                            {{ $address->shipping_zip }}
                                                        @endif
                                                        @if ($address->shipping_country)
                                                            <br>{{ $address->shipping_country }}
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-muted">Same as billing</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Order Items --}}
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Order Items</h6>
                            </div>
                            <div class="card-body pt-0 px-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th><span class="c-o-light f-w-600">Product</span></th>
                                                <th><span class="c-o-light f-w-600">Image</span></th>
                                                <th><span class="c-o-light f-w-600">Quantity</span></th>
                                                <th><span class="c-o-light f-w-600">Unit Price</span></th>
                                                <th class="text-end"><span class="c-o-light f-w-600">Total</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    <td>{{ $item->product?->name ?? 'Unknown Product' }}</td>
                                                    <td>
                                                        @if ($item->product?->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                alt="{{ $item->product->name }}"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>${{ number_format($item->price, 2) }}</td>
                                                    <td class="text-end">${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top">
                                    @php $subtotal = $order->items->sum('total'); @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span>${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    @if ($order->tax > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax</span>
                                            <span>${{ number_format($order->tax, 2) }}</span>
                                        </div>
                                    @endif
                                    @if ($order->discount > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Discount</span>
                                            <span>-${{ number_format($order->discount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total</span>
                                        <span>${{ number_format($order->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
