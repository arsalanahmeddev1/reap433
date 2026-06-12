$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
});

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function laravelKeyToInputName(key) {
    const parts = String(key).split('.');

    if (parts.length === 1) {
        return parts[0];
    }

    let name = parts[0];
    for (let i = 1; i < parts.length; i++) {
        name += '[' + parts[i] + ']';
    }

    return name;
}

function findFieldForValidationKey(form, key) {
    const $form = $(form);
    const bracketName = laravelKeyToInputName(key);
    let input = $form.find('[name="' + bracketName.replace(/"/g, '\\"') + '"]');

    if (input.length) {
        return input.first();
    }

    const simpleArray = String(key).match(/^(.+)\.(\d+)$/);
    if (simpleArray) {
        input = $form.find('[name="' + simpleArray[1] + '[' + simpleArray[2] + ']"]');
        if (input.length) {
            return input.first();
        }
    }

    const rootKey = String(key).split('.')[0];
    const anchorSelectors = {
        attr_blocks: '#product-variations-section, #attr-blocks-container, .woo-attr-blocks',
        variation_rows: '#product-variations-section, #attr-blocks-container, .woo-attr-blocks',
    };

    if (anchorSelectors[rootKey]) {
        const anchor = $form.find(anchorSelectors[rootKey]).first();
        if (anchor.length) {
            return anchor;
        }
    }

    return $();
}

function showAjaxValidationErrors(form, response) {
    const $form = $(form);

    $form.find('.invalid-feedback, .ajax-field-error').remove();
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('#product-variations-section').removeClass('border border-danger rounded p-2');
    $form.find('.ajax-validation-summary').remove();

    const messages = [];
    let firstInvalid = null;

    if (response && response.errors) {
        $.each(response.errors, function (key, fieldMessages) {
            const message = fieldMessages[0];
            if (!message || messages.indexOf(message) !== -1) {
                return;
            }

            messages.push(message);

            const input = findFieldForValidationKey(form, key);
            if (!input.length) {
                return;
            }

            input.addClass('is-invalid');

            const feedback = $('<div class="invalid-feedback d-block ajax-field-error"></div>').text(message);

            if (input.is('#product-variations-section, #attr-blocks-container, .woo-attr-blocks')) {
                input.addClass('border border-danger rounded p-2');
                input.prepend(feedback);
            } else {
                input.after(feedback);
            }

            if (!firstInvalid) {
                firstInvalid = input[0];
            }
        });
    }

    if (messages.length) {
        const summary = $('<div class="alert alert-danger ajax-validation-summary mb-3" role="alert"></div>');
        summary.append('<strong>Please fix the following:</strong>');
        const list = $('<ul class="mb-0 ps-3 mt-2"></ul>');
        messages.forEach(function (message) {
            list.append($('<li></li>').text(message));
        });
        summary.append(list);

        const target = $form.find('.card-body').first();
        if (target.length) {
            target.prepend(summary);
        } else {
            $form.prepend(summary);
        }
    }

    if (firstInvalid && typeof firstInvalid.scrollIntoView === 'function') {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    const formId = $form.attr('id') || '';
    const validationTitle =
        formId === 'createProductForm'
            ? 'Could not create product'
            : formId === 'editProductForm'
              ? 'Could not save product'
              : 'Validation failed';

    Swal.fire({
        icon: 'error',
        title: validationTitle,
        text:
            messages.length > 0
                ? 'Please review the highlighted fields below.'
                : response && response.message
                  ? response.message
                  : 'Please check the form and try again.',
    });
}

function ajaxCreate(successRedirect = null) {
    $(document).on('submit', 'form.ajax-form', function (e) {
        e.preventDefault();

        const form = $(this);
        if (form.data('ajax-submit-locked')) {
            return false;
        }
        form.data('ajax-submit-locked', true);

        const submitBtn = form.find('button[type="submit"]');
        const btnOriginalText = submitBtn.length ? submitBtn.text() : 'Save';
        const formData = new FormData(this);

        const galleryInput = form.find('#galleryInput')[0];
        const usesGalleryInput = !!galleryInput;

        if (typeof Dropzone !== 'undefined' && Dropzone.instances && Dropzone.instances.length > 0 && !usesGalleryInput) {
            Dropzone.instances.forEach(function (dz) {
                dz.getQueuedFiles().forEach(function (file) {
                    const param = dz.options.paramName || 'file';
                    formData.append(param, file);
                });
            });
        }

        function handleSuccessResponse(response) {
            const methodOverride = form.find('input[name="_method"]').val();
            if (methodOverride !== 'PUT' && methodOverride !== 'PATCH') {
                form[0].reset();
            }

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message || 'Created successfully!',
                showConfirmButton: false,
                timer: 1500,
            });

            if (successRedirect) {
                setTimeout(function () {
                    window.location.href = successRedirect;
                }, 1600);
            } else if (response.redirect) {
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1600);
            } else if (typeof $('#dataTable').DataTable === 'function') {
                $('#dataTable').DataTable().ajax.reload(null, false);
            }
        }

        function handle422(response) {
            showAjaxValidationErrors(form[0], response);
        }

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'text',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            beforeSend: function () {
                submitBtn.prop('disabled', true).text('Saving...');
            },
            success: function (raw, _textStatus, xhr) {
                const parsed =
                    typeof parseJsonFromAjaxResponse === 'function'
                        ? parseJsonFromAjaxResponse(raw)
                        : null;

                if (parsed && parsed.success) {
                    if (typeof raw === 'string' && (raw.indexOf('<b>Warning</b>') !== -1 || raw.indexOf('Maximum number of allowable file uploads') !== -1)) {
                        console.warn(
                            'Response included PHP warnings before JSON. Increase max_file_uploads and post_max_size in php.ini (or public/.user.ini) so all images are saved.',
                        );
                    }
                    handleSuccessResponse(parsed);
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: (parsed && parsed.message) || 'Invalid response from server.',
                });
            },
            error: function (xhr) {
                const raw = xhr.responseText || '';
                const parsed =
                    xhr.responseJSON ||
                    (typeof parseJsonFromAjaxResponse === 'function' ? parseJsonFromAjaxResponse(raw) : null);

                if (parsed && parsed.success) {
                    if (raw.indexOf('<b>Warning</b>') !== -1 || raw.indexOf('Maximum number of allowable file uploads') !== -1) {
                        console.warn(
                            'Response included PHP warnings before JSON. Increase max_file_uploads and post_max_size in php.ini (or public/.user.ini) so all images are saved.',
                        );
                    }
                    handleSuccessResponse(parsed);
                    return;
                }

                if (xhr.status === 422 && parsed) {
                    handle422(parsed);
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: (parsed && parsed.message) || 'Something went wrong!',
                });
            },
            complete: function () {
                submitBtn.prop('disabled', false).text(btnOriginalText);
                form.data('ajax-submit-locked', false);
            },
        });
    });
}
