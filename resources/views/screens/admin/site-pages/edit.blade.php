@extends('layouts.admin.master')
@section('title', __('Edit Site Page'))
@section('content')
    <div class="container-fluid">
        <div class="edit-profile">
            <form class="card ajax-form" id="editSitePageForm" action="{{ route('site-pages.update', $page) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Edit site page') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row custom-input">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required maxlength="255" />
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" @selected(old('status', $page->status) === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected(old('status', $page->status) === 'inactive')>{{ __('Inactive') }}</option>
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
                        @if ($page->imageUrl())
                            <div class="col-12 mb-3">
                                <p class="form-label mb-1">{{ __('Current image') }}</p>
                                <img src="{{ $page->imageUrl() }}" alt="" class="img-thumbnail" style="max-height: 160px" />
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1" />
                                    <label class="form-check-label" for="remove_image">{{ __('Remove current image') }}</label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('SEO Title') }}</label>
                            <input type="text" name="seo_title" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title', $page->seo_title) }}" maxlength="255" />
                            @error('seo_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('SEO Description') }}</label>
                            <textarea name="seo_description" rows="3" class="form-control @error('seo_description') is-invalid @enderror">{{ old('seo_description', $page->seo_description) }}</textarea>
                            @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea id="blog_body" name="description" class="form-control @error('description') is-invalid @enderror" rows="10">{{ old('description', $page->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('site-pages.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                    <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
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
