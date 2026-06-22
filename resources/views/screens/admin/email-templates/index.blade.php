@extends('layouts.admin.master')
@section('title', __('Email templates'))

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border">
                        <h5 class="mb-0 f-w-600">{{ __('Email templates') }}</h5>
                        <p class="mb-0 c-o-light">{{ __('Manage dynamic emails sent when orders are placed or statuses change.') }}</p>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="list-product user-list-table">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table" id="email-templates-table">
                                    <thead>
                                        <tr>
                                            <th><span class="c-o-light f-w-600">{{ __('Name') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Slug') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Subject') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Status') }}</span></th>
                                            <th><span class="c-o-light f-w-600">{{ __('Actions') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($templates as $template)
                                            <tr>
                                                <td>{{ $template->name }}</td>
                                                <td><code class="text-reset">{{ $template->slug }}</code></td>
                                                <td>{{ Str::limit($template->subject, 60) }}</td>
                                                <td>
                                                    <span class="badge {{ $template->is_active ? 'badge-light-success' : 'badge-light-secondary' }}">
                                                        {{ $template->is_active ? __('Active') : __('Inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a class="square-white" href="{{ route('email-templates.edit', $template) }}" title="{{ __('Edit') }}">
                                                        <span><i class="fa-solid fa-pen"></i></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">{{ __('No email templates found.') }}</td>
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
    $(function () {
        if ($.fn.DataTable && $('#email-templates-table tbody tr').length > 0 && $('#email-templates-table tbody tr td[colspan]').length === 0) {
            $('#email-templates-table').DataTable({
                order: [[0, 'asc']],
                columnDefs: [{ orderable: false, targets: [4] }],
            });
        }
    });
</script>
@endpush
