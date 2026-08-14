@extends('layouts.admin.master')
@section('title', __('Edit Product SEO'))
@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('admin.printful.products.show', $product) }}" class="btn btn-light btn-sm">
            <i class="fa-solid fa-arrow-left pe-1"></i> {{ __('Back to product') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="edit-profile">
        <form class="card" action="{{ route('admin.printful.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h5 class="mb-0">{{ __('Edit SEO') }} — {{ $product->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row custom-input">
                    <div class="col-md-4 mb-3">
                        @if ($product->thumbnail_url)
                            <img
                                src="{{ $product->thumbnail_url }}"
                                alt="{{ $product->name }}"
                                class="img-thumbnail"
                                style="max-height: 160px"
                            />
                        @endif
                        <p class="c-o-light small mt-2 mb-0">
                            {{ __('Printful product fields stay synced from Printful. Only SEO fields below are editable here.') }}
                        </p>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">{{ __('SEO Title') }}</label>
                            <input
                                type="text"
                                name="seo_title"
                                class="form-control @error('seo_title') is-invalid @enderror"
                                value="{{ old('seo_title', $product->seo_title) }}"
                                maxlength="255"
                                placeholder="{{ $product->name }}"
                            />
                            <small class="text-muted">{{ __('Optional. Leave blank to use the product name.') }}</small>
                            @error('seo_title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Meta Description') }}</label>
                            <textarea
                                name="seo_description"
                                rows="4"
                                class="form-control @error('seo_description') is-invalid @enderror"
                                placeholder="{{ __('Product meta description for search engines') }}"
                            >{{ old('seo_description', $product->seo_description) }}</textarea>
                            <small class="text-muted">{{ __('Optional. Used as the product page meta description.') }}</small>
                            @error('seo_description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.printful.products.show', $product) }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('Save SEO') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
