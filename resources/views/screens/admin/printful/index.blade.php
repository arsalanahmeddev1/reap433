@section('title', 'Printful Products')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid user-list-wrapper">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-no-border text-end">
                    <div class="card-header-right-icon">
                        <form action="{{ route('admin.printful.sync-products') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary f-w-500">
                                <i class="fa-solid fa-rotate pe-2"></i>Sync Printful Products
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body pt-0 px-0">
                    <div class="list-product user-list-table">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table" id="printful-products-table">
                                <thead>
                                    <tr>
                                        <th><span class="c-o-light f-w-600">Thumbnail</span></th>
                                        <th><span class="c-o-light f-w-600">Name</span></th>
                                        <th><span class="c-o-light f-w-600">Variants</span></th>
                                        <th><span class="c-o-light f-w-600">Synced</span></th>
                                        <th><span class="c-o-light f-w-600">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>
                                                @if ($product->thumbnail_url)
                                                    <img
                                                        src="{{ $product->thumbnail_url }}"
                                                        alt="{{ $product->name }}"
                                                        width="48"
                                                        height="48"
                                                        class="rounded object-fit-cover"
                                                        style="object-fit: cover;"
                                                    >
                                                @else
                                                    <span class="c-o-light">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->variants_count }}</td>
                                            <td>
                                                <span class="badge {{ $product->is_synced ? 'badge-light-success' : 'badge-light-warning' }}">
                                                    {{ $product->is_synced ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a class="square-white" href="{{ route('admin.printful.products.show', $product) }}" title="View">
                                                    <span><i class="fa-solid fa-eye"></i></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <h3 class="pt-5">{{ __('No Printful products synced yet.') }}</h3>
                                                <p class="c-o-light pb-4">Click "Sync Printful Products" to import from Printful.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($products->hasPages())
                        <div class="px-4 pb-4 d-flex justify-content-center">
                            {{ $products->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function () {
        if ($.fn.DataTable && $('#printful-products-table tbody tr').length > 0 && $('#printful-products-table tbody tr td[colspan]').length === 0) {
            $('#printful-products-table').DataTable({
                order: [[1, 'asc']],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 4],
                }],
                paging: false,
                info: false,
            });
        }
    });
</script>
@endpush
@endsection
