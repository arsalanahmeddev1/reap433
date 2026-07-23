@extends('layouts.admin.master')
@section('title', __('Abandoned Cart Details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <a href="{{ route('abandoned-carts.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left pe-1"></i>{{ __('Back to Abandoned Carts') }}
                </a>
            </div>

            @if (session('success'))
                <div class="col-12">
                    <div class="alert alert-success">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="col-12">
                    <div class="alert alert-danger">{{ session('error') }}</div>
                </div>
            @endif
            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="mb-0">{{ __('User Information') }}</h6>
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#sendOfferModal"
                            @disabled($activeCoupons->isEmpty())
                        >
                            <i class="fa-solid fa-tags pe-1"></i>{{ __('Send offer') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-2"><strong>{{ __('Name') }}:</strong> {{ $user->name }}</div>
                        <div class="mb-2"><strong>{{ __('Email') }}:</strong> {{ $user->email }}</div>
                        <div class="mb-2"><strong>{{ __('Total cart items') }}:</strong> {{ $cartItemsCount }}</div>
                        <div class="mb-0"><strong>{{ __('Cart amount') }}:</strong> {{ $cartCurrency }} {{ number_format((float) $cartAmount, 2) }}</div>
                        @if ($activeCoupons->isEmpty())
                            <p class="mb-0 mt-3 text-warning small">{{ __('Create an active coupon before sending an offer.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Cart Items') }}</h6>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th><span class="c-o-light f-w-600">{{ __('Image') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Product') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Variant') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('SKU') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Qty') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Price') }}</span></th>
                                        <th class="text-end"><span class="c-o-light f-w-600">{{ __('Line total') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>
                                                @if ($item->thumbnail_url)
                                                    <img
                                                        src="{{ $item->thumbnail_url }}"
                                                        alt="{{ $item->product_name }}"
                                                        width="40"
                                                        height="40"
                                                        class="rounded"
                                                        style="object-fit: cover;"
                                                    >
                                                @else
                                                    <span class="c-o-light">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->variant_name ?? '—' }}</td>
                                            <td>{{ $item->sku ?? '—' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ strtoupper((string) ($item->currency ?? 'USD')) }} {{ number_format((float) $item->price, 2) }}</td>
                                            <td class="text-end">{{ strtoupper((string) ($item->currency ?? 'USD')) }} {{ number_format($item->lineTotal(), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>{{ __('Cart amount') }}</span>
                                <span>{{ $cartCurrency }} {{ number_format((float) $cartAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Sent offers') }}</h6>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th><span class="c-o-light f-w-600">{{ __('Coupon') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Discount') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Message') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Sent by') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Sent at') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sentOffers as $offer)
                                        <tr>
                                            <td>
                                                <code class="text-reset">{{ $offer->coupon_code }}</code>
                                                @if ($offer->coupon?->title)
                                                    <div class="small c-o-light">{{ $offer->coupon->title }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $offer->discount_in_percent !== null ? $offer->discount_in_percent.'%' : '—' }}</td>
                                            <td style="max-width: 320px; white-space: pre-wrap;">{{ $offer->message }}</td>
                                            <td>{{ $offer->sender?->name ?? '—' }}</td>
                                            <td>{{ $offer->sent_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center c-o-light py-4">{{ __('No offers sent yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sendOfferModal" tabindex="-1" aria-labelledby="sendOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendOfferModalLabel">{{ __('Send offer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form action="{{ route('abandoned-carts.send-offer', $user) }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p class="c-o-light mb-3">
                            {{ __('Email will be sent to') }} <strong>{{ $user->email }}</strong>
                        </p>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="offer-coupon-id">{{ __('Active coupon') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="offer-coupon-id" name="coupon_id" required>
                                <option value="">{{ __('Select coupon code') }}</option>
                                @foreach ($activeCoupons as $coupon)
                                    <option value="{{ $coupon->id }}" @selected((string) old('coupon_id') === (string) $coupon->id)>
                                        {{ $coupon->coupon_code }} — {{ $coupon->title }} ({{ $coupon->discount_in_percent }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="offer-message">{{ __('Email message') }} <span class="text-danger">*</span></label>
                            <textarea
                                class="form-control"
                                id="offer-message"
                                name="message"
                                rows="5"
                                required
                                maxlength="5000"
                                placeholder="{{ __('Write the offer message the customer will see in the email…') }}"
                            >{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane pe-1"></i>{{ __('Send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@if ($errors->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEl = document.getElementById('sendOfferModal');
                if (modalEl && window.bootstrap) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endpush
@endif
