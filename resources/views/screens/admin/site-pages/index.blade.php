@extends('layouts.admin.master')
@section('title', __('Site Pages'))
@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0 f-w-600">{{ __('Site Pages') }}</h5>
                        <a class="btn btn-primary f-w-500" href="{{ route('site-pages.create') }}">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add page') }}
                        </a>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="site-pages-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pages as $page)
                                            @php
                                                $statusBadge = $page->status === 'active'
                                                    ? 'badge-light-success'
                                                    : 'badge-light-secondary';
                                            @endphp
                                            <tr class="product-removes inbox-data">
                                                <td>{{ $page->title }}</td>
                                                <td><code class="text-reset small">{{ $page->slug }}</code></td>
                                                <td>
                                                    <span class="badge {{ $statusBadge }}">{{ ucfirst($page->status) }}</span>
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <a class="square-white" href="{{ route('site-pages.edit', $page) }}" title="{{ __('Edit') }}">
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </a>
                                                        <form action="{{ route('site-pages.destroy', $page) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-site-page-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <h3 class="pt-5">{{ __('No site pages found') }}</h3>
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
@endsection

@push('scripts')
    <script>
        $(function() {
            if ($.fn.DataTable && $('#site-pages-table tbody tr').length > 0 && $('#site-pages-table tbody tr td[colspan]').length === 0) {
                $('#site-pages-table').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [{ orderable: false, targets: [3] }]
                });
            }
            ajaxDelete('.js-site-page-delete', 'tr');
        });
    </script>
@endpush
