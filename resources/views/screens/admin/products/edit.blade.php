@section('title', 'Edit Product')
@extends('layouts.admin.master')
@section('content')
<div class="container-fluid">
    <div class="edit-profile">
        <form class="card ajax-form" id="editProductForm" action="{{ route('products.update', $product) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                        'product' => $product,
                        'categories' => $categories,
                        'productTypes' => $productTypes,
                        'readonly' => false,
                        'lockProductType' => true,
                        'wooInitialPayload' => $wooInitialPayload,
                    ])
                    @include('screens.admin.products.partials.gallery', [
                        'readonly' => false,
                        'galleryImages' => $product->images
                            ->filter(fn ($img) => $img->product_attribute_item_id === null
                                && $img->product_variation_id === null
                                && trim((string) ($img->color_key ?? '')) === '')
                            ->values(),
                    ])
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('products.show', $product) }}" class="btn btn-light me-2">View</a>
                <a href="{{ route('products.index') }}" class="btn btn-light me-2">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
@php
    $galleryCount = $product->images
        ->filter(fn ($img) => $img->product_attribute_item_id === null
            && $img->product_variation_id === null
            && trim((string) ($img->color_key ?? '')) === '')
        ->count();
    $galleryDeleteBase = url('/admin/products/'.$product->slug.'/gallery-image');
@endphp
<script src="{{ asset('assets/js/admin-product-woo-attributes.js') }}"></script>
<script src="{{ asset('assets/js/admin-product-color-gallery.js') }}"></script>
<script>
    window.adminProductGalleryDeleteBase = @json($galleryDeleteBase);
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

        $('#product_type_id').on('change', toggleSimpleVariable);
        toggleSimpleVariable();

        ajaxCreate("{{ route('products.index') }}");
    })();

    window.existingGalleryCount = {{ (int) $galleryCount }};
    Dropzone.autoDiscover = false;
    const deleteGalleryUrlBase = @json($galleryDeleteBase);

    $(document).ready(function() {
        window.existingCount = window.existingGalleryCount || 0;

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

        var editForm = document.getElementById('editProductForm');
        if (editForm && typeof window.initWooColorGalleryBlocksForForm === 'function') {
            window.initWooColorGalleryBlocksForForm(editForm);
        }
    });

    $(document).on('click', '.delete-gallery-image', function() {
        const btn = $(this);
        const wrapper = btn.closest('.gallery-item');
        const imageId = wrapper.data('id');

        Swal.fire({
            title: "Remove image?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, remove"
        }).then(function(result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: deleteGalleryUrlBase + '/' + imageId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(res) {
                    if (res.success) {
                        wrapper.remove();
                        window.existingCount = Math.max(0, (window.existingCount || 0) - 1);
                        if (window.myDropzone) {
                            window.myDropzone.options.maxFiles = null;
                            window.myDropzone.enable();
                            $('#gallery_images').removeClass('disabled');
                        }
                        Swal.fire({
                            icon: "success",
                            title: res.message || 'Removed',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Could not remove image.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            });
        });
    });
</script>
@endpush
@endsection
