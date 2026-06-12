@extends('layouts.admin.master')
@section('title', __('All posts'))
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
                            <a class="btn btn-primary f-w-500" href="{{ route('blogs.create') }}">
                                <i class="fa-solid fa-plus pe-2"></i>{{ __('Add post') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Category') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Published') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($blogs as $blog)
                                            <tr class="product-removes inbox-data">
                                                <td>{{ $blog->title }}</td>
                                                <td>{{ $blog->category?->name ?? '—' }}</td>
                                                <td><code class="text-reset small">{{ $blog->slug }}</code></td>
                                                <td>
                                                    @if ($blog->is_published)
                                                        <span class="badge badge-light-success">{{ __('Yes') }}</span>
                                                    @else
                                                        <span class="badge badge-light-secondary">{{ __('No') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <a class="square-white" href="{{ route('blogs.edit', $blog) }}" title="{{ __('Edit') }}">
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </a>
                                                        <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-blog-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <h3 class="pt-5">{{ __('No posts yet') }}</h3>
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
        ajaxDelete('.js-blog-delete', 'tr');
    </script>
@endpush
