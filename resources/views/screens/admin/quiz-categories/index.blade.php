@extends('layouts.admin.master')
@section('title', __('Quiz Category'))

@include('screens.admin.quiz-categories.partials.quiz-category-quill-scripts')

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
                        <h5 class="mb-0 f-w-600">{{ __('Quiz Category') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#quizCategoryCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add category') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="quiz-categories-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Image') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Difficulty') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($categories as $category)
                                            <tr class="product-removes inbox-data" data-quiz-category-id="{{ $category->id }}">
                                                <td class="qc-image">
                                                    <template class="qc-description-template">{!! $category->description !!}</template>
                                                    @if ($category->imageUrl())
                                                        <img src="{{ $category->imageUrl() }}" alt="{{ $category->title }}" width="48" height="48" class="rounded object-fit-cover" />
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="qc-title">{{ $category->title }}</td>
                                                <td class="qc-difficulty">{{ $category->quizTypes->pluck('title')->implode(', ') ?: '—' }}</td>
                                                <td class="qc-slug"><code class="text-reset">{{ $category->slug }}</code></td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-quiz-category-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('quiz-categories.update', $category) }}"
                                                            data-title="{{ $category->title }}"
                                                            data-seo-title="{{ $category->seo_title }}"
                                                            data-seo-description="{{ $category->seo_description }}"
                                                            data-quiz-type-ids="{{ $category->quizTypes->pluck('id')->implode(',') }}"
                                                            data-image-url="{{ $category->imageUrl() }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('quiz-categories.destroy', $category) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-quiz-category-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
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

    {{-- Create --}}
    <div class="modal fade" id="quizCategoryCreateModal" tabindex="-1" aria-labelledby="quizCategoryCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizCategoryCreateModalLabel">{{ __('Add category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-category-create-form" action="{{ route('quiz-categories.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-create-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qc-create-title" name="title" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-create-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="qc-create-image"
                                name="image_url"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-create-description">{{ __('Description') }}</label>
                            <div id="qc_create_description_editor" class="quiz-category-quill-wrap"></div>
                            <textarea id="qc-create-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500">{{ __('Difficulty') }} <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($quizTypes as $quizType)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input qc-create-quiz-type"
                                            type="checkbox"
                                            name="quiz_type_ids[]"
                                            id="qc-create-quiz-type-{{ $quizType->id }}"
                                            value="{{ $quizType->id }}"
                                        />
                                        <label class="form-check-label" for="qc-create-quiz-type-{{ $quizType->id }}">
                                            {{ $quizType->title }}{{ $quizType->slogan_text ? ' ('.$quizType->slogan_text.')' : '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-create-seo-title">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="qc-create-seo-title" name="seo_title" maxlength="255" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="qc-create-seo-description">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="qc-create-seo-description" name="seo_description" rows="3"></textarea>
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
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="quizCategoryEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizCategoryEditModalLabel">{{ __('Edit category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-category-edit-form" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-edit-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qc-edit-title" name="title" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-edit-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="qc-edit-image"
                                name="image_url"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                            />
                            <img id="qc-edit-image-current" class="mt-2 rounded d-none" alt="" width="80" height="80" style="object-fit: cover;" />
                            <div id="qc-remove-image-wrap" class="form-check mt-2 d-none">
                                <input class="form-check-input" type="checkbox" value="1" id="qc-remove-image" name="remove_image" />
                                <label class="form-check-label" for="qc-remove-image">{{ __('Remove current image') }}</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-edit-description">{{ __('Description') }}</label>
                            <div id="qc_edit_description_editor" class="quiz-category-quill-wrap"></div>
                            <textarea id="qc-edit-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500">{{ __('Difficulty') }} <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($quizTypes as $quizType)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input qc-edit-quiz-type"
                                            type="checkbox"
                                            name="quiz_type_ids[]"
                                            id="qc-edit-quiz-type-{{ $quizType->id }}"
                                            value="{{ $quizType->id }}"
                                        />
                                        <label class="form-check-label" for="qc-edit-quiz-type-{{ $quizType->id }}">
                                            {{ $quizType->title }}{{ $quizType->slogan_text ? ' ('.$quizType->slogan_text.')' : '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qc-edit-seo-title">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="qc-edit-seo-title" name="seo_title" maxlength="255" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="qc-edit-seo-description">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="qc-edit-seo-description" name="seo_description" rows="3"></textarea>
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
            if ($.fn.DataTable && $('#quiz-categories-table tbody tr').length > 0 && $('#quiz-categories-table tbody tr td[colspan]').length === 0) {
                $('#quiz-categories-table').DataTable({
                    order: [[1, 'asc']],
                    columnDefs: [{ orderable: false, targets: [0, 4] }]
                });
            }

            function setQuizTypeCheckboxes(selector, ids) {
                $(selector).prop('checked', false);
                (ids || []).forEach(function(id) {
                    $(selector + '[value="' + id + '"]').prop('checked', true);
                });
            }

            $(document).on('click', '.js-quiz-category-edit', function() {
                var btn = $(this);
                var row = btn.closest('tr');
                var descriptionHtml = '';
                var tpl = row.find('template.qc-description-template');
                if (tpl.length) {
                    descriptionHtml = tpl.html() || '';
                }

                $('#quiz-category-edit-form').attr('action', btn.data('update-url'));
                $('#qc-edit-title').val(btn.data('title'));
                $('#qc-edit-seo-title').val(btn.data('seo-title') || '');
                $('#qc-edit-seo-description').val(btn.data('seo-description') || '');
                $('#qc-edit-image').val('');
                $('#qc-remove-image').prop('checked', false);

                var typeIds = String(btn.attr('data-quiz-type-ids') || '')
                    .split(',')
                    .map(function(v) { return v.trim(); })
                    .filter(function(v) { return v !== ''; });
                setQuizTypeCheckboxes('.qc-edit-quiz-type', typeIds);

                if (window.setQuizCategoryQuillContent && window.quizCategoryQuillEditors.edit) {
                    setQuizCategoryQuillContent(window.quizCategoryQuillEditors.edit, descriptionHtml);
                    syncQuizCategoryQuill(window.quizCategoryQuillEditors.edit, 'qc-edit-description');
                } else {
                    $('#qc-edit-description').val(descriptionHtml);
                }

                var imageUrl = btn.data('image-url');
                var currentImage = $('#qc-edit-image-current');
                var removeWrap = $('#qc-remove-image-wrap');
                if (imageUrl) {
                    currentImage.attr('src', imageUrl).removeClass('d-none');
                    removeWrap.removeClass('d-none');
                } else {
                    currentImage.addClass('d-none').attr('src', '');
                    removeWrap.addClass('d-none');
                }

                var modal = new bootstrap.Modal(document.getElementById('crudModal'));
                modal.show();
            });

            $('#quizCategoryCreateModal').on('hidden.bs.modal', function() {
                if (window.setQuizCategoryQuillContent && window.quizCategoryQuillEditors.create) {
                    setQuizCategoryQuillContent(window.quizCategoryQuillEditors.create, '');
                    syncQuizCategoryQuill(window.quizCategoryQuillEditors.create, 'qc-create-description');
                }
                $('.qc-create-quiz-type').prop('checked', false);
            });

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-quiz-category-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }
                row.find('.qc-title').text(data.title);
                row.find('.qc-difficulty').text(data.difficulty_titles || '—');
                row.find('.qc-slug code').text(data.slug);
                row.find('template.qc-description-template').html(data.description || '');
                if (data.image_url) {
                    row.find('.qc-image').find('img').remove();
                    row.find('.qc-image').find('span.text-muted').remove();
                    row.find('.qc-image').append(
                        '<img src="' + data.image_url + '" alt="' + data.title + '" width="48" height="48" class="rounded object-fit-cover" />'
                    );
                } else {
                    row.find('.qc-image').find('img').remove();
                    if (!row.find('.qc-image span.text-muted').length) {
                        row.find('.qc-image').append('<span class="text-muted">—</span>');
                    }
                }
                var editBtn = row.find('.js-quiz-category-edit');
                editBtn.attr('data-title', data.title);
                editBtn.attr('data-seo-title', data.seo_title || '');
                editBtn.attr('data-seo-description', data.seo_description || '');
                editBtn.attr('data-quiz-type-ids', (data.quiz_type_ids || []).join(','));
                editBtn.attr('data-image-url', data.image_url || '');
            };

            ajaxUpdate('#quiz-category-edit-form');
            ajaxDelete('.js-quiz-category-delete', 'tr', null, null);
        });
    </script>
@endpush
