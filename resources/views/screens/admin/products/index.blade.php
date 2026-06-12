@section('title', 'All Products')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid user-list-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-no-border text-end">
                    <div class="card-header-right-icon">
                        <a class="btn btn-primary f-w-500" href="{{ route('products.create') }}"><i
                                class="fa-solid fa-plus pe-2"></i>Add
                            Product</a>
                    </div>
                </div>
                <div class="card-body pt-0 px-0">
                    <div class="list-product user-list-table">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table" id="products-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <span class="c-o-light f-w-600">Name</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Category</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Type</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Price</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Status</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                    <tr class="product-removes inbox-data">
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category?->name ?? '—' }}</td>
                                        <td>{{ $product->productType?->name ?? '—' }}</td>
                                        <td>
                                            @if ($product->productType?->slug === 'variable')
                                                {{ $product->listingPriceLabel() }}
                                            @else
                                                {{ number_format((float) $product->price, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ $product->status }}</td>
                                        <td>
                                            <div class="common-align gap-2 justify-content-start">
                                                <a class="square-white" href="{{ route('products.show', $product) }}" title="View">
                                                    <span><i class="fa-solid fa-eye"></i></span>
                                                </a>
                                                <a class="square-white" href="{{ route('products.edit', $product) }}" title="Edit">
                                                    <span><i class="fa-solid fa-pen"></i></span>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="square-white border-0 js-product-delete"
                                                        title="Delete">
                                                        <span><i class="fa-solid fa-trash"></i></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="products-empty-row">
                                        <td colspan="6" class="text-center">
                                            <h3 class="pt-5">{{ __('No products found') }}</h3>
                                        </td>
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
</div>
@push('scripts')
<script>
    $(function () {
        if ($.fn.DataTable && $('#products-table tbody tr').length > 0 && $('#products-table tbody tr td[colspan]').length === 0) {
            $('#products-table').DataTable({
                order: [[0, 'asc']],
                columnDefs: [{
                    orderable: false,
                    targets: 5,
                }],
            });
        }

        ajaxDelete('.js-product-delete', 'tr');
    });
</script>
@endpush
@endsection
