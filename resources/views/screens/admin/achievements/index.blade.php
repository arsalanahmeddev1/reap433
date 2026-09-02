@extends('layouts.admin.master')
@section('title', __('Achievements'))

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
                        <h5 class="mb-0 f-w-600">{{ __('Achievements') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#achievementCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add achievement') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="achievements-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Image') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Description') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('XP') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Coins') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($achievements as $achievement)
                                            @php
                                                $statusBadge = $achievement->status === 'active'
                                                    ? 'badge-light-success'
                                                    : 'badge-light-secondary';
                                            @endphp
                                            <tr class="product-removes inbox-data" data-achievement-id="{{ $achievement->id }}">
                                                <td class="ach-image">
                                                    @if ($achievement->imageUrl())
                                                        <img src="{{ $achievement->imageUrl() }}" alt="{{ $achievement->title }}" width="48" height="48" class="rounded object-fit-cover" />
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="ach-title">{{ $achievement->title }}</td>
                                                <td class="ach-description">{{ $achievement->description ?: '—' }}</td>
                                                <td class="ach-xp">{{ $achievement->xp }}</td>
                                                <td class="ach-coins">{{ $achievement->coins }}</td>
                                                <td class="ach-status">
                                                    <span class="badge {{ $statusBadge }}">{{ ucfirst($achievement->status) }}</span>
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-achievement-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('achievements.update', $achievement) }}"
                                                            data-title="{{ $achievement->title }}"
                                                            data-description="{{ $achievement->description }}"
                                                            data-xp="{{ $achievement->xp }}"
                                                            data-coins="{{ $achievement->coins }}"
                                                            data-status="{{ $achievement->status }}"
                                                            data-image-url="{{ $achievement->imageUrl() }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('achievements.destroy', $achievement) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0 js-achievement-delete" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <h3 class="pt-5">{{ __('No achievements found') }}</h3>
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
    <div class="modal fade" id="achievementCreateModal" tabindex="-1" aria-labelledby="achievementCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="achievementCreateModalLabel">{{ __('Add achievement') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="achievement-create-form" action="{{ route('achievements.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="ach-create-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ach-create-title" name="title" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="ach-create-description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="ach-create-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label f-w-500" for="ach-create-xp">{{ __('XP') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="ach-create-xp" name="xp" min="0" value="0" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label f-w-500" for="ach-create-coins">{{ __('Coins') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="ach-create-coins" name="coins" min="0" value="0" required />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="ach-create-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="ach-create-image"
                                name="image_url"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                            />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="ach-create-status">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="ach-create-status" name="status" required>
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
    <div class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="achievementEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="achievementEditModalLabel">{{ __('Edit achievement') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="achievement-edit-form" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="ach-edit-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ach-edit-title" name="title" required maxlength="255" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="ach-edit-description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="ach-edit-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label f-w-500" for="ach-edit-xp">{{ __('XP') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="ach-edit-xp" name="xp" min="0" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label f-w-500" for="ach-edit-coins">{{ __('Coins') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="ach-edit-coins" name="coins" min="0" required />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="ach-edit-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="ach-edit-image"
                                name="image_url"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                            />
                            <img id="ach-edit-image-current" class="mt-2 rounded d-none" alt="" width="80" height="80" style="object-fit: cover;" />
                            <div id="ach-remove-image-wrap" class="form-check mt-2 d-none">
                                <input class="form-check-input" type="checkbox" value="1" id="ach-remove-image" name="remove_image" />
                                <label class="form-check-label" for="ach-remove-image">{{ __('Remove current image') }}</label>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="ach-edit-status">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="ach-edit-status" name="status" required>
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
            if ($.fn.DataTable && $('#achievements-table tbody tr').length > 0 && $('#achievements-table tbody tr td[colspan]').length === 0) {
                $('#achievements-table').DataTable({
                    order: [[1, 'asc']],
                    columnDefs: [{ orderable: false, targets: [0, 6] }]
                });
            }

            $(document).on('click', '.js-achievement-edit', function() {
                var btn = $(this);

                $('#achievement-edit-form').attr('action', btn.data('update-url'));
                $('#ach-edit-title').val(btn.data('title'));
                $('#ach-edit-description').val(btn.data('description') || '');
                $('#ach-edit-xp').val(btn.data('xp'));
                $('#ach-edit-coins').val(btn.data('coins'));
                $('#ach-edit-status').val(btn.data('status') || 'active');
                $('#ach-edit-image').val('');
                $('#ach-remove-image').prop('checked', false);

                var imageUrl = btn.data('image-url');
                var currentImage = $('#ach-edit-image-current');
                var removeWrap = $('#ach-remove-image-wrap');
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

            window.updateCategoryRow = function(data) {
                var row = $('tr[data-achievement-id="' + data.id + '"]');
                if (!row.length) {
                    return;
                }

                row.find('.ach-title').text(data.title);
                row.find('.ach-description').text(data.description || '—');
                row.find('.ach-xp').text(data.xp);
                row.find('.ach-coins').text(data.coins);

                var statusBadge = data.status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
                row.find('.ach-status').html('<span class="badge ' + statusBadge + '">' + (data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'Active') + '</span>');

                if (data.image_url) {
                    row.find('.ach-image').find('img').remove();
                    row.find('.ach-image').find('span.text-muted').remove();
                    row.find('.ach-image').append(
                        '<img src="' + data.image_url + '" alt="' + data.title + '" width="48" height="48" class="rounded object-fit-cover" />'
                    );
                } else {
                    row.find('.ach-image').find('img').remove();
                    if (!row.find('.ach-image span.text-muted').length) {
                        row.find('.ach-image').append('<span class="text-muted">—</span>');
                    }
                }

                var editBtn = row.find('.js-achievement-edit');
                editBtn.attr('data-title', data.title);
                editBtn.attr('data-description', data.description || '');
                editBtn.attr('data-xp', data.xp);
                editBtn.attr('data-coins', data.coins);
                editBtn.attr('data-status', data.status || 'active');
                editBtn.attr('data-image-url', data.image_url || '');
            };

            ajaxUpdate('#achievement-edit-form');
            ajaxDelete('.js-achievement-delete', 'tr', null, null);
        });
    </script>
@endpush
