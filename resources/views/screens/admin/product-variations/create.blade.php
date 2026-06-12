@section('title', 'Create Variation')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid">
    <div class="edit-profile">
        <form class="card ajax-form" id="createVariationForm" action="{{ route('product-variations.store') }}" method="POST">
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
                            <label class="form-label" for="variation_name">Name <span class="text-danger">*</span></label>
                            <input class="form-control" id="variation_name" type="text" placeholder="Enter variation name"
                                name="name" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    ajaxCreate("{{ route('product-variations.index') }}");
</script>
@endpush
@endsection
