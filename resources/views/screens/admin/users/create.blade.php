@section('title', 'Create User')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid">
    <div class="edit-profile">
        <form class="card ajax-form" id="createUserForm" action={{ route('users.store') }} method="POST">
            @csrf
            <div class="card-header">
                <div class="card-options">
                    <a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i
                            class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#"
                        data-bs-toggle="card-remove"><i class="fe fe-x"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="row custom-input">
                    <div class="col-sm-6 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                            <input class="form-control" id="name"
                                type="text" placeholder="Enter Name" name="name" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="email">Email Address <span
                                    class="text-danger">*</span></label><input class="form-control" id="email"
                                type="email" placeholder="Enter Email Address" name="email" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="password">Password <span
                                    class="text-danger">*</span></label><input class="form-control" id="password"
                                type="password" placeholder="Enter Password" name="password" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="confirm_password">Confirm Password</label><input
                                class="form-control" id="confirm_password" type="password"
                                placeholder="Enter Confirm Password" name="password_confirmation" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary" type="submit">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    ajaxCreate("{{ route('users.index') }}");
</script>
@endpush
@endsection