@extends('layouts.admin.master')
@section('title', __('Quiz Type'))

@include('screens.admin.quiz-types.partials.quiz-type-quill-scripts')

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
                        <h5 class="mb-0 f-w-600">{{ __('Quiz Type') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#quizTypeCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add type') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="quiz-types-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Image') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slogan') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($quizTypes as $quizType)
                                            <tr class="product-removes inbox-data" data-quiz-type-id="{{ $quizType->id }}">
                                                <td class="qt-image">
                                                    <template class="qt-description-template">{!! $quizType->description !!}</template>
                                                    @if ($quizType->imageUrl())
                                                        <img src="{{ $quizType->imageUrl() }}" alt="{{ $quizType->title }}" width="48" height="48" class="rounded object-fit-cover" />
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="qt-title">{{ $quizType->title }}</td>
                                                <td class="qt-slogan">{{ $quizType->slogan_text ?: '—' }}</td>
                                                <td class="qt-slug"><code class="text-reset">{{ $quizType->slug }}</code></td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-quiz-type-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('quiz-types.update', $quizType) }}"
                                                            data-title="{{ $quizType->title }}"
                                                            data-slogan-text="{{ $quizType->slogan_text }}"
                                                            data-seo-title="{{ $quizType->seo_title }}"
                                                            data-seo-description="{{ $quizType->seo_description }}"
                                                            data-image-url="{{ $quizType->imageUrl() }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('quiz-types.destroy', $quizType) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-quiz-type-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <h3 class="pt-5">{{ __('No quiz types found') }}</h3>
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
    <div class="modal fade" id="quizTypeCreateModal" tabindex="-1" aria-labelledby="quizTypeCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizTypeCreateModalLabel">{{ __('Add type') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-type-create-form" action="{{ route('quiz-types.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-create-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qt-create-title" name="title" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-create-slogan">{{ __('Slogan Text') }}</label>
                            <input type="text" class="form-control" id="qt-create-slogan" name="slogan_text" maxlength="255" placeholder="{{ __('e.g. Easy, Medium, Hard') }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-create-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="qt-create-image"
                                name="image_url"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-create-description">{{ __('Description') }}</label>
                            <div id="qt_create_description_editor" class="quiz-type-quill-wrap"></div>
                            <textarea id="qt-create-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-create-seo-title">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="qt-create-seo-title" name="seo_title" maxlength="255" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="qt-create-seo-description">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="qt-create-seo-description" name="seo_description" rows="3"></textarea>
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
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="quizTypeEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizTypeEditModalLabel">{{ __('Edit type') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-type-edit-form" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-edit-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qt-edit-title" name="title" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-edit-slogan">{{ __('Slogan Text') }}</label>
                            <input type="text" class="form-control" id="qt-edit-slogan" name="slogan_text" maxlength="255" placeholder="{{ __('e.g. Easy, Medium, Hard') }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-edit-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="qt-edit-image"
                                name="image_url"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                            />
                            <img id="qt-edit-image-current" class="mt-2 rounded d-none" alt="" width="80" height="80" style="object-fit: cover;" />
                            <div id="qt-remove-image-wrap" class="form-check mt-2 d-none">
                                <input class="form-check-input" type="checkbox" value="1" id="qt-remove-image" name="remove_image" />
                                <label class="form-check-label" for="qt-remove-image">{{ __('Remove current image') }}</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-edit-description">{{ __('Description') }}</label>
                            <div id="qt_edit_description_editor" class="quiz-type-quill-wrap"></div>
                            <textarea id="qt-edit-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qt-edit-seo-title">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="qt-edit-seo-title" name="seo_title" maxlength="255" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="qt-edit-seo-description">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="qt-edit-seo-description" name="seo_description" rows="3"></textarea>
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
            if ($.fn.DataTable && $('#quiz-types-table tbody tr').length > 0 && $('#quiz-types-table tbody tr td[colspan]').length === 0) {
                $('#quiz-types-table').DataTable({
                    order: [[1, 'asc']],
                    columnDefs: [{ orderable: false, targets: [0, 4] }]
                });
            }

            $(document).on('click', '.js-quiz-type-edit', function() {
                var btn = $(this);
                var row = btn.closest('tr');
                var descriptionHtml = '';
                var tpl = row.find('template.qt-description-template');
                if (tpl.length) {
                    descriptionHtml = tpl.html() || '';
                }

                $('#quiz-type-edit-form').attr('action', btn.data('update-url'));
                $('#qt-edit-title').val(btn.data('title'));
                $('#qt-edit-slogan').val(btn.data('slogan-text') || '');
                $('#qt-edit-seo-title').val(btn.data('seo-title') || '');
                $('#qt-edit-seo-description').val(btn.data('seo-description') || '');
                $('#qt-edit-image').val('');
                $('#qt-remove-image').prop('checked', false);

                if (window.setQuizTypeQuillContent && window.quizTypeQuillEditors.edit) {
                    setQuizTypeQuillContent(window.quizTypeQuillEditors.edit, descriptionHtml);
                    syncQuizTypeQuill(window.quizTypeQuillEditors.edit, 'qt-edit-description');
                } else {
                    $('#qt-edit-description').val(descriptionHtml);
                }

                var imageUrl = btn.data('image-url');
                var currentImage = $('#qt-edit-image-current');
                var removeWrap = $('#qt-remove-image-wrap');
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

            $('#quizTypeCreateModal').on('hidden.bs.modal', function() {
                if (window.setQuizTypeQuillContent && window.quizTypeQuillEditors.create) {
                    setQuizTypeQuillContent(window.quizTypeQuillEditors.create, '');
                    syncQuizTypeQuill(window.quizTypeQuillEditors.create, 'qt-create-description');
                }
            });

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-quiz-type-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }
                row.find('.qt-title').text(data.title);
                row.find('.qt-slogan').text(data.slogan_text || '—');
                row.find('.qt-slug code').text(data.slug);
                row.find('template.qt-description-template').html(data.description || '');
                if (data.image_url) {
                    row.find('.qt-image').find('img').remove();
                    row.find('.qt-image').find('span.text-muted').remove();
                    row.find('.qt-image').append(
                        '<img src="' + data.image_url + '" alt="' + data.title + '" width="48" height="48" class="rounded object-fit-cover" />'
                    );
                } else {
                    row.find('.qt-image').find('img').remove();
                    if (!row.find('.qt-image span.text-muted').length) {
                        row.find('.qt-image').append('<span class="text-muted">—</span>');
                    }
                }
                var editBtn = row.find('.js-quiz-type-edit');
                editBtn.attr('data-title', data.title);
                editBtn.attr('data-slogan-text', data.slogan_text || '');
                editBtn.attr('data-seo-title', data.seo_title || '');
                editBtn.attr('data-seo-description', data.seo_description || '');
                editBtn.attr('data-image-url', data.image_url || '');
            };

            ajaxUpdate('#quiz-type-edit-form');
            ajaxDelete('.js-quiz-type-delete', 'tr', null, null);
        });
    </script>
@endpush
