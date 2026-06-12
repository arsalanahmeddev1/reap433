@extends('layouts.admin.master')
@section('title', __('Create post'))
@section('content')
    <div class="container-fluid">
        <div class="edit-profile">
            <form class="card ajax-form" id="createBlogForm" action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <h5 class="mb-0">{{ __('New blog post') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row custom-input">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                            <select name="blog_category_id" class="form-select @error('blog_category_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('blog_category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('blog_category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Published') }}</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" @checked(old('is_published')) />
                                <label class="form-check-label" for="is_published">{{ __('Publish immediately') }}</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required maxlength="255" />
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Slug') }}</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" maxlength="255" placeholder="{{ __('Leave empty to auto-generate from title') }}" />
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Publish date') }}</label>
                            <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at') }}" />
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Body') }} <span class="text-danger">*</span></label>
                            <div id="blog_body_editor" class="blog-quill-wrap @error('body') is-invalid @enderror"></div>
                            <textarea id="blog_body" name="body" class="d-none @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Featured image') }}</label>
                            <input type="file" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif" />
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('blogs.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                    <button class="btn btn-primary" type="submit">{{ __('Save post') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('screens.admin.blogs.partials.blog-quill-scripts')

@push('scripts')
    <script>
        (function() {
            ajaxCreate("{{ route('blogs.index') }}");
        })();
    </script>
@endpush
