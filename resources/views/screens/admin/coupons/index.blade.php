@extends('layouts.admin.master')
@section('title', __('Coupon Management'))

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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0 f-w-600">{{ __('Coupon Management') }}</h5>
                        <button type="button" class="btn btn-primary f-w-500" data-bs-toggle="modal" data-bs-target="#couponCreateModal">
                            <i class="fa-solid fa-plus pe-2"></i>{{ __('Add coupon') }}
                        </button>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="coupons-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Image') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Title') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Coupon code') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Discount') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($coupons as $coupon)
                                            @php
                                                $statusBadge = $coupon->status === 'active'
                                                    ? 'badge-light-success'
                                                    : 'badge-light-secondary';
                                            @endphp
                                            <tr class="product-removes inbox-data" data-coupon-id="{{ $coupon->id }}">
                                                <td>
                                                    @if ($coupon->imageUrl())
                                                        <img
                                                            src="{{ $coupon->imageUrl() }}"
                                                            alt="{{ $coupon->title }}"
                                                            width="40"
                                                            height="40"
                                                            class="rounded"
                                                            style="object-fit: cover;"
                                                        >
                                                    @else
                                                        <span class="c-o-light">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->title }}</td>
                                                <td><code class="text-reset">{{ $coupon->coupon_code }}</code></td>
                                                <td><code class="text-reset">{{ $coupon->slug }}</code></td>
                                                <td>{{ $coupon->discount_in_percent }}%</td>
                                                <td>
                                                    <span class="badge {{ $statusBadge }}">{{ ucfirst($coupon->status) }}</span>
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <button
                                                            type="button"
                                                            class="square-white js-coupon-edit border-0 p-0"
                                                            title="{{ __('Edit') }}"
                                                            data-update-url="{{ route('coupons.update', $coupon) }}"
                                                            data-title="{{ $coupon->title }}"
                                                            data-coupon-code="{{ $coupon->coupon_code }}"
                                                            data-description="{{ $coupon->description }}"
                                                            data-discount="{{ $coupon->discount_in_percent }}"
                                                            data-status="{{ $coupon->status }}"
                                                            data-image-url="{{ $coupon->imageUrl() }}"
                                                        >
                                                            <span><i class="fa-solid fa-pen"></i></span>
                                                        </button>
                                                        <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this coupon?') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="square-white border-0" title="{{ __('Delete') }}">
                                                                <span><i class="fa-solid fa-trash"></i></span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <h3 class="pt-5">{{ __('No coupons found') }}</h3>
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
    <div class="modal fade" id="couponCreateModal" tabindex="-1" aria-labelledby="couponCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponCreateModalLabel">{{ __('Add coupon') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form action="{{ route('coupons.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-create-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="coupon-create-title" name="title" value="{{ old('title') }}" required maxlength="255" />
                            <div class="form-text">{{ __('Slug is auto-generated from the title.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-create-code">{{ __('Coupon code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="coupon-create-code" name="coupon_code" value="{{ old('coupon_code') }}" required maxlength="100" placeholder="{{ __('e.g. REAP10') }}" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-create-discount">{{ __('Discount (%)') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="coupon-create-discount" name="discount_in_percent" required>
                                <option value="">{{ __('Select discount') }}</option>
                                @for ($i = 1; $i <= 100; $i++)
                                    <option value="{{ $i }}" @selected((string) old('discount_in_percent') === (string) $i)>{{ $i }}%</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-create-description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="coupon-create-description" name="description" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-create-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="coupon-create-image"
                                name="image"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,image/avif"
                            />
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="coupon-create-status">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="coupon-create-status" name="status" required>
                                <option value="active" @selected(old('status', 'active') === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>{{ __('Inactive') }}</option>
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
    <div class="modal fade" id="couponEditModal" tabindex="-1" aria-labelledby="couponEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponEditModalLabel">{{ __('Edit coupon') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form id="coupon-edit-form" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-edit-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="coupon-edit-title" name="title" required maxlength="255" />
                            <div class="form-text">{{ __('Slug is auto-generated from the title.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-edit-code">{{ __('Coupon code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="coupon-edit-code" name="coupon_code" required maxlength="100" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-edit-discount">{{ __('Discount (%)') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="coupon-edit-discount" name="discount_in_percent" required>
                                @for ($i = 1; $i <= 100; $i++)
                                    <option value="{{ $i }}">{{ $i }}%</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-edit-description">{{ __('Description') }}</label>
                            <textarea class="form-control" id="coupon-edit-description" name="description" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label f-w-500" for="coupon-edit-image">{{ __('Image') }}</label>
                            <input
                                type="file"
                                class="form-control"
                                id="coupon-edit-image"
                                name="image"
                                accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,image/avif"
                            />
                            <div id="coupon-edit-image-preview-wrap" class="mt-2 d-none">
                                <img id="coupon-edit-image-preview" src="" alt="" width="80" height="80" class="rounded" style="object-fit: cover;">
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="coupon-edit-remove-image" name="remove_image" value="1">
                                <label class="form-check-label" for="coupon-edit-remove-image">{{ __('Remove current image') }}</label>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label f-w-500" for="coupon-edit-status">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="coupon-edit-status" name="status" required>
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
            if ($.fn.DataTable && $('#coupons-table tbody tr').length > 0 && $('#coupons-table tbody tr td[colspan]').length === 0) {
                $('#coupons-table').DataTable({
                    order: [[1, 'asc']],
                    columnDefs: [{ orderable: false, targets: [0, 6] }]
                });
            }

            $(document).on('click', '.js-coupon-edit', function() {
                var btn = $(this);
                $('#coupon-edit-form').attr('action', btn.data('update-url'));
                $('#coupon-edit-title').val(btn.data('title'));
                $('#coupon-edit-code').val(btn.data('coupon-code'));
                $('#coupon-edit-description').val(btn.data('description') || '');
                $('#coupon-edit-discount').val(String(btn.data('discount')));
                $('#coupon-edit-status').val(btn.data('status'));
                $('#coupon-edit-remove-image').prop('checked', false);
                $('#coupon-edit-image').val('');

                var imageUrl = btn.data('image-url');
                if (imageUrl) {
                    $('#coupon-edit-image-preview').attr('src', imageUrl);
                    $('#coupon-edit-image-preview-wrap').removeClass('d-none');
                } else {
                    $('#coupon-edit-image-preview').attr('src', '');
                    $('#coupon-edit-image-preview-wrap').addClass('d-none');
                }

                var modal = new bootstrap.Modal(document.getElementById('couponEditModal'));
                modal.show();
            });
        });
    </script>
@endpush
