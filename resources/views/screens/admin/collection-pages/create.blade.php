@extends('layouts.admin.master')
@section('title', __('Create Collection Page'))
@section('content')
    <div class="container-fluid">
        <div class="edit-profile">
            <form class="card ajax-form" id="createCollectionPageForm" action="{{ route('collection-pages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <h5 class="mb-0">{{ __('New collection page') }}</h5>
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
                        @include('screens.admin.collection-pages.partials.categories-field', [
                            'categories' => $categories,
                            'selectedCategoryIds' => old('categories', []),
                        ])
                        @include('screens.admin.collection-pages.partials.uncategorized-products-field', [
                            'uncategorizedProducts' => $uncategorizedProducts,
                            'selectedUncategorizedProductIds' => old('uncategorized_products', []),
                        ])
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Slug') }}</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" maxlength="255" placeholder="{{ __('Leave blank to auto-generate from title') }}" />
                            <small class="text-muted">{{ __('URL slug, e.g. multi-category. Leave blank to generate from title.') }}</small>
                            @error('slug')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Image') }}</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" />
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <div id="collection_description_editor" class="collection-quill-wrap @error('description') is-invalid @enderror"></div>
                            <textarea id="collection_description" name="description" class="d-none @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @include('screens.admin.collection-pages.partials.faqs-field', [
                            'faqItems' => old('faqs', [['question' => '', 'answer' => '']]),
                        ])
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
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('collection-pages.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('screens.admin.collection-pages.partials.description-quill-scripts')
@include('screens.admin.collection-pages.partials.categories-faqs-scripts')

@push('scripts')
    <script>
        (function() {
            ajaxCreate("{{ route('collection-pages.index') }}");
        })();
    </script>
@endpush
