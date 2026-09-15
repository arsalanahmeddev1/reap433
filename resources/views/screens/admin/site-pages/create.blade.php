@extends('layouts.admin.master')
@section('title', __('Create Site Page'))
@section('content')
    <div class="container-fluid">
        <div class="edit-profile">
            <form class="card ajax-form" id="createSitePageForm" action="{{ route('site-pages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <h5 class="mb-0">{{ __('New site page') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row custom-input">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required maxlength="255" />
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" @selected(old('status', 'active') === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>{{ __('Inactive') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Image') }}</label>
                            <input type="file" name="image_url" class="form-control @error('image_url') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif" />
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('SEO Title') }}</label>
                            <input type="text" name="seo_title" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title') }}" maxlength="255" />
                            @error('seo_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('SEO Description') }}</label>
                            <textarea name="seo_description" rows="3" class="form-control @error('seo_description') is-invalid @enderror">{{ old('seo_description') }}</textarea>
                            @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea id="blog_body" name="description" class="form-control @error('description') is-invalid @enderror" rows="10">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('site-pages.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('screens.admin.blogs.partials.blog-editor-scripts')

@push('scripts')
    <script>
        (function() {
            ajaxCreate("{{ route('site-pages.index') }}");
        })();
    </script>
@endpush
