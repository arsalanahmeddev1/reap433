@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/select2.css') }}">
    <style>
        .collection-faq-row {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: rgba(255, 255, 255, 0.03);
        }
        .collection-faq-row .faq-row-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }

        /* Select2 multi — dark tags, readable text + clear X */
        .js-collection-categories + .select2-container,
        .js-collection-uncategorized-products + .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 42px;
            background-color: #1b1b1b !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 6px;
            padding: 4px 6px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #bf8834 !important;
            box-shadow: 0 0 0 0.15rem rgba(191, 136, 52, 0.25);
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #111111 !important;
            border: 1px solid rgba(255, 255, 255, 0.28) !important;
            border-radius: 4px;
            color: #f5f5f5 !important;
            padding: 3px 8px 3px 24px;
            margin-top: 4px;
            margin-right: 6px;
            font-weight: 500;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
            color: #f5f5f5 !important;
            padding-left: 0;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #f5f5f5 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.2) !important;
            background: transparent !important;
            font-weight: 700;
            font-size: 16px;
            line-height: 1;
            left: 0;
            padding: 0 6px;
            margin-right: 0;
            border-radius: 4px 0 0 4px;
        }
        body.dark-only .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #ffffff !important;
            opacity: 1;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:focus {
            background: #bf8834 !important;
            color: #0f0f0f !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #f5f5f5;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            color: rgba(245, 245, 245, 0.55);
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            color: #f5f5f5 !important;
            caret-color: #f5f5f5;
        }
        .select2-dropdown {
            background-color: #1b1b1b !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #f5f5f5;
        }
        .select2-container--default .select2-results__option {
            color: #f5f5f5;
            padding: 8px 12px;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #bf8834 !important;
            color: #0f0f0f !important;
        }
        .select2-container--default .select2-results__option--selected {
            background-color: #2a2a2a !important;
            color: #f5f5f5 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/libs/js/select2/select2.full.min.js') }}"></script>
    <script>
        (function ($) {
            'use strict';

            var $categories = $('.js-collection-categories');
            if ($categories.length) {
                $categories.select2({
                    width: '100%',
                    placeholder: @json(__('Select categories')),
                    allowClear: false,
                    closeOnSelect: false
                });
            }

            var $uncategorizedProducts = $('.js-collection-uncategorized-products');
            if ($uncategorizedProducts.length) {
                $uncategorizedProducts.select2({
                    width: '100%',
                    placeholder: @json(__('Select products with no category')),
                    allowClear: true,
                    closeOnSelect: false
                });
            }

            var $faqList = $('#collection-faq-list');
            var $faqTemplate = $('#collection-faq-template');

            function reindexFaqs() {
                $faqList.find('.collection-faq-row').each(function (index) {
                    $(this).find('[data-faq-field="question"]').attr('name', 'faqs[' + index + '][question]');
                    $(this).find('[data-faq-field="answer"]').attr('name', 'faqs[' + index + '][answer]');
                });
            }

            $('#collection-faq-add').on('click', function () {
                if (!$faqTemplate.length) {
                    return;
                }

                $faqList.append($faqTemplate.html());
                reindexFaqs();
            });

            $faqList.on('click', '.js-faq-remove', function () {
                $(this).closest('.collection-faq-row').remove();
                reindexFaqs();
            });

            reindexFaqs();
        })(jQuery);
    </script>
@endpush
