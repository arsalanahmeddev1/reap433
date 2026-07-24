@extends('layouts.admin.master')
@section('title', __('Whole Seller Setting'))

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border">
                        <h5 class="mb-0 f-w-600">{{ __('Whole Seller Setting') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('whole-seller-settings.update') }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label for="product_discount" class="form-label">{{ __('Product discount') }}</label>
                                <select
                                    id="product_discount"
                                    name="product_discount"
                                    class="form-select @error('product_discount') is-invalid @enderror"
                                    required
                                >
                                    <option value="">{{ __('Select discount') }}</option>
                                    @for ($i = 1; $i <= 100; $i++)
                                        <option value="{{ $i }}" @selected((string) old('product_discount', $setting->product_discount) === (string) $i)>
                                            {{ $i }}%
                                        </option>
                                    @endfor
                                </select>
                                @error('product_discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="order_quantity" class="form-label">{{ __('Order quantity') }}</label>
                                <input
                                    type="text"
                                    id="order_quantity"
                                    name="order_quantity"
                                    class="form-control @error('order_quantity') is-invalid @enderror"
                                    value="{{ old('order_quantity', $setting->order_quantity) }}"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    required
                                    placeholder="{{ __('Enter order quantity') }}"
                                >
                                @error('order_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk pe-1"></i>{{ __('Update setting') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
