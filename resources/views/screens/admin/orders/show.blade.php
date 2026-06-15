@section('title', 'Order Details')
@extends('layouts.admin.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5>Order {{ $order->publicOrderNumber() }}</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <strong>Order Status:</strong>
                            <span class="badge {{ $order->status === 'completed' || $order->status === 'delivered' ? 'badge-light-success' : ($order->status === 'cancelled' ? 'badge-light-danger' : 'badge-light-info') }}">
                                {{ $order->statusLabel() }}
                            </span>
                            <strong>Payment:</strong>
                            <span class="badge badge-light-secondary">{{ ucfirst($order->payment_status) }}</span>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-arrow-left pe-1"></i> Back to Orders
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Order Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2"><strong>Order Number:</strong> {{ $order->publicOrderNumber() }}</div>
                                        <div class="mb-2"><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</div>
                                        <div class="mb-2"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</div>
                                        <div class="mb-0"><strong>Total Items:</strong> {{ $order->items->sum('quantity') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Customer Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2"><strong>Name:</strong> {{ $order->customer_name }}</div>
                                        <div class="mb-2"><strong>Email:</strong> {{ $order->customer_email }}</div>
                                        @if ($order->customer_phone)
                                            <div class="mb-0"><strong>Phone:</strong> {{ $order->customer_phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Shipping Address</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-0">
                                            {{ $order->address1 }}<br>
                                            @if ($order->address2)
                                                {{ $order->address2 }}<br>
                                            @endif
                                            {{ $order->city }}, {{ $order->state_code }} {{ $order->zip }}<br>
                                            {{ strtoupper($order->country_code) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                <th><span class="c-o-light f-w-600">Variant</span></th>
                                                <th><span class="c-o-light f-w-600">SKU</span></th>
                                                <th><span class="c-o-light f-w-600">Quantity</span></th>
                                                <th><span class="c-o-light f-w-600">Unit Price</span></th>
                                                <th class="text-end"><span class="c-o-light f-w-600">Total</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->variant_name ?? '—' }}</td>
                                                    <td>{{ $item->sku ?? '—' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $order->currency }} {{ number_format((float) $item->price, 2) }}</td>
                                                    <td class="text-end">{{ $order->currency }} {{ number_format((float) $item->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Subtotal</span>
                                        <span>{{ $order->currency }} {{ number_format((float) $order->subtotal, 2) }}</span>
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
