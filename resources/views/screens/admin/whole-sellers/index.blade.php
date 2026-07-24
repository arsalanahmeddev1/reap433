@section('title', 'Whole Sellers')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid user-list-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-no-border">
                    <h5 class="mb-0">{{ __('Whole Sellers') }}</h5>
                </div>
                <div class="card-body pt-3 px-0">
                    @if (session('success'))
                        <div class="alert alert-success mx-3" role="status">{{ session('success') }}</div>
                    @endif
                    <div class="list-product user-list-table">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table" id="whole-sellers-table">
                                <thead>
                                    <tr>
                                        <th><span class="c-o-light f-w-600">{{ __('Name') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Email') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Business') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Creation Date') }}</span></th>
                                        <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($wholeSellers as $user)
                                    <tr class="product-removes inbox-data">
                                        <td>{{ $user->name }}</td>
                                        <td><p>{{ $user->email }}</p></td>
                                        <td>{{ $user->business_name ?: '—' }}</td>
                                        <td>
                                            @if ($user->isPendingApproval())
                                                <span class="badge badge-light-warning">{{ __('Pending approval') }}</span>
                                            @elseif ($user->isApproved())
                                                <span class="badge badge-light-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{ ucfirst((string) $user->approval_status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p>{{ $user->created_at->format('d M Y, H:i A') }}</p>
                                        </td>
                                        <td>
                                            <div class="common-align gap-2 justify-content-start">
                                                <a class="square-white" href="{{ route('whole-sellers.show', $user) }}" title="{{ __('View') }}">
                                                    <span><i class="fa-solid fa-eye"></i></span>
                                                </a>
                                                @if ($user->isPendingApproval())
                                                    <form action="{{ route('whole-sellers.approve', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="square-white" title="{{ __('Approve') }}">
                                                            <span><i class="fa-solid fa-check"></i></span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="users-empty-row">
                                        <td colspan="6" class="text-center">
                                            <h3 class="pt-5">{{ __('No whole sellers found') }}</h3>
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
        if ($.fn.DataTable && $('#whole-sellers-table tbody tr').length > 0 && $('#whole-sellers-table tbody tr td[colspan]').length === 0) {
            $('#whole-sellers-table').DataTable({
                order: [[4, 'desc']],
                columnDefs: [{
                    orderable: false,
                    targets: 5
                }]
            });
        }
    });
</script>
@endpush
@endsection
