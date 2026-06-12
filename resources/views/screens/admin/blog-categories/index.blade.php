@extends('layouts.admin.master')
@section('title', __('Blog categories'))

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0 f-w-600">{{ __('Blog categories') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#blogCategoryCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add category') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="blog-categories-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Name') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($categories as $category)
                                            @php
                                                $statusBadge =
                                                    $category->status === 'active'
                                                        ? 'badge-light-success'
                                                        : 'badge-light-secondary';
                                            @endphp
                                            <tr class="product-removes inbox-data" data-blog-category-id="{{ $category->id }}">
                                                <td class="bc-name">{{ $category->name }}</td>
                                                <td class="bc-slug"><code class="text-reset">{{ $category->slug }}</code></td>
                                                <td class="bc-status">
                                                    <span class="badge {{ $statusBadge }}">{{ ucfirst($category->status) }}</span>
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-blog-category-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('blog-categories.update', $category) }}"
                                                            data-name="{{ $category->name }}"
                                                            data-slug="{{ $category->slug }}"
                                                            data-status="{{ $category->status }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('blog-categories.destroy', $category) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-blog-category-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <h3 class="pt-5">{{ __('No categories found') }}</h3>
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

    {{-- Create --}}
    <div class="modal fade" id="blogCategoryCreateModal" tabindex="-1" aria-labelledby="blogCategoryCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blogCategoryCreateModalLabel">{{ __('Add category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form action="{{ route('blog-categories.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="bc-create-name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="bc-create-name" name="name" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="bc-create-slug">{{ __('Slug') }}</label>
                            <input
                                type="text"
                                class="form-control"
                                id="bc-create-slug"
                                name="slug"
                                maxlength="255"
                                placeholder="{{ __('Leave empty to auto-generate from name') }}"
                            />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="bc-create-status">{{ __('Status') }}</label>
                            <select class="form-select" id="bc-create-status" name="status" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit --}}
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="blogCategoryEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blogCategoryEditModalLabel">{{ __('Edit category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="blog-category-edit-form" action="#" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="bc-edit-name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="bc-edit-name" name="name" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="bc-edit-slug">{{ __('Slug') }}</label>
                            <input
                                type="text"
                                class="form-control"
                                id="bc-edit-slug"
                                name="slug"
                                maxlength="255"
                                placeholder="{{ __('Leave empty to auto-generate from name') }}"
                            />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="bc-edit-status">{{ __('Status') }}</label>
                            <select class="form-select" id="bc-edit-status" name="status" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            if ($.fn.DataTable && $('#blog-categories-table tbody tr').length > 0 && $('#blog-categories-table tbody tr td[colspan]').length === 0) {
                $('#blog-categories-table').DataTable({
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: 3
                    }]
                });
            }

            $(document).on('click', '.js-blog-category-edit', function() {
                var btn = $(this);
                $('#blog-category-edit-form').attr('action', btn.data('update-url'));
                $('#bc-edit-name').val(btn.data('name'));
                $('#bc-edit-slug').val(btn.data('slug'));
                $('#bc-edit-status').val(btn.data('status'));
                var modal = new bootstrap.Modal(document.getElementById('crudModal'));
                modal.show();
            });

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-blog-category-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }
                row.find('.bc-name').text(data.name);
                row.find('.bc-slug code').text(data.slug);
                var badgeClass = data.status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
                row.find('.bc-status').html(
                    '<span class="badge ' + badgeClass + '">' + data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</span>'
                );
                var editBtn = row.find('.js-blog-category-edit');
                editBtn.attr('data-name', data.name);
                editBtn.attr('data-slug', data.slug);
                editBtn.attr('data-status', data.status);
            };

            ajaxUpdate('#blog-category-edit-form');
            ajaxDelete('.js-blog-category-delete', 'tr', null, null);
        });
    </script>
@endpush
