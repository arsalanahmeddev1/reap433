<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="row mb-3">
                {{--<div class="col-12">
                    @if (auth()->check() && auth()->user()->hasRole(config('roles.employee')) && ! request()->routeIs('employees.edit'))
                        @php
                            $profileEmployee = \App\Models\Employee::query()->where('user_id', auth()->id())->first();
                        @endphp
                        @if ($profileEmployee)
                            <div class="alert alert-warning alert-dismissible fade show d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 border"
                                role="alert">
                                <span class="mb-0 text-body"><i class="fa-solid fa-user-pen me-2"></i>Complete your employee profile.</span>
                                <a href="{{ route('employees.edit', $profileEmployee) }}" class="btn btn-sm btn-primary flex-shrink-0">Complete profile</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    @endif
                </div>--}}
                {{-- <div class="col-12">
                    @if (auth()->user()->hasRole(config('roles.league_contractor')))
                        @if (!$company || !$company->is_profile_completed)
                            <div class="alert alert-warning custom-alert alert-dismissible fade show d-flex align-items-center justify-content-between"
                                role="alert" style="background-color: #1d1d1d; border-color: #ffc107; color: #fff;">
                                <div class="d-flex align-items-center"> <i class="fa-solid fa-exclamation-triangle me-2"
                                        style="color: #ffc107;"></i> <span>Please complete your profile To Activate
                                        Services and add Products</span> </div> <a
                                    href="{{ route('company-profile.index') }}" class="btn btn-warning btn-sm"
                                    style="min-width: 140px;">Complete Profile</a>
                            </div>
                        @elseif($company->is_profile_completed && !$company->is_profile_approved)
                            <div class="alert alert-info" style="background:#1d1d1d; border-color:#0dcaf0; color:#fff;">
                                <i class="fa-solid fa-hourglass-half"></i> Your profile has been submitted for approval.
                                Once approved, you can add Services.
                            </div>
                        @elseif(!$company->is_active)
                            <div class="alert alert-danger"
                                style="background:#1d1d1d; border-color:#0dcaf0; color:#fff;"> <i
                                    class="fa-solid fa-hourglass-half"></i> Your profile Deactivated By Admin. Please
                                contact support to reactivate your profile. </div>
                        @endif
                    @endif
                </div> --}}
            </div>
            <div class="col-sm-6">
                <h3>@yield('title', 'Dashboard')</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="#"> <svg class="stroke-icon">
                                <use href="{{ asset('/assets/libs/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a> </li> {{-- @foreach ($breadcrumbs as $crumb) @if ($loop->last) <li class="breadcrumb-item active">{{ $crumb['page_title'] }}</li> @else <li class="breadcrumb-item"> <a href="{{ $crumb['url'] }}">{{ $crumb['page_title'] }}</a> </li> @endif @endforeach --}}
                </ol>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- <script>
    @if ($approvalNotification)
        Swal.fire({
        icon: 'success',
        title: 'Profile Approved',
        text: '{{ $approvalNotification->data['message'] }}',
        confirmButtonText: 'Ok'
        }).then(() => {
        // mark notification as read
        fetch("{{ route('notifications.read', $approvalNotification->id) }}", {
        method: 'POST',
        headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
        });
        });
    @endif
</script> --}}
@endpush
