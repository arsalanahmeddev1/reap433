@section('title', 'All Variations')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid user-list-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-no-border text-end">
                    <div class="card-header-right-icon">
                        <a class="btn btn-primary f-w-500" href="{{ route('product-variations.create') }}"><i
                                class="fa-solid fa-plus pe-2"></i>Create variation</a>
                    </div>
                </div>
                <div class="card-body pt-0 px-0">
                    <div class="list-product user-list-table">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table" id="variations-table">
                                <thead>
                                    <tr>
                                        <th><span class="c-o-light f-w-600">Name</span></th>
                                        <th><span class="c-o-light f-w-600">Created</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($variations as $v)
                                    <tr class="inbox-data">
                                        <td>{{ $v->name }}</td>
                                        <td>{{ $v->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <h3 class="pt-5">No variations found</h3>
                                            <p class="text-muted pb-4">Create variation definitions first, then use them on variable products.</p>
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
@if ($variations->isNotEmpty())
<script>
    $('#variations-table').DataTable({
        order: [[0, 'asc']]
    });
</script>
@endif
@endpush
@endsection
