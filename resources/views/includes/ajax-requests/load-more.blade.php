<script>
    $(document).ready(function() {

        $(document).on('click', '#load-more', function() {

            const button = $(this);
            const page = button.data('page');
            const url = button.data('url');

            if (!url) {
                console.error('Load more URL missing');
                return;
            }

            button.prop('disabled', true);

            $.ajax({
                url: `${url}?page=${page}`,
                type: 'GET',
                beforeSend: function() {
                    button.html('<div class="loader"></div>');
                },
                success: function(response) {

                    if (response.services && response.services.trim().length > 0) {

                        $('#services-wrapper').append(response.services);
                        button.data('page', response.next_page);

                        if (response.next_page > response.last_page) {
                            button.hide();
                        } else {
                            button.prop('disabled', false).text('Load More');
                        }

                    } else {
                        button.hide();
                    }
                },
                error: function() {
                    console.error('Failed to load more data');
                    button.prop('disabled', false).text('Load More');
                }
            });
        });

    });
</script>
