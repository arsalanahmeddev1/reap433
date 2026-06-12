function ajaxUpdate(formSelector, successRedirect = null) {
    $(document).on('submit', formSelector, function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); // Spoof PUT method for Laravel

        const hasDropzone =
            typeof window.Dropzone !== 'undefined' && Dropzone.instances && Dropzone.instances.length > 0;
        const galleryInput = form.find('#galleryInput')[0];
        const usesGalleryInput = !!galleryInput;

        if (hasDropzone && !usesGalleryInput) {
            Dropzone.instances.forEach(function (dz) {
                dz.getQueuedFiles().forEach(function (file) {
                    formData.append(dz.options.paramName || 'file', file);
                });
            });
        }

        const submitBtn = form.find('button[type="submit"]');
        const btnLabel = submitBtn.text();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'text',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            beforeSend: function () {
                submitBtn.prop('disabled', true).text('Updating...');
            },
            success: function (raw) {
                const parsed =
                    typeof parseJsonFromAjaxResponse === 'function'
                        ? parseJsonFromAjaxResponse(raw)
                        : null;

                if (parsed && parsed.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: parsed.message || 'Updated successfully!',
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    if (parsed.data && typeof window.updateCategoryRow === 'function') {
                        window.updateCategoryRow(parsed.data);
                    }

                    if (successRedirect) {
                        setTimeout(function () {
                            window.location.href = successRedirect;
                        }, 1600);
                    }
                    if (window.location.pathname === '/admin/products/categories') {
                        location.reload();
                    }
                    $('#crudModal').modal('hide');
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: parsed.message || 'Updated successfully!',
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    if (successRedirect) {
                        setTimeout(function () {
                            window.location.href = successRedirect;
                        }, 1600);
                    }
                    $('#crudModal').modal('hide');
                    return;
                }

                if (xhr.status === 422 && parsed) {
                    form.find('.invalid-feedback').remove();
                    form.find('.is-invalid').removeClass('is-invalid');

                    if (parsed.success === false && parsed.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: parsed.message,
                        });
                    }

                    const globalErrors = [];
                    if (parsed.errors) {
                        $.each(parsed.errors, function (key, messages) {
                            const input = form.find(`[name="${key}"]`);
                            if (input.length) {
                                input.addClass('is-invalid');
                                input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                            } else {
                                globalErrors.push(messages[0]);
                            }
                        });
                    }

                    if (globalErrors.length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: globalErrors.join('<br>'),
                        });
                    }
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: (parsed && parsed.message) || 'Something went wrong!',
                });
            },
            complete: function () {
                submitBtn.prop('disabled', false).text(btnLabel);
            },
        });
    });
}
