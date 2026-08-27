@extends('layouts.admin.master')
@section('title', __('Quiz Answer'))

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
                        <h5 class="mb-0 f-w-600">{{ __('Quiz Answer') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#quizAnswerCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add answer') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="quiz-answers-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Answer') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Question') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('XP') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Coins') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Right') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($quizAnswers as $quizAnswer)
                                            <tr class="product-removes inbox-data" data-quiz-answer-id="{{ $quizAnswer->id }}">
                                                <td class="qa-answers">
                                                    <template class="qa-description-template">{{ $quizAnswer->description }}</template>
                                                    <span class="qa-answers-text">{{ $quizAnswer->answers }}</span>
                                                </td>
                                                <td class="qa-question">{{ $quizAnswer->question?->question ?: '—' }}</td>
                                                <td class="qa-xp">{{ $quizAnswer->xp ?? '—' }}</td>
                                                <td class="qa-coins">{{ $quizAnswer->coins ?? '—' }}</td>
                                                <td class="qa-is-right">
                                                    @if ($quizAnswer->is_right)
                                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-quiz-answer-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('quiz-answers.update', $quizAnswer) }}"
                                                            data-quiz-category-id="{{ $quizAnswer->question?->quiz_category_id }}"
                                                            data-quiz-type-id="{{ $quizAnswer->question?->quiz_type_id }}"
                                                            data-question-id="{{ $quizAnswer->question_id }}"
                                                            data-answers="{{ $quizAnswer->answers }}"
                                                            data-bible-title="{{ $quizAnswer->bible_title }}"
                                                            data-description="{{ $quizAnswer->description }}"
                                                            data-xp="{{ $quizAnswer->xp }}"
                                                            data-coins="{{ $quizAnswer->coins }}"
                                                            data-is-right="{{ $quizAnswer->is_right ? 1 : 0 }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('quiz-answers.destroy', $quizAnswer) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-quiz-answer-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <h3 class="pt-5">{{ __('No quiz answers found') }}</h3>
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
    <div class="modal fade" id="quizAnswerCreateModal" tabindex="-1" aria-labelledby="quizAnswerCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizAnswerCreateModalLabel">{{ __('Add answer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-answer-create-form" action="{{ route('quiz-answers.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-category">{{ __('Quiz Category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-create-category" required>
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-type">{{ __('Quiz Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-create-type" required>
                                <option value="">{{ __('Select type') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-question">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-create-question" name="question_id" required>
                                <option value="">{{ __('Select question') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-answers">{{ __('Answer') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qa-create-answers" name="answers" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-bible-title">{{ __('Bible Title') }}</label>
                            <input type="text" class="form-control" id="qa-create-bible-title" name="bible_title" maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-description">{{ __('Bible Description') }}</label>
                            <textarea class="form-control" id="qa-create-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label f-w-500" for="qa-create-xp">{{ __('XP') }}</label>
                                <input type="number" class="form-control" id="qa-create-xp" name="xp" min="0" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label f-w-500" for="qa-create-coins">{{ __('Coins') }}</label>
                                <input type="number" class="form-control" id="qa-create-coins" name="coins" min="0" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label f-w-500" for="qa-create-is-right">{{ __('Is Right') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="qa-create-is-right" name="is_right" required>
                                    <option value="0">{{ __('No (0)') }}</option>
                                    <option value="1">{{ __('Yes (1)') }}</option>
                                </select>
                            </div>
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
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="quizAnswerEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizAnswerEditModalLabel">{{ __('Edit answer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="quiz-answer-edit-form" action="#" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-category">{{ __('Quiz Category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-edit-category" required>
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-type">{{ __('Quiz Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-edit-type" required>
                                <option value="">{{ __('Select type') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-question">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-edit-question" name="question_id" required>
                                <option value="">{{ __('Select question') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-answers">{{ __('Answer') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qa-edit-answers" name="answers" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-bible-title">{{ __('Bible Title') }}</label>
                            <input type="text" class="form-control" id="qa-edit-bible-title" name="bible_title" maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="qa-edit-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label f-w-500" for="qa-edit-xp">{{ __('XP') }}</label>
                                <input type="number" class="form-control" id="qa-edit-xp" name="xp" min="0" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label f-w-500" for="qa-edit-coins">{{ __('Coins') }}</label>
                                <input type="number" class="form-control" id="qa-edit-coins" name="coins" min="0" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label f-w-500" for="qa-edit-is-right">{{ __('Is Right') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="qa-edit-is-right" name="is_right" required>
                                    <option value="0">{{ __('No (0)') }}</option>
                                    <option value="1">{{ __('Yes (1)') }}</option>
                                </select>
                            </div>
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
    @php
        $quizTypesByCategory = [];
        foreach ($categories as $category) {
            $quizTypesByCategory[(string) $category->id] = $category->quizTypes
                ->sortBy('title')
                ->map(fn ($type) => [
                    'id' => $type->id,
                    'title' => $type->title,
                ])
                ->values()
                ->all();
        }

        $questionsData = $questions->map(fn ($question) => [
            'id' => $question->id,
            'question' => $question->question,
            'quiz_category_id' => $question->quiz_category_id,
            'quiz_type_id' => $question->quiz_type_id,
        ])->values()->all();
    @endphp
    <script>
        $(function() {
            var quizTypesByCategory = @json($quizTypesByCategory);
            var questionsData = @json($questionsData);

            function fillQuizTypeSelect($select, categoryId, selectedTypeId) {
                $select.find('option:not(:first)').remove();
                $select.val('');

                if (! categoryId || ! quizTypesByCategory[categoryId]) {
                    return;
                }

                quizTypesByCategory[categoryId].forEach(function(type) {
                    $select.append(
                        $('<option></option>').attr('value', type.id).text(type.title)
                    );
                });

                if (selectedTypeId) {
                    $select.val(String(selectedTypeId));
                }
            }

            function fillQuestionSelect($select, categoryId, typeId, selectedQuestionId) {
                $select.find('option:not(:first)').remove();
                $select.val('');

                if (! categoryId || ! typeId) {
                    return;
                }

                questionsData.forEach(function(question) {
                    if (
                        String(question.quiz_category_id) === String(categoryId)
                        && String(question.quiz_type_id) === String(typeId)
                    ) {
                        $select.append(
                            $('<option></option>').attr('value', question.id).text(question.question)
                        );
                    }
                });

                if (selectedQuestionId) {
                    $select.val(String(selectedQuestionId));
                }
            }

            $('#qa-create-category').on('change', function() {
                fillQuizTypeSelect($('#qa-create-type'), $(this).val(), '');
                fillQuestionSelect($('#qa-create-question'), '', '', '');
            });

            $('#qa-create-type').on('change', function() {
                fillQuestionSelect(
                    $('#qa-create-question'),
                    $('#qa-create-category').val(),
                    $(this).val(),
                    ''
                );
            });

            $('#qa-edit-category').on('change', function() {
                fillQuizTypeSelect($('#qa-edit-type'), $(this).val(), '');
                fillQuestionSelect($('#qa-edit-question'), '', '', '');
            });

            $('#qa-edit-type').on('change', function() {
                fillQuestionSelect(
                    $('#qa-edit-question'),
                    $('#qa-edit-category').val(),
                    $(this).val(),
                    ''
                );
            });

            if ($.fn.DataTable && $('#quiz-answers-table tbody tr').length > 0 && $('#quiz-answers-table tbody tr td[colspan]').length === 0) {
                $('#quiz-answers-table').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [{ orderable: false, targets: [5] }]
                });
            }

            $(document).on('click', '.js-quiz-answer-edit', function() {
                var btn = $(this);
                var categoryId = String(btn.data('quiz-category-id') || '');
                var typeId = String(btn.data('quiz-type-id') || '');
                var questionId = String(btn.data('question-id') || '');

                $('#quiz-answer-edit-form').attr('action', btn.data('update-url'));
                $('#qa-edit-category').val(categoryId);
                fillQuizTypeSelect($('#qa-edit-type'), categoryId, typeId);
                fillQuestionSelect($('#qa-edit-question'), categoryId, typeId, questionId);
                $('#qa-edit-answers').val(btn.data('answers') || '');
                $('#qa-edit-bible-title').val(btn.data('bible-title') || '');
                $('#qa-edit-description').val(btn.data('description') || '');
                $('#qa-edit-xp').val(btn.data('xp') !== undefined && btn.data('xp') !== '' ? btn.data('xp') : '');
                $('#qa-edit-coins').val(btn.data('coins') !== undefined && btn.data('coins') !== '' ? btn.data('coins') : '');
                $('#qa-edit-is-right').val(String(btn.data('is-right') ?? 0));

                var modal = new bootstrap.Modal(document.getElementById('crudModal'));
                modal.show();
            });

            $('#quizAnswerCreateModal').on('hidden.bs.modal', function() {
                $('#qa-create-category').val('');
                fillQuizTypeSelect($('#qa-create-type'), '', '');
                fillQuestionSelect($('#qa-create-question'), '', '', '');
            });

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-quiz-answer-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }
                row.find('.qa-answers-text').text(data.answers || '');
                row.find('.qa-question').text(data.question_text || '—');
                row.find('.qa-xp').text(data.xp !== null && data.xp !== undefined ? data.xp : '—');
                row.find('.qa-coins').text(data.coins !== null && data.coins !== undefined ? data.coins : '—');
                row.find('template.qa-description-template').text(data.description || '');
                if (Number(data.is_right) === 1) {
                    row.find('.qa-is-right').html('<span class="badge bg-success">{{ __('Yes') }}</span>');
                    $('#quiz-answers-table tbody tr').each(function() {
                        var other = $(this);
                        if (other.data('quiz-answer-id') == data.id) {
                            return;
                        }
                        if (other.find('.js-quiz-answer-edit').data('question-id') == data.question_id) {
                            other.find('.qa-is-right').html('<span class="badge bg-secondary">{{ __('No') }}</span>');
                            other.find('.js-quiz-answer-edit').attr('data-is-right', 0);
                        }
                    });
                } else {
                    row.find('.qa-is-right').html('<span class="badge bg-secondary">{{ __('No') }}</span>');
                }
                var editBtn = row.find('.js-quiz-answer-edit');
                editBtn.attr('data-quiz-category-id', data.quiz_category_id || '');
                editBtn.attr('data-quiz-type-id', data.quiz_type_id || '');
                editBtn.attr('data-question-id', data.question_id || '');
                editBtn.attr('data-answers', data.answers || '');
                editBtn.attr('data-bible-title', data.bible_title || '');
                editBtn.attr('data-description', data.description || '');
                editBtn.attr('data-xp', data.xp !== null && data.xp !== undefined ? data.xp : '');
                editBtn.attr('data-coins', data.coins !== null && data.coins !== undefined ? data.coins : '');
                editBtn.attr('data-is-right', data.is_right ? 1 : 0);
            };

            ajaxUpdate('#quiz-answer-edit-form');
            ajaxDelete('.js-quiz-answer-delete', 'tr', null, null);
        });
    </script>
@endpush
