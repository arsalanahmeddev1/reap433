@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />
    <style>
        .collection-quill-wrap .ql-toolbar {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-bottom: none;
            border-radius: 0.25rem 0.25rem 0 0;
            background: #1a1a1a;
        }
        .collection-quill-wrap .ql-container {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0 0 0.25rem 0.25rem;
            font-size: 1rem;
            min-height: 300px;
            background: #0f0f0f;
            color: #f8f4ed;
            position: relative;
        }
        .collection-quill-wrap .ql-editor {
            min-height: 280px;
            color: #f8f4ed;
        }
        .collection-quill-wrap .ql-editor.ql-blank::before {
            color: rgba(248, 244, 237, 0.45);
            font-style: normal;
        }
        .collection-quill-wrap .ql-editor img {
            max-width: 100%;
            height: auto;
        }
        .collection-quill-wrap .ql-stroke {
            stroke: #f8f4ed;
        }
        .collection-quill-wrap .ql-fill {
            fill: #f8f4ed;
        }
        .collection-quill-wrap .ql-picker {
            color: #f8f4ed;
        }
        .collection-quill-wrap .ql-picker-label {
            color: #f8f4ed;
        }
        .collection-quill-wrap .ql-picker-options {
            background: #1a1a1a;
            border-color: rgba(255, 255, 255, 0.12);
            color: #f8f4ed;
        }
        .collection-quill-wrap .ql-picker.ql-expanded .ql-picker-label,
        .collection-quill-wrap .ql-picker.ql-expanded .ql-picker-options {
            border-color: rgba(201, 162, 39, 0.45);
        }
        .collection-quill-wrap .ql-snow.ql-toolbar button:hover .ql-stroke,
        .collection-quill-wrap .ql-snow .ql-toolbar button:hover .ql-stroke,
        .collection-quill-wrap .ql-snow.ql-toolbar button.ql-active .ql-stroke,
        .collection-quill-wrap .ql-snow .ql-toolbar button.ql-active .ql-stroke {
            stroke: #c9a227;
        }
        .collection-quill-wrap .ql-snow.ql-toolbar button:hover .ql-fill,
        .collection-quill-wrap .ql-snow .ql-toolbar button:hover .ql-fill,
        .collection-quill-wrap .ql-snow.ql-toolbar button.ql-active .ql-fill,
        .collection-quill-wrap .ql-snow .ql-toolbar button.ql-active .ql-fill {
            fill: #c9a227;
        }
        .collection-quill-wrap.is-invalid .ql-toolbar,
        .collection-quill-wrap.is-invalid .ql-container {
            border-color: var(--bs-danger, #dc3545);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
    <script>
        (function() {
            var uploadUrl = @json(route('blogs.editor-image'));
            var csrf = document.querySelector('meta[name="csrf-token"]');
            csrf = csrf ? csrf.getAttribute('content') : '';
            var msgUploadFailed = @json(__('Image upload failed.'));
            var msgUploading = @json(__('Uploading image…'));

            function getBodyHtml(quill) {
                var html = quill.root.innerHTML;
                if (html === '<p><br></p>' || html === '<p></p>' || html.trim() === '') {
                    return '';
                }
                return html;
            }

            function syncTextarea(quill) {
                var ta = document.getElementById('collection_description');
                if (ta) {
                    ta.value = getBodyHtml(quill);
                }
            }

            function showError(msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: String(msg) });
                } else {
                    alert(msg);
                }
            }

            function uploadAndInsertImage(quill, file) {
                if (!file || !file.type || file.type.indexOf('image') !== 0) {
                    return;
                }
                var formData = new FormData();
                formData.append('image', file);
                if (csrf) {
                    formData.append('_token', csrf);
                }
                var loading = false;
                if (typeof Swal !== 'undefined') {
                    loading = true;
                    Swal.fire({
                        title: msgUploading,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: function() {
                            Swal.showLoading();
                        },
                    });
                }
                fetch(uploadUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                    },
                })
                    .then(function(r) {
                        return r.text().then(function(text) {
                            var data = {};
                            try {
                                data = text ? JSON.parse(text) : {};
                            } catch (e) {
                                data = {};
                            }
                            return { ok: r.ok, status: r.status, data: data };
                        });
                    })
                    .then(function(result) {
                        if (loading && typeof Swal !== 'undefined') {
                            Swal.close();
                        }
                        if (!result.ok) {
                            var msg =
                                (result.data && result.data.message) ||
                                (result.data &&
                                    result.data.errors &&
                                    result.data.errors.image &&
                                    result.data.errors.image[0]) ||
                                msgUploadFailed;
                            showError(msg);
                            return;
                        }
                        var url = result.data && result.data.url;
                        if (!url) {
                            showError(msgUploadFailed);
                            return;
                        }
                        var range = quill.getSelection(true);
                        var index = range && typeof range.index === 'number' ? range.index : 0;
                        quill.insertEmbed(index, 'image', url, 'user');
                        quill.setSelection(index + 1, 0);
                        syncTextarea(quill);
                    })
                    .catch(function() {
                        if (loading && typeof Swal !== 'undefined') {
                            Swal.close();
                        }
                        showError(msgUploadFailed);
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                var host = document.getElementById('collection_description_editor');
                var ta = document.getElementById('collection_description');
                if (!host || !ta || typeof Quill === 'undefined') {
                    return;
                }

                var quill;

                if (typeof ImageResize !== 'undefined') {
                    var IR = ImageResize.default || ImageResize;
                    Quill.register('modules/imageResize', IR);
                }

                var quillModules = {
                    toolbar: {
                        container: [
                            [{ header: [1, 2, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['blockquote', 'link', 'image'],
                            ['clean'],
                        ],
                        handlers: {
                            image: function() {
                                var input = document.createElement('input');
                                input.type = 'file';
                                input.accept = 'image/*';
                                input.setAttribute('aria-hidden', 'true');
                                input.style.position = 'fixed';
                                input.style.left = '-9999px';
                                document.body.appendChild(input);
                                input.addEventListener('change', function() {
                                    if (input.files && input.files[0]) {
                                        uploadAndInsertImage(quill, input.files[0]);
                                    }
                                    input.remove();
                                });
                                input.click();
                            },
                        },
                    },
                };

                if (typeof ImageResize !== 'undefined') {
                    quillModules.imageResize = {
                        modules: ['Resize', 'DisplaySize', 'Toolbar'],
                    };
                }

                quill = new Quill('#collection_description_editor', {
                    theme: 'snow',
                    modules: quillModules,
                    placeholder: @json(__('Write here… Use the image button or paste a screenshot.')),
                });

                if (ta.value) {
                    quill.clipboard.dangerouslyPasteHTML(ta.value);
                }
                syncTextarea(quill);

                quill.on('text-change', function() {
                    syncTextarea(quill);
                });

                quill.root.addEventListener(
                    'paste',
                    function(e) {
                        var items = e.clipboardData && e.clipboardData.items;
                        if (!items) {
                            return;
                        }
                        for (var i = 0; i < items.length; i++) {
                            if (items[i].type.indexOf('image') !== -1) {
                                e.preventDefault();
                                var f = items[i].getAsFile();
                                if (f) {
                                    uploadAndInsertImage(quill, f);
                                }
                                return;
                            }
                        }
                    },
                    true
                );

                quill.root.addEventListener(
                    'drop',
                    function(e) {
                        var files = e.dataTransfer && e.dataTransfer.files;
                        if (files && files.length && files[0].type.indexOf('image') === 0) {
                            e.preventDefault();
                            uploadAndInsertImage(quill, files[0]);
                        }
                    },
                    true
                );

                window.CollectionPageQuill = quill;

                document.addEventListener(
                    'submit',
                    function(e) {
                        var t = e.target;
                        if (t && (t.id === 'createCollectionPageForm' || t.id === 'editCollectionPageForm')) {
                            if (window.CollectionPageQuill) {
                                syncTextarea(window.CollectionPageQuill);
                            }
                        }
                    },
                    true
                );
            });
        })();
    </script>
@endpush
