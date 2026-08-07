@extends('layouts.admin.master')
@section('title', __('Collection Pages'))
@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border text-end">
                        <div class="card-header-right-icon">
                            <a class="btn btn-primary f-w-500" href="{{ route('collection-pages.create') }}">
                                <i class="fa-solid fa-plus pe-2"></i>{{ __('Add Collection Page') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Image') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Category') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($collectionPages as $page)
                                            <tr class="product-removes inbox-data">
                                                <td>
                                                    @if ($page->imageUrl())
                                                        <img src="{{ $page->imageUrl() }}" alt="{{ $page->title }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $page->title }}</td>
                                                <td>
                                                    @if ($page->productCategories->isNotEmpty())
                                                        {{ $page->productCategories->pluck('name')->join(', ') }}
                                                    @else
                                                        {{ $page->productCategory?->name ?? '—' }}
                                                    @endif
                                                </td>
                                                <td><code class="text-reset small">{{ $page->slug }}</code></td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <a class="square-white" href="{{ route('collection-pages.edit', $page) }}" title="{{ __('Edit') }}">
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </a>
                                                        <form action="{{ route('collection-pages.destroy', $page) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-collection-page-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <h3 class="pt-5">{{ __('No collection pages yet') }}</h3>
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
        ajaxDelete('.js-collection-page-delete', 'tr');
    </script>
@endpush
