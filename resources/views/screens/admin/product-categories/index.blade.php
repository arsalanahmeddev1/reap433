@extends('layouts.admin.master')
@section('title', 'All Categories')

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border d-flex flex-wrap justify-content-end align-items-center gap-2">
                        <div class="card-header-right-icon">
                            <button
                                type="button"
                                class="btn btn-primary f-w-500"
                                data-bs-toggle="modal"
                                data-bs-target="#categoryCreateModal"
                            >
                                <i class="fa-solid fa-plus pe-2"></i>{{ __('Add category') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="categories-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">Name</span></th>
                                            <th><span class="c-o-light f-w-600">Slug</span></th>
                                            <th><span class="c-o-light f-w-600">Status</span></th>
                                            <th><span class="c-o-light f-w-600">Actions</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($categories as $category)
                                            @include('screens.admin.product-categories.partials.table-row', ['category' => $category])
                                        @empty
                                            <tr class="categories-empty-row">
                                                <td colspan="5" class="text-center">
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

    {{-- Create category --}}
    <div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-labelledby="categoryCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryCreateModalLabel">{{ __('Add category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="category-create-form" action="{{ route('product-categories.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-create-name">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="category-create-name"
                                name="name"
                                required
                                maxlength="255"
                            />
                            <div class="form-text">{{ __('Slug is auto-generated from the name.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-create-parent">{{ __('Parent category') }}</label>
                            <select class="form-select" id="category-create-parent" name="parent_id">
                                <option value="0">{{ __('None (top-level category)') }}</option>
                                @foreach ($categories->where('parent_id', 0) as $parentCategory)
                                    <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Choose a parent to create this as a subcategory.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-create-description">{{ __('Description') }}</label>
                            <div id="category_create_description_editor" class="category-quill-wrap"></div>
                            <textarea id="category-create-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-create-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="category-create-image"
                                name="image"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,image/avif"
                            />
                            <img id="category-create-image-preview" class="image-preview mt-2 d-none" alt="" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="category-create-status">{{ __('Status') }}</label>
                            <select class="form-select" id="category-create-status" name="status" required>
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

    {{-- Edit category (Bootstrap modal; closed by ajax-update.js on success) --}}
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crudModalLabel">{{ __('Edit category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="category-edit-form" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-name">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control"
                                id="category-name"
                                name="name"
                                required
                                maxlength="255"
                            >
                            <div class="form-text">{{ __('Slug updates automatically when the name changes.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-parent-id">{{ __('Parent category') }}</label>
                            <select class="form-select" id="category-parent-id" name="parent_id">
                                <option value="0">{{ __('None (top-level category)') }}</option>
                                @foreach ($categories->where('parent_id', 0) as $parentCategory)
                                    <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Choose a parent to save this as a subcategory.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="category-description">{{ __('Description') }}</label>
                            <div id="category_edit_description_editor" class="category-quill-wrap"></div>
                            <textarea id="category-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500 d-block">{{ __('Current image') }}</label>
                            <img id="category-edit-image-current" class="image-preview mb-2 d-none" alt="" />
                            <div class="form-check mb-2 d-none" id="category-remove-image-wrap">
                                <input class="form-check-input" type="checkbox" id="category-remove-image" name="remove_image" value="1">
                                <label class="form-check-label" for="category-remove-image">{{ __('Remove current image') }}</label>
                            </div>
                            <label class="form-label f-w-500" for="category-image">{{ __('Replace image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="category-image"
                                name="image"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,image/avif"
                            />
                            <img id="category-edit-image-preview" class="image-preview mt-2 d-none" alt="" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="category-status">{{ __('Status') }}</label>
                            <select class="form-select" id="category-status" name="status" required>
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

@include('screens.admin.product-categories.partials.category-quill-scripts')

@push('scripts')
    <script>
        $(function() {
            function initCategoriesTable() {
                if (!$.fn.DataTable || !$('#categories-table').length) {
                    return null;
                }

                if ($.fn.DataTable.isDataTable('#categories-table')) {
                    return $('#categories-table').DataTable();
                }

                if ($('#categories-table tbody tr td[colspan]').length > 0) {
                    return null;
                }

                return $('#categories-table').DataTable({
                    order: [[1, 'desc']],
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 3]
                    }]
                });
            }

            initCategoriesTable();

            function previewImageFile(input, previewSelector) {
                var preview = $(previewSelector);
                if (!input.files || !input.files[0]) {
                    preview.addClass('d-none').attr('src', '');
                    return;
                }
                preview.attr('src', URL.createObjectURL(input.files[0])).removeClass('d-none');
            }

            $('#category-create-image').on('change', function() {
                previewImageFile(this, '#category-create-image-preview');
            });

            $('#category-image').on('change', function() {
                previewImageFile(this, '#category-edit-image-preview');
            });

            $('#categoryCreateModal').on('hidden.bs.modal', function() {
                if (window.setCategoryQuillContent && window.categoryQuillEditors.create) {
                    setCategoryQuillContent(window.categoryQuillEditors.create, '');
                    syncCategoryQuill(window.categoryQuillEditors.create, 'category-create-description');
                }
            });

            $(document).on('click', '.js-category-edit', function() {
                var btn = $(this);
                var row = btn.closest('tr[data-category-id]');
                var categoryId = row.data('category-id');
                var descriptionHtml = '';
                var tpl = row.find('template.category-description-template');
                if (tpl.length) {
                    descriptionHtml = tpl.html() || '';
                }

                $('#category-edit-form').attr('action', btn.data('update-url'));
                $('#category-name').val(btn.data('name'));
                $('#category-status').val(btn.data('status'));
                $('#category-parent-id option').prop('disabled', false);
                $('#category-parent-id option[value="' + categoryId + '"]').prop('disabled', true);
                $('#category-parent-id').val(String(btn.data('parent-id') || '0'));

                if (window.setCategoryQuillContent && window.categoryQuillEditors.edit) {
                    setCategoryQuillContent(window.categoryQuillEditors.edit, descriptionHtml);
                    syncCategoryQuill(window.categoryQuillEditors.edit, 'category-description');
                } else {
                    $('#category-description').val(descriptionHtml);
                }
                $('#category-image').val('');
                $('#category-remove-image').prop('checked', false);
                $('#category-edit-image-preview').addClass('d-none').attr('src', '');

                var imageUrl = btn.data('image-url');
                var currentImage = $('#category-edit-image-current');
                var removeWrap = $('#category-remove-image-wrap');

                if (imageUrl) {
                    currentImage.attr('src', imageUrl).removeClass('d-none');
                    removeWrap.removeClass('d-none');
                } else {
                    currentImage.addClass('d-none').attr('src', '');
                    removeWrap.addClass('d-none');
                }

                $('#crudModal').modal('show');
            });

            $('#category-create-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                if (window.categoryQuillEditors.create) {
                    syncCategoryQuill(window.categoryQuillEditors.create, 'category-create-description');
                }
                var submitBtn = form.find('button[type="submit"]');
                var btnText = submitBtn.text();
                var fd = new FormData(this);
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    beforeSend: function() {
                        submitBtn.prop('disabled', true).text(@json(__('Saving...')));
                    },
                    success: function(res) {
                        submitBtn.prop('disabled', false).text(btnText);
                        $('#categories-table tbody tr').has('td[colspan]').remove();
                        var $row = $(String(res.html).trim());
                        var table = initCategoriesTable();
                        if (table) {
                            table.row.add($row[0]).draw(false);
                        } else {
                            $('#categories-table tbody').append($row);
                            initCategoriesTable();
                        }
                        form[0].reset();
                        $('#category-create-image-preview').addClass('d-none').attr('src', '');
                        if (window.setCategoryQuillContent && window.categoryQuillEditors.create) {
                            setCategoryQuillContent(window.categoryQuillEditors.create, '');
                            syncCategoryQuill(window.categoryQuillEditors.create, 'category-create-description');
                        }
                        $('#categoryCreateModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message || @json(__('Created successfully.')),
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text(btnText);
                        if (xhr.status === 422) {
                            var response = xhr.responseJSON;
                            form.find('.invalid-feedback').remove();
                            form.find('.is-invalid').removeClass('is-invalid');
                            if (response.success === false && response.message) {
                                Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                            }
                            var globalErrors = [];
                            if (response.errors) {
                                $.each(response.errors, function(key, messages) {
                                    var input = form.find('[name="' + key + '"]');
                                    if (input.length) {
                                        input.addClass('is-invalid');
                                        input.after('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                                    } else {
                                        globalErrors.push(messages[0]);
                                    }
                                });
                            }
                            if (globalErrors.length > 0) {
                                Swal.fire({ icon: 'error', title: 'Validation Error', html: globalErrors.join('<br>') });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text:
                                    xhr.responseJSON && xhr.responseJSON.message
                                        ? xhr.responseJSON.message
                                        : @json(__('Something went wrong!')),
                            });
                        }
                    },
                });
            });

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-category-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }
                var imageCell = row.find('.category-image');
                if (data.image_url) {
                    imageCell.html('<img src="' + data.image_url + '" alt="" class="dashboard-sm-img rounded" />');
                } else {
                    imageCell.html('<span class="dim-white">—</span>');
                }
                row.find('.category-name').text(data.name);
                row.find('.category-slug code').text(data.slug);
                var badgeClass = data.status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
                row.find('.category-status').html(
                    '<span class="badge ' + badgeClass + '">' + data.status.charAt(0).toUpperCase() + data.status.slice(1) +
                    '</span>'
                );
                var editBtn = row.find('.js-category-edit');
                editBtn.attr('data-name', data.name);
                editBtn.attr('data-slug', data.slug);
                var tpl = row.find('template.category-description-template');
                if (tpl.length) {
                    tpl.html(data.description || '');
                }
                editBtn.attr('data-status', data.status);
                editBtn.attr('data-parent-id', data.parent_id || 0);
                editBtn.attr('data-image-url', data.image_url || '');
            };

            ajaxUpdate('#category-edit-form');
            ajaxDelete('.js-category-delete', 'tr', null, '#categories-table');
        });
    </script>
@endpush
