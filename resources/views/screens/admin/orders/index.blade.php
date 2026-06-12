@extends('layouts.admin.master')
@section('title', __('All Orders'))

@push('styles')
    <style>
        .order-status-select.order-status-select--themed {
            min-width: 10rem;
            max-width: 12rem;
            background-color: rgba(255, 255, 255, 0.06);
            color: #e8eaed;
            border-color: rgba(255, 255, 255, 0.14);
        }

        .order-status-select.order-status-select--themed:focus {
            border-color: #7366ff;
            box-shadow: 0 0 0 0.2rem rgba(115, 102, 255, 0.22);
            background-color: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .order-status-select.order-status-select--themed:disabled {
            opacity: 0.65;
            cursor: wait;
        }

        .order-status-select.order-status-select--themed option {
            background-color: #1e1e2d;
            color: #e8eaed;
        }
    </style>
@endpush

@section('content')
    @php
        $orderStatusOptions = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];
    @endphp
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-3 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="orders-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Order ID') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Customer') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Total') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Order Date') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $order)
                                            <tr class="product-removes inbox-data">
                                                <td>
                                                    <a href="{{ route('orders.show', $order) }}">{{ $order->publicOrderNumber() }}</a>
                                                </td>
                                                <td>
                                                    <p class="mb-0">{{ $order->user?->name ?? __('Guest') }}</p>
                                                    @if ($order->user?->email)
                                                        <small class="c-o-light">{{ $order->user->email }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <p class="mb-0">${{ number_format((float) $order->total, 2) }}</p>
                                                </td>
                                                <td>
                                                    @if ($order->order_status === 'completed')
                                                        <span class="badge badge-light-success">{{ ucfirst($order->order_status) }}</span>
                                                    @else
                                                        <select
                                                            class="form-select form-select-sm order-status-select order-status-select--themed"
                                                            aria-label="{{ __('Order status') }}"
                                                            data-order-id="{{ $order->id }}"
                                                            data-current-status="{{ $order->order_status }}"
                                                        >
                                                            @foreach ($orderStatusOptions as $status)
                                                                <option value="{{ $status }}" @selected($order->order_status === $status)>
                                                                    {{ ucfirst($status) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </td>
                                                <td>
                                                    <p class="mb-0">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                                                </td>
                                                <td>
                                                    <div class="common-align gap-2 justify-content-start">
                                                        <a class="square-white" href="{{ route('orders.show', $order) }}" title="{{ __('View order') }}">
                                                            <span><i class="fa-solid fa-eye"></i></span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">{{ __('No orders found') }}</td>
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
        (function() {
            const ordersStatusBaseUrl = @json(url('/admin/orders'));

            document.querySelectorAll('.order-status-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    const orderId = this.dataset.orderId;
                    const newStatus = this.value;
                    const oldStatus = this.dataset.currentStatus;
                    const selectEl = this;

                    Swal.fire({
                        title: @json(__('Change order status?')),
                        text: @json(__('This will set the order status to')) + ' "' + newStatus + '".',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: @json(__('Yes, update')),
                        cancelButtonText: @json(__('Cancel')),
                        customClass: {
                            popup: 'custom-popup',
                            confirmButton: 'custom-confirm-btn',
                            cancelButton: 'custom-cancel-btn',
                        },
                    }).then(function(result) {
                        if (!result.isConfirmed) {
                            selectEl.value = oldStatus;
                            return;
                        }

                        selectEl.disabled = true;

                        fetch(ordersStatusBaseUrl + '/' + orderId + '/status', {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                },
                                body: JSON.stringify({
                                    order_status: newStatus
                                }),
                            })
                            .then(async function(res) {
                                const data = await res.json().catch(function() {
                                    return {};
                                });
                                if (!res.ok) {
                                    const msg = data.message ||
                                        (data.errors && data.errors.order_status && data.errors.order_status[0]) ||
                                        @json(__('Something went wrong.'));
                                    throw new Error(msg);
                                }
                                return data;
                            })
                            .then(function(data) {
                                if (newStatus === 'completed') {
                                    const cell = selectEl.closest('td');
                                    if (cell) {
                                        cell.innerHTML = '<span class="badge badge-light-success">Completed</span>';
                                    }
                                } else {
                                    selectEl.dataset.currentStatus = newStatus;
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: @json(__('Updated')),
                                    text: data.message || @json(__('Order status updated successfully.')),
                                    timer: 1600,
                                    showConfirmButton: false,
                                });
                            })
                            .catch(function(err) {
                                selectEl.value = oldStatus;
                                Swal.fire({
                                    icon: 'error',
                                    title: @json(__('Error')),
                                    text: err.message || @json(__('Unable to update status.')),
                                });
                            })
                            .finally(function() {
                                selectEl.disabled = false;
                            });
                    });
                });
            });

            if ($.fn.DataTable && $('#orders-table tbody tr').length > 0 && $('#orders-table tbody tr td[colspan]').length === 0) {
                $('#orders-table').DataTable({
                    order: [[4, 'desc']],
                    columnDefs: [{
                        orderable: false,
                        targets: [3, 5],
                    }],
                });
            }
        })();
    </script>
@endpush
