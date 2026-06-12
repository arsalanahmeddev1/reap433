@section('title', 'All Users')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid user-list-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-3 px-0">
                    <div class="list-product user-list-table">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table" id="users-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <span class="c-o-light f-w-600">Name</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Email</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Creation Date</span>
                                        </th>
                                        <th>
                                            <span class="c-o-light f-w-600">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr class="product-removes inbox-data">
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <p>{{ $user->email }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $user->created_at->format('d M Y, H:i A') }}</p>
                                        </td>
                                        <td>
                                            <div class="common-align gap-2 justify-content-start">
                                                <a class="square-white" href="{{ route('users.show', $user) }}" title="{{ __('View') }}">
                                                    <span><i class="fa-solid fa-eye"></i></span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="users-empty-row">
                                        <td colspan="4" class="text-center">
                                            <h3 class="pt-5">No users found</h3>
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
@push('scripts')
<script>
    $(function() {
        if ($.fn.DataTable && $('#users-table tbody tr').length > 0 && $('#users-table tbody tr td[colspan]').length === 0) {
            $('#users-table').DataTable({
                order: [[2, 'desc']],
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }]
            });
        }
    });
</script>
@endpush
@endsection
