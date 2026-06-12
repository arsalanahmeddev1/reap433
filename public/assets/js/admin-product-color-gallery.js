/**
 * Per color-group Dropzone + hidden multi file input for attr_blocks[bi][color_gallery][].
 * Depends on Dropzone (same as main product gallery). Does not destroy existing Dropzones on reindex.
 */
(function () {
    'use strict';

    function wireFileInputFromDropzone(dz, fileInput) {
        dz.on('addedfile', function (file) {
            if (!fileInput) {
                return;
            }
            var dt = new DataTransfer();
            Array.from(fileInput.files || []).forEach(function (f) {
                dt.items.add(f);
            });
            dt.items.add(file);
            fileInput.files = dt.files;
        });

        dz.on('removedfile', function (file) {
            if (!fileInput) {
                return;
            }
            var dt = new DataTransfer();
            Array.from(fileInput.files || []).forEach(function (f) {
                if (f.name !== file.name || f.size !== file.size) {
                    dt.items.add(f);
                }
            });
            fileInput.files = dt.files;
        });
    }

    function initBlock(block, bi) {
        var dzEl = block.querySelector('.js-color-gallery-dz');
        var fileInput = block.querySelector('.js-color-gallery-input');
        if (!dzEl || !fileInput) {
            return;
        }

        fileInput.setAttribute('name', 'attr_blocks[' + bi + '][color_gallery][]');
        fileInput.classList.add('d-none');

        block.querySelectorAll('.js-color-gallery-keep').forEach(function (inp) {
            inp.setAttribute('name', 'attr_blocks[' + bi + '][color_gallery_keep][]');
        });

        if (dzEl.dropzone) {
            return;
        }

        if (!dzEl.id) {
            dzEl.id = 'woo-color-gallery-dz-' + bi + '-' + Math.random().toString(36).slice(2, 9);
        }

        if (typeof Dropzone === 'undefined') {
            return;
        }

        var dz = new Dropzone(dzEl, {
            url: 'javascript:void(0)',
            autoProcessQueue: false,
            maxFiles: null,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            clickable: true,
            dictDefaultMessage: 'Drop color group images here (optional)',
        });

        wireFileInputFromDropzone(dz, fileInput);
    }

    function initAllInForm(form) {
        if (!form) {
            return;
        }
        var container = form.querySelector('#attr-blocks-container');
        if (!container) {
            return;
        }
        var blocks = container.querySelectorAll('.js-woo-attr-block');
        blocks.forEach(function (block, bi) {
            initBlock(block, bi);
        });
    }

    window.initWooColorGalleryBlocksForForm = initAllInForm;

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-color-gallery-delete-existing');
        if (!btn) {
            return;
        }
        var wrap = btn.closest('.js-color-gallery-existing');
        if (!wrap) {
            return;
        }
        var imageId = wrap.getAttribute('data-image-id');
        if (!imageId || !window.adminProductGalleryDeleteBase) {
            wrap.remove();
            return;
        }
        var base = String(window.adminProductGalleryDeleteBase).replace(/\/$/, '');
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            return;
        }
        Swal.fire({
            title: 'Remove image?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove',
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }
            $.ajax({
                url: base + '/' + imageId,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                success: function (res) {
                    if (res.success) {
                        wrap.remove();
                        Swal.fire({ icon: 'success', title: res.message || 'Removed', timer: 1200, showConfirmButton: false });
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Could not remove image.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
            });
        });
    });
})();
