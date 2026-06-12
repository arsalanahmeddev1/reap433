@section('title', 'Create Product')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid">
    <div class="edit-profile">
        <form class="card ajax-form" id="createProductForm" action="{{ route('products.store') }}" method="POST"
            enctype="multipart/form-data">
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
                    @include('screens.admin.products.partials.core-fields', [
                        'product' => null,
                        'categories' => $categories,
                        'productTypes' => $productTypes,
                        'readonly' => false,
                        'lockProductType' => false,
                        'wooInitialBlocks' => $wooInitialBlocks,
                    ])
                    @include('screens.admin.products.partials.gallery', ['readonly' => false])
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('products.index') }}" class="btn btn-light me-2">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    Create product
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/admin-product-woo-attributes.js') }}"></script>
<script src="{{ asset('assets/js/admin-product-color-gallery.js') }}"></script>
<script>
    (function() {
        function toggleSimpleVariable() {
            const slug = $('#product_type_id').find(':selected').data('slug');
            const $simple = $('.js-simple-only');
            const $variable = $('.js-variable-only');

            if (slug === 'variable') {
                $simple.hide().find('#price').prop('disabled', true).removeAttr('required');
                $variable.show().find('select, input, textarea, button').prop('disabled', false);
                $('#price').val('0');
            } else {
                $simple.show().find('#price').prop('disabled', false).attr('required', 'required');
                $variable.hide().find('select, input, textarea, button').prop('disabled', true);
            }
        }

        function initColorGalleriesIfVariable() {
            const slug = $('#product_type_id').find(':selected').data('slug');
            const cf = document.getElementById('createProductForm');
            if (slug === 'variable' && cf && typeof window.initWooColorGalleryBlocksForForm === 'function') {
                window.initWooColorGalleryBlocksForForm(cf);
            }
        }

        $('#product_type_id').on('change', function () {
            toggleSimpleVariable();
            initColorGalleriesIfVariable();
        });
        toggleSimpleVariable();
        initColorGalleriesIfVariable();

        ajaxCreate("{{ route('products.index') }}");
    })();

    Dropzone.autoDiscover = false;

    $(document).ready(function() {
        window.myDropzone = new Dropzone("#gallery_images", {
            url: "javascript:void(0)",
            autoProcessQueue: false,
            maxFiles: null,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            clickable: true,
            paramName: 'images[]',

            init: function() {
                const dz = this;

                dz.on("addedfile", function(file) {
                    const input = document.getElementById("galleryInput");
                    if (!input) {
                        return;
                    }
                    const dt = new DataTransfer();
                    Array.from(input.files).forEach(function(f) {
                        dt.items.add(f);
                    });
                    dt.items.add(file);
                    input.files = dt.files;
                });

                dz.on("removedfile", function(file) {
                    const input = document.getElementById("galleryInput");
                    if (!input) {
                        return;
                    }
                    const dt = new DataTransfer();
                    Array.from(input.files).forEach(function(f) {
                        if (f.name !== file.name || f.size !== file.size) {
                            dt.items.add(f);
                        }
                    });
                    input.files = dt.files;
                });
            }
        });
    });
</script>
@endpush
@endsection
