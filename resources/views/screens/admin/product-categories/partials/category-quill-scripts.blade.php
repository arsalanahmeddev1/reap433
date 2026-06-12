@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />
    <style>
        .category-quill-wrap .ql-toolbar {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-bottom: none;
            border-radius: 0.25rem 0.25rem 0 0;
            background: #1a1a1a;
        }
        .category-quill-wrap .ql-container {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0 0 0.25rem 0.25rem;
            font-size: 1rem;
            min-height: 220px;
            background: #0f0f0f;
            color: #f8f4ed;
        }
        .category-quill-wrap .ql-editor {
            min-height: 200px;
        }
        .category-quill-wrap .ql-editor.ql-blank::before {
            color: rgba(248, 244, 237, 0.45);
        }
        .category-quill-wrap .ql-editor img {
            max-width: 100%;
            height: auto;
        }
        .category-quill-wrap .ql-stroke {
            stroke: #f8f4ed;
        }
        .category-quill-wrap .ql-fill {
            fill: #f8f4ed;
        }
        .category-quill-wrap .ql-picker-label {
            color: #f8f4ed;
        }
        .category-quill-wrap.is-invalid .ql-toolbar,
        .category-quill-wrap.is-invalid .ql-container {
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
            var csrfEl = document.querySelector('meta[name="csrf-token"]');
            var csrf = csrfEl ? csrfEl.getAttribute('content') : '';
            var msgUploadFailed = @json(__('Image upload failed.'));
            var msgUploading = @json(__('Uploading image…'));
            var placeholder = @json(__('Write here… Use the image button or paste a screenshot.'));

            window.categoryQuillEditors = {
                create: null,
                edit: null,
            };

            function getEditorHtml(quill) {
                var html = quill.root.innerHTML;
                if (html === '<p><br></p>' || html === '<p></p>' || html.trim() === '') {
                    return '';
                }
                return html;
            }

            window.syncCategoryQuill = function(quill, textareaId) {
                var ta = document.getElementById(textareaId);
                if (ta && quill) {
                    ta.value = getEditorHtml(quill);
                }
            };

            window.setCategoryQuillContent = function(quill, html) {
                if (!quill) {
                    return;
                }
                if (html && html.trim() !== '') {
                    quill.clipboard.dangerouslyPasteHTML(html);
                } else {
                    quill.setText('');
                }
            };

            function showError(msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: String(msg) });
                } else {
                    alert(msg);
                }
            }

            function uploadAndInsertImage(quill, textareaId, file) {
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
                            return { ok: r.ok, data: data };
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
                        syncCategoryQuill(quill, textareaId);
                    })
                    .catch(function() {
                        if (loading && typeof Swal !== 'undefined') {
                            Swal.close();
                        }
                        showError(msgUploadFailed);
                    });
            }

            function bindQuillImageHandlers(quill, textareaId) {
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
                                    uploadAndInsertImage(quill, textareaId, f);
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
                            uploadAndInsertImage(quill, textareaId, files[0]);
                        }
                    },
                    true
                );
            }

            function createCategoryQuill(hostSelector, textareaId) {
                var host = document.querySelector(hostSelector);
                var ta = document.getElementById(textareaId);
                if (!host || !ta || typeof Quill === 'undefined') {
                    return null;
                }

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
                                var quill = this.quill;
                                var input = document.createElement('input');
                                input.type = 'file';
                                input.accept = 'image/*';
                                input.setAttribute('aria-hidden', 'true');
                                input.style.position = 'fixed';
                                input.style.left = '-9999px';
                                document.body.appendChild(input);
                                input.addEventListener('change', function() {
                                    if (input.files && input.files[0]) {
                                        uploadAndInsertImage(quill, textareaId, input.files[0]);
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

                var quill = new Quill(hostSelector, {
                    theme: 'snow',
                    modules: quillModules,
                    placeholder: placeholder,
                });

                if (ta.value) {
                    quill.clipboard.dangerouslyPasteHTML(ta.value);
                }
                syncCategoryQuill(quill, textareaId);

                quill.on('text-change', function() {
                    syncCategoryQuill(quill, textareaId);
                });

                bindQuillImageHandlers(quill, textareaId);

                return quill;
            }

            document.addEventListener('DOMContentLoaded', function() {
                window.categoryQuillEditors.create = createCategoryQuill(
                    '#category_create_description_editor',
                    'category-create-description'
                );
                window.categoryQuillEditors.edit = createCategoryQuill(
                    '#category_edit_description_editor',
                    'category-description'
                );

                document.addEventListener(
                    'submit',
                    function(e) {
                        var t = e.target;
                        if (!t) {
                            return;
                        }
                        if (t.id === 'category-create-form' && window.categoryQuillEditors.create) {
                            syncCategoryQuill(window.categoryQuillEditors.create, 'category-create-description');
                        }
                        if (t.id === 'category-edit-form' && window.categoryQuillEditors.edit) {
                            syncCategoryQuill(window.categoryQuillEditors.edit, 'category-description');
                        }
                    },
                    true
                );
            });
        })();
    </script>
@endpush
