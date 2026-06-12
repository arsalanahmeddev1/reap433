function ajaxDelete(buttonSelector, itemSelector, dataTableId = null, rowRemovalTableSelector = null) {
    $(document).on('click', buttonSelector, function(e) {
        e.preventDefault();

        let form = $(this).closest('form');
        let formData = form.serialize();
        const $row = form.closest(itemSelector);

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                popup: 'custom-popup',
                confirmButton: 'custom-confirm-btn',
                cancelButton: 'custom-cancel-btn'
            },
            
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'DELETE',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Accept: 'application/json',
                    },
                    success: function(response) {
                        if (rowRemovalTableSelector && $.fn.DataTable.isDataTable($(rowRemovalTableSelector))) {
                            $(rowRemovalTableSelector).DataTable().row($row).remove().draw(false);
                        } else {
                            $row.remove();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Record deleted successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        if (dataTableId && typeof $(dataTableId).DataTable === 'function') {
                            $(dataTableId).DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Unable to delete this record.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg,
                        });
                    }
                });
            }
        });
    });
}
