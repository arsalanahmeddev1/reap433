@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet" />
    <style>
        .note-editor.note-frame {
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 0.25rem;
            background: #fff;
        }
        .note-editor.note-frame .note-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.12);
        }
        .note-editor.note-frame .note-editing-area .note-editable {
            min-height: 280px;
            background: #fff;
            color: #1a1a1a;
        }
        .note-editor.note-frame.is-invalid,
        .is-invalid + .note-editor.note-frame,
        textarea.is-invalid + .note-editor.note-frame {
            border-color: var(--bs-danger, #dc3545);
        }
        .note-editor.note-frame .note-editable img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script>
        (function($) {
            var uploadUrl = @json(route('blogs.editor-image'));
            var csrf = $('meta[name="csrf-token"]').attr('content') || '';
            var msgUploadFailed = @json(__('Image upload failed.'));
            var msgUploading = @json(__('Uploading image…'));

            function showError(msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: String(msg) });
                } else {
                    alert(msg);
                }
            }

            function syncBody() {
                var $ta = $('#blog_body');
                if ($ta.length && $ta.next('.note-editor').length) {
                    $ta.val($ta.summernote('code'));
                }
            }

            $(function() {
                var $ta = $('#blog_body');
                if (!$ta.length || typeof $.fn.summernote !== 'function') {
                    return;
                }

                $ta.summernote({
                    height: 300,
                    placeholder: @json(__('Write here…')),
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            if (!files || !files.length) {
                                return;
                            }

                            var file = files[0];
                            var formData = new FormData();
                            formData.append('image', file);
                            if (csrf) {
                                formData.append('_token', csrf);
                            }

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: msgUploading,
                                    allowOutsideClick: false,
                                    didOpen: function() {
                                        Swal.showLoading();
                                    },
                                });
                            }

                            $.ajax({
                                url: uploadUrl,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    Accept: 'application/json',
                                },
                                success: function(res) {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.close();
                                    }
                                    if (res && res.url) {
                                        $ta.summernote('insertImage', res.url);
                                        syncBody();
                                        return;
                                    }
                                    showError(msgUploadFailed);
                                },
                                error: function() {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.close();
                                    }
                                    showError(msgUploadFailed);
                                },
                            });
                        },
                        onChange: function() {
                            syncBody();
                        },
                        onBlur: function() {
                            syncBody();
                        },
                    },
                });

                $(document).on('submit', '#createBlogForm, #editBlogForm', function() {
                    syncBody();
                });
            });
        })(jQuery);
    </script>
@endpush
