<script>
    $(document).ready(function() {
        $(document).on('submit', '#submit-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');
            let btnOriginalText = submitBtn.text();
            let formData = new FormData(form[0]);
           
            // $.LoadingOverlay("show");
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                beforeSend: function() {
                    submitBtn.prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    console.log(response);
                    submitBtn.prop('disabled', false).text(btnOriginalText);
                    // $.LoadingOverlay("hide");
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        form[0].reset();
                        if ($('#editProfileModal').length) {
                            $('#editProfileModal').modal('hide');
                        }
                        if (response.data && response.data.user) {
                            updateProfileUI(response.data.user);
                        }
                        setTimeout(function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: response.message,
                            timer: 2000
                        })
                    }
                },
                error: function(error) {
                    submitBtn.prop('disabled', false).text(btnOriginalText);
                    // $.LoadingOverlay("hide");

                    if (error.responseJSON && error.responseJSON.message) {
                        Swal.fire({
                            icon: "error",
                            title: error.responseJSON.message,
                            showConfirmButton: true
                        });
                        return;
                    }

                    let errors = error.responseJSON.errors;

                    if (errors) {
                        handleValidationErrors(errors);
                        return;
                    }
                    Swal.fire({
                        icon: "error",
                        title: "An error occurred. Please try again.",
                        showConfirmButton: true,
                        timer: 2000
                    });
                }

            })
        })

        $(document).on('input change keydown', 'input, select, textarea', function() {
            $(this).next('span.error-message').text('');
            $(this).removeClass('is-invalid');
        });


        // ✅ Dynamic Profile Update Function


        function handleValidationErrors(errors) {
            // pehle sab error messages aur red borders hata do
            $('.error-message').remove();
            $('.form-control, .form-select').removeClass('is-invalid');


            $.each(errors, function(key, messages) {
                // Laravel key ko [ ] notation me convert karna
                // "modules.0.module_id" => "modules[0][module_id]"
                let nameAttr = key.replace(/\.(\d+)/g, "[$1]").replace(/\.(\w+)/g, "[$1]");

                // Input/Select/Textarea dhoondo with that name
                let inputField = $(
                    `input[name="${nameAttr}"], select[name="${nameAttr}"], textarea[name="${nameAttr}"]`
                );
                inputField.addClass('is-invalid');
                if (inputField.length > 0) {
                    if (nameAttr === "rating") {
                        // sirf pehli rating div ke neeche error lagana hai
                        if ($(".rating-main-div .error-message").length === 0) {
                            $(".rating-main-div").after(
                                `<span class="error-message text-danger">${messages[0]}</span>`
                            );
                        }
                    } else {
                        // normal fields
                        let errorMessage = $(
                            `<span class="error-message text-danger">${messages[0]}</span>`);
                        inputField.last().after(errorMessage);
                    }
                } else {
                    console.log("No input found for", nameAttr);
                }
            });
        }
    })
</script>
