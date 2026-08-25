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
                                                            data-question-id="{{ $quizAnswer->question_id }}"
                                                            data-answers="{{ $quizAnswer->answers }}"
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
                            <label class="form-label f-w-500" for="qa-create-question">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-create-question" name="question_id" required>
                                <option value="">{{ __('Select question') }}</option>
                                @foreach ($questions as $question)
                                    <option value="{{ $question->id }}">{{ $question->question }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-answers">{{ __('Answer') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qa-create-answers" name="answers" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-create-description">{{ __('Description') }}</label>
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
                            <label class="form-label f-w-500" for="qa-edit-question">{{ __('Question') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="qa-edit-question" name="question_id" required>
                                <option value="">{{ __('Select question') }}</option>
                                @foreach ($questions as $question)
                                    <option value="{{ $question->id }}">{{ $question->question }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="qa-edit-answers">{{ __('Answer') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qa-edit-answers" name="answers" required maxlength="255" />
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
    <script>
        $(function() {
            if ($.fn.DataTable && $('#quiz-answers-table tbody tr').length > 0 && $('#quiz-answers-table tbody tr td[colspan]').length === 0) {
                $('#quiz-answers-table').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [{ orderable: false, targets: [5] }]
                });
            }

            $(document).on('click', '.js-quiz-answer-edit', function() {
                var btn = $(this);

                $('#quiz-answer-edit-form').attr('action', btn.data('update-url'));
                $('#qa-edit-question').val(String(btn.data('question-id') || ''));
                $('#qa-edit-answers').val(btn.data('answers') || '');
                $('#qa-edit-description').val(btn.data('description') || '');
                $('#qa-edit-xp').val(btn.data('xp') !== undefined && btn.data('xp') !== '' ? btn.data('xp') : '');
                $('#qa-edit-coins').val(btn.data('coins') !== undefined && btn.data('coins') !== '' ? btn.data('coins') : '');
                $('#qa-edit-is-right').val(String(btn.data('is-right') ?? 0));

                var modal = new bootstrap.Modal(document.getElementById('crudModal'));
                modal.show();
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
                editBtn.attr('data-question-id', data.question_id || '');
                editBtn.attr('data-answers', data.answers || '');
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
