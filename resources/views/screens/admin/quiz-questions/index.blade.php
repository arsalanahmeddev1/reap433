@extends('layouts.admin.master')
@section('title', __('Quiz Question'))

@include('screens.admin.quiz-questions.partials.quiz-question-quill-scripts')

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
                        <h5 class="mb-0 f-w-600">{{ __('Quiz Question') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#quizQuestionCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add question') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="quiz-questions-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Question') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Category') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Type') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($quizQuestions as $quizQuestion)
                                            <tr class="product-removes inbox-data" data-quiz-question-id="{{ $quizQuestion->id }}">
                                                <td class="qq-question">
                                                    <template class="qq-description-template">{!! $quizQuestion->description !!}</template>
                                                    <span class="qq-question-text">{{ $quizQuestion->question }}</span>
                                                </td>
                                                <td class="qq-category">{{ $quizQuestion->category?->title ?: '—' }}</td>
                                                <td class="qq-type">{{ $quizQuestion->type?->title ?: '—' }}</td>
                                                <td class="qq-slug"><code class="text-reset">{{ $quizQuestion->slug }}</code></td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-quiz-question-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('quiz-questions.update', $quizQuestion) }}"
                                                            data-quiz-category-id="{{ $quizQuestion->quiz_category_id }}"
                                                            data-quiz-type-id="{{ $quizQuestion->quiz_type_id }}"
                                                            data-question="{{ $quizQuestion->question }}"
                                                            data-seo-title="{{ $quizQuestion->seo_title }}"
                                                            data-seo-description="{{ $quizQuestion->seo_description }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('quiz-questions.destroy', $quizQuestion) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-quiz-question-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <h3 class="pt-5">{{ __('No quiz questions found') }}</h3>
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
    <div class="modal fade" id="quizQuestionCreateModal" tabindex="-1" aria-labelledby="quizQuestionCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizQuestionCreateModalLabel">{{ __('Add question') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-question-create-form" action="{{ route('quiz-questions.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-create-category">{{ __('Category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qq-create-category" name="quiz_category_id" required>
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-create-type">{{ __('Quiz Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qq-create-type" name="quiz_type_id" required>
                                <option value="">{{ __('Select type') }}</option>
                                @foreach ($quizTypes as $quizType)
                                    <option value="{{ $quizType->id }}">{{ $quizType->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-create-question">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qq-create-question" name="question" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-create-description">{{ __('Description') }}</label>
                            <div id="qq_create_description_editor" class="quiz-question-quill-wrap"></div>
                            <textarea id="qq-create-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-create-seo-title">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="qq-create-seo-title" name="seo_title" maxlength="255" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="qq-create-seo-description">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="qq-create-seo-description" name="seo_description" rows="3"></textarea>
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
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="quizQuestionEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizQuestionEditModalLabel">{{ __('Edit question') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-question-edit-form" action="#" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-edit-category">{{ __('Category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qq-edit-category" name="quiz_category_id" required>
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-edit-type">{{ __('Quiz Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qq-edit-type" name="quiz_type_id" required>
                                <option value="">{{ __('Select type') }}</option>
                                @foreach ($quizTypes as $quizType)
                                    <option value="{{ $quizType->id }}">{{ $quizType->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-edit-question">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qq-edit-question" name="question" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-edit-description">{{ __('Description') }}</label>
                            <div id="qq_edit_description_editor" class="quiz-question-quill-wrap"></div>
                            <textarea id="qq-edit-description" name="description" class="d-none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qq-edit-seo-title">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="qq-edit-seo-title" name="seo_title" maxlength="255" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="qq-edit-seo-description">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="qq-edit-seo-description" name="seo_description" rows="3"></textarea>
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
            if ($.fn.DataTable && $('#quiz-questions-table tbody tr').length > 0 && $('#quiz-questions-table tbody tr td[colspan]').length === 0) {
                $('#quiz-questions-table').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [{ orderable: false, targets: [4] }]
                });
            }

            $(document).on('click', '.js-quiz-question-edit', function() {
                var btn = $(this);
                var row = btn.closest('tr');
                var descriptionHtml = '';
                var tpl = row.find('template.qq-description-template');
                if (tpl.length) {
                    descriptionHtml = tpl.html() || '';
                }

                $('#quiz-question-edit-form').attr('action', btn.data('update-url'));
                $('#qq-edit-category').val(String(btn.data('quiz-category-id') || ''));
                $('#qq-edit-type').val(String(btn.data('quiz-type-id') || ''));
                $('#qq-edit-question').val(btn.data('question') || '');
                $('#qq-edit-seo-title').val(btn.data('seo-title') || '');
                $('#qq-edit-seo-description').val(btn.data('seo-description') || '');

                if (window.setQuizQuestionQuillContent && window.quizQuestionQuillEditors.edit) {
                    setQuizQuestionQuillContent(window.quizQuestionQuillEditors.edit, descriptionHtml);
                    syncQuizQuestionQuill(window.quizQuestionQuillEditors.edit, 'qq-edit-description');
                } else {
                    $('#qq-edit-description').val(descriptionHtml);
                }

                var modal = new bootstrap.Modal(document.getElementById('crudModal'));
                modal.show();
            });

            $('#quizQuestionCreateModal').on('hidden.bs.modal', function() {
                if (window.setQuizQuestionQuillContent && window.quizQuestionQuillEditors.create) {
                    setQuizQuestionQuillContent(window.quizQuestionQuillEditors.create, '');
                    syncQuizQuestionQuill(window.quizQuestionQuillEditors.create, 'qq-create-description');
                }
            });

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-quiz-question-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }
                row.find('.qq-question-text').text(data.question || '');
                row.find('.qq-category').text(data.category_title || '—');
                row.find('.qq-type').text(data.type_title || '—');
                row.find('.qq-slug code').text(data.slug);
                row.find('template.qq-description-template').html(data.description || '');
                var editBtn = row.find('.js-quiz-question-edit');
                editBtn.attr('data-quiz-category-id', data.quiz_category_id || '');
                editBtn.attr('data-quiz-type-id', data.quiz_type_id || '');
                editBtn.attr('data-question', data.question || '');
                editBtn.attr('data-seo-title', data.seo_title || '');
                editBtn.attr('data-seo-description', data.seo_description || '');
            };

            ajaxUpdate('#quiz-question-edit-form');
            ajaxDelete('.js-quiz-question-delete', 'tr', null, null);
        });
    </script>
@endpush
