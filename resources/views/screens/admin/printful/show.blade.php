@section('title', $product->name)
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid user-list-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <a href="{{ route('admin.printful.products.index') }}" class="btn btn-light btn-sm">
                    <i class="fa-solid fa-arrow-left pe-1"></i> Back to Printful Products
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-4 align-items-start">
                        <div class="col-md-4 col-lg-3">
                            @if ($product->thumbnail_url)
                                <img
                                    src="{{ $product->thumbnail_url }}"
                                    alt="{{ $product->name }}"
                                    class="img-fluid rounded w-100"
                                    style="max-width: 280px; aspect-ratio: 1; object-fit: cover;"
                                >
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted"
                                    style="width: 100%; max-width: 280px; aspect-ratio: 1;">
                                    No image
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <h4 class="mb-2">{{ $product->name }}</h4>
                            <p class="c-o-light mb-1">
                                <strong>Printful ID:</strong> {{ $product->printful_product_id ?? '—' }}
                            </p>
                            <p class="c-o-light mb-1">
                                <strong>External ID:</strong> {{ $product->external_id ?? '—' }}
                            </p>
                            <p class="c-o-light mb-0">
                                <strong>Variants:</strong> {{ $product->variants->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Variants</h5>
                </div>
                <div class="card-body pt-0 px-0">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><span class="c-o-light f-w-600">Thumbnail</span></th>
                                    <th><span class="c-o-light f-w-600">Name</span></th>
                                    <th><span class="c-o-light f-w-600">SKU</span></th>
                                    <th><span class="c-o-light f-w-600">Price</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($product->variants as $variant)
                                    <tr>
                                        <td>
                                            @if ($variant->thumbnail_url)
                                                <img
                                                    src="{{ $variant->thumbnail_url }}"
                                                    alt="{{ $variant->name ?? 'Variant' }}"
                                                    width="40"
                                                    height="40"
                                                    class="rounded"
                                                    style="object-fit: cover;"
                                                >
                                            @else
                                                <span class="c-o-light">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $variant->name ?? '—' }}</td>
                                        <td>{{ $variant->sku ?? '—' }}</td>
                                        <td>
                                            @if ($variant->retail_price !== null)
                                                {{ strtoupper($variant->currency ?? 'USD') }}
                                                {{ number_format((float) $variant->retail_price, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 c-o-light">No variants synced.</td>
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
@endsection
