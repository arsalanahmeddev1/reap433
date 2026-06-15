@extends('layouts.admin.master')
@section('title', __('Dashboard'))

@section('content')
<div class="container-fluid default-dashboard">
    @if ($isAdmin ?? false)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h4 class="mb-1">{{ __('Welcome back, :name', ['name' => $user->name]) }}</h4>
                            <p class="mb-0 c-o-light">{{ __('Here is what is happening in your REAP433 store today.') }}</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">{{ __('View orders') }}</a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Manage products') }}</a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm" target="_blank">{{ __('View storefront') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row widget-grid">
            <div class="col-xxl-3 col-md-6 box-col-6">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round secondary">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-customer') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['totalUsers'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('Customers') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-md-6 box-col-6">
                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round primary">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-invoice') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['totalOrders'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('Total orders') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-md-6 box-col-6">
                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round warning">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-profit') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['pendingOrders'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('Pending orders') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-md-6 box-col-6">
                <div class="card widget-1">
                    <div class="card-body">
                        <div class="widget-content">
                            <div class="widget-round success">
                                <div class="bg-round">
                                    <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-revenue') }}"></use></svg>
                                    <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0">${{ number_format((float) ($stats['totalRevenue'] ?? 0), 2) }}</h4>
                                <span class="f-light">{{ __('Total revenue') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-md-6 box-col-6">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round secondary">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-revenue') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['totalProducts'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('Products') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-4 col-md-6 box-col-6">
                <a href="{{ route('product-categories.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round secondary">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-profit') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['totalCategories'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('Categories') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-4 col-md-6 box-col-6">
                <a href="{{ route('blogs.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round secondary">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-invoice') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['totalBlogs'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('Blog posts') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="mb-0">{{ __('Recent orders') }}</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">{{ __('All orders') }}</a>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><span class="c-o-light f-w-600">{{ __('Order') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Customer') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Total') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Date') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentOrders as $order)
                                        @php
                                            $statusBadge = match ($order->status) {
                                                'completed', 'delivered' => 'badge-light-success',
                                                'cancelled' => 'badge-light-danger',
                                                default => 'badge-light-info',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('orders.show', $order) }}">{{ $order->publicOrderNumber() }}</a>
                                            </td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td>{{ $order->currency }} {{ number_format((float) $order->subtotal, 2) }}</td>
                                            <td><span class="badge {{ $statusBadge }}">{{ $order->statusLabel() }}</span></td>
                                            <td>{{ $order->created_at?->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <a class="square-white" href="{{ route('orders.show', $order) }}" title="{{ __('View') }}">
                                                    <span><i class="fa-solid fa-eye"></i></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 c-o-light">{{ __('No orders yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row widget-grid">
            <div class="col-xl-4 col-lg-6 col-sm-12">
                <a href="{{ route('profile.orders.index') }}" class="text-decoration-none">
                    <div class="card widget-1">
                        <div class="card-body">
                            <div class="widget-content">
                                <div class="widget-round secondary">
                                    <div class="bg-round">
                                        <svg aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#c-invoice') }}"></use></svg>
                                        <svg class="half-circle svg-fill" aria-hidden="true"><use href="{{ asset('assets/libs/svg/icon-sprite.svg#halfcircle') }}"></use></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ number_format((int) ($stats['myOrdersCount'] ?? 0)) }}</h4>
                                    <span class="f-light">{{ __('My orders') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
