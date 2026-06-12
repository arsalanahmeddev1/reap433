$(document).on('change', '.toggle-status', function () {

let checkbox = $(this);
let id = checkbox.data('id');
let url = checkbox.data('url');
let willActivate = checkbox.is(':checked');

let titleOn = checkbox.data('title-on') || 'Activate?';
let titleOff = checkbox.data('title-off') || 'Deactivate?';

Swal.fire({
    title: willActivate ? titleOn : titleOff,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes'
}).then(function(result) {

    if (!result.isConfirmed) {
        checkbox.prop('checked', !willActivate);
        return;
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        },
        error: function(xhr) {
            checkbox.prop('checked', !willActivate);

            Swal.fire({
                icon: 'error',
                title: xhr.responseJSON?.message || 'Something went wrong'
            });
        }
    });

});
});