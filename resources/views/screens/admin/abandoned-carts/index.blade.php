@extends('layouts.admin.master')
@section('title', __('Abandoned Carts'))

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border">
                        <h5 class="mb-0 f-w-600">{{ __('Abandoned Carts') }}</h5>
                        <p class="mb-0 c-o-light mt-1">{{ __('Users who added items to their cart but have not checked out yet.') }}</p>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="abandoned-carts-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('User name') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('User email') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Total cart items') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Cart amount') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Last updated') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr class="product-removes inbox-data">
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->cart_items_count }}</td>
                                                <td>{{ $user->cart_currency }} {{ number_format((float) $user->cart_amount, 2) }}</td>
                                                <td>
                                                    {{ $user->cart_updated_at ? \Illuminate\Support\Carbon::parse($user->cart_updated_at)->format('d M Y, h:i A') : '—' }}
                                                </td>
                                                <td>
                                                    <a
                                                        class="square-white"
                                                        href="{{ route('abandoned-carts.show', $user) }}"
                                                        title="{{ __('View details') }}"
                                                    >
                                                        <span><i class="fa-solid fa-eye"></i></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <h3 class="pt-5">{{ __('No abandoned carts found') }}</h3>
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
@endsection

@push('scripts')
    <script>
        $(function() {
            if ($.fn.DataTable && $('#abandoned-carts-table tbody tr').length > 0 && $('#abandoned-carts-table tbody tr td[colspan]').length === 0) {
                $('#abandoned-carts-table').DataTable({
                    order: [[4, 'desc']],
                    columnDefs: [{ orderable: false, targets: 5 }]
                });
            }
        });
    </script>
@endpush
