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
                        @if (session('success'))
                            <div class="alert alert-success" role="status">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                        @endif

                        @php
                            $printfulStatus = strtolower((string) ($order->printful_status ?? ''));
                            $printfulRaw = is_array($order->raw_data['printful'] ?? null) ? $order->raw_data['printful'] : [];
                            $printfulLastSync = $printfulRaw['last_status_sync_at']
                                ?? $printfulRaw['confirmed_at']
                                ?? $printfulRaw['submitted_at']
                                ?? null;
                            $printfulSafeError = null;

                            if (! empty($printfulRaw['confirm_error']['error']) && is_string($printfulRaw['confirm_error']['error'])) {
                                $printfulSafeError = $printfulRaw['confirm_error']['error'];
                            } elseif (! empty($printfulRaw['status_sync_error']['error']) && is_string($printfulRaw['status_sync_error']['error'])) {
                                $printfulSafeError = $printfulRaw['status_sync_error']['error'];
                            } elseif ($printfulStatus === 'failed') {
                                $printfulSafeError = __('Printful reported a failed order status. Refresh the status or retry confirmation if applicable.');
                            }

                            $canCreateDraft = strcasecmp((string) $order->payment_status, 'paid') === 0
                                && $order->printful_order_id === null;
                            $canConfirmPrintful = $order->printful_order_id !== null
                                && in_array($printfulStatus, ['draft', 'failed'], true);
                            $canRefreshPrintfulStatus = $order->printful_order_id !== null;
                        @endphp

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

                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Printful Fulfillment</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="small c-o-light">Local order number</div>
                                                <div class="fw-semibold">{{ $order->publicOrderNumber() }}</div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="small c-o-light">Payment status</div>
                                                <div class="fw-semibold">{{ ucfirst($order->payment_status) }}</div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="small c-o-light">Printful order ID</div>
                                                <div class="fw-semibold">{{ $order->printful_order_id ?? '—' }}</div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="small c-o-light">Printful status</div>
                                                <div class="fw-semibold">
                                                    @if ($order->printful_status)
                                                        <span class="badge {{ $printfulStatus === 'failed' ? 'badge-light-danger' : ($printfulStatus === 'draft' ? 'badge-light-warning' : 'badge-light-info') }}">
                                                            {{ ucwords(str_replace('_', ' ', $order->printful_status)) }}
                                                        </span>
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="small c-o-light">Last Printful sync</div>
                                                <div class="fw-semibold">
                                                    @if ($printfulLastSync)
                                                        {{ \Illuminate\Support\Carbon::parse($printfulLastSync)->format('d M Y, h:i A') }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if ($printfulSafeError)
                                            <div class="alert alert-danger py-2 mb-3" role="alert">
                                                <strong>{{ __('Printful error:') }}</strong> {{ $printfulSafeError }}
                                            </div>
                                        @endif

                                        <div class="d-flex flex-wrap gap-2">
                                            @if ($canCreateDraft)
                                                <form action="{{ route('orders.printful.create-draft', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-file-circle-plus pe-1"></i>
                                                        Create Printful Draft
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($canConfirmPrintful)
                                                <form action="{{ route('orders.printful.confirm', $order) }}" method="POST" class="d-inline printful-confirm-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="fa-solid fa-truck-fast pe-1"></i>
                                                        Confirm Printful Order
                                                    </button>
                                                </form>
                                            @endif

                                        @if ($canRefreshPrintfulStatus)
                                            <a href="{{ route('orders.printful.status', $order) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-rotate pe-1"></i>
                                                Refresh Printful Status
                                            </a>
                                        @endif
                                        </div>

                                        @unless (config('services.printful.auto_confirm'))
                                            <p class="mb-0 mt-3 c-o-light small">
                                                Automatic Printful confirmation is disabled. Draft orders must be confirmed manually.
                                            </p>
                                        @endunless
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

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('.printful-confirm-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const confirmed = window.confirm(
                        'Confirming this order may charge your Printful billing method and start fulfillment. Continue?'
                    );

                    if (confirmed) {
                        form.submit();
                    }
                });
            });
        })();
    </script>
@endpush
