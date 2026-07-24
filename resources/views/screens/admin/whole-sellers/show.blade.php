@section('title', __('Whole Seller Details'))
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-no-border d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-0">{{ __('Whole Seller Details') }}</h5>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if ($user->isPendingApproval())
                            <form action="{{ route('whole-sellers.approve', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-check pe-1"></i> {{ __('Approve Whole Seller') }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('whole-sellers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left pe-1"></i> {{ __('Back to Whole Sellers') }}
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

                    <div class="row">
                        <div class="col-lg-5 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Account') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Name') }}</div>
                                        <div>{{ $user->name }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Login email') }}</div>
                                        <div>{{ $user->email }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Role') }}</div>
                                        <div>{{ __('Whole Seller') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Approval status') }}</div>
                                        <div>
                                            @if ($user->isPendingApproval())
                                                <span class="badge badge-light-warning">{{ __('Pending approval') }}</span>
                                            @elseif ($user->isApproved())
                                                <span class="badge badge-light-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{ ucfirst((string) $user->approval_status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($user->approved_at)
                                        <div class="mb-3">
                                            <div class="c-o-light f-w-600 mb-1">{{ __('Approved at') }}</div>
                                            <div>{{ $user->approved_at->format('d M Y, h:i A') }}</div>
                                        </div>
                                    @endif
                                    <div class="mb-0">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Total orders') }}</div>
                                        <div>{{ $user->orders_count }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 mb-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Business details') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Business name') }}</div>
                                        <div>{{ $user->business_name ?: '—' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Business phone') }}</div>
                                        <div>{{ $user->business_phone ?: '—' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Business email') }}</div>
                                        <div>{{ $user->business_email ?: '—' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Business location') }}</div>
                                        <div>{{ $user->business_location ?: '—' }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <div class="c-o-light f-w-600 mb-1">{{ __('Business description') }}</div>
                                        <div>{{ $user->business_description ?: '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h6 class="mb-0">{{ __('Addresses') }}</h6>
                                    <span class="badge badge-light-secondary">{{ $user->addresses->count() }}</span>
                                </div>
                                <div class="card-body">
                                    @forelse ($user->addresses as $address)
                                        <div class="border rounded p-3 mb-3 @if ($loop->last) mb-0 @endif">
                                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                                <div>
                                                    <strong>{{ $address->full_name }}</strong>
                                                    @if ($address->label)
                                                        <span class="badge badge-light-primary ms-1">{{ $address->label }}</span>
                                                    @endif
                                                </div>
                                                @if ($address->is_default)
                                                    <span class="badge badge-light-success">{{ __('Default') }}</span>
                                                @endif
                                            </div>
                                            <div class="mb-1">{{ $address->street_address }}@if ($address->street_address_2), {{ $address->street_address_2 }}@endif</div>
                                            <div class="mb-1">
                                                {{ $address->city }}@if ($address->state), {{ $address->state }}@endif {{ $address->zipcode }}
                                            </div>
                                            <div class="mb-1">{{ $address->country }}</div>
                                            @if ($address->phone)
                                                <div class="c-o-light">{{ __('Phone') }}: {{ $address->phone }}</div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="mb-0 c-o-light">{{ __('No saved addresses.') }}</p>
                                    @endforelse
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
