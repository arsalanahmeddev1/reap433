/**
 * Grouped product variations (attr_blocks). Vanilla JS, scoped to product create/edit forms only.
 * Re-indexes names before submit (capture phase) so the existing ajax-form / FormData flow stays intact.
 */
(function () {
    'use strict';

    var FORM_IDS = ['createProductForm', 'editProductForm'];

    function getProductForm(el) {
        if (!el || !el.closest) {
            return null;
        }
        var f = el.closest('form');
        if (!f || !f.id) {
            return null;
        }
        return FORM_IDS.indexOf(f.id) !== -1 ? f : null;
    }

    function getAllWooBlocks(form) {
        var blocks = [];
        var draft = form.querySelector('#attr-blocks-draft');
        var container = form.querySelector('#attr-blocks-container');

        if (draft) {
            draft.querySelectorAll('.js-woo-attr-block').forEach(function (block) {
                blocks.push(block);
            });
        }
        if (container) {
            container.querySelectorAll('.js-woo-attr-block').forEach(function (block) {
                blocks.push(block);
            });
        }

        return blocks;
    }

    function reindexWooAttrBlocks(form) {
        var blocks = getAllWooBlocks(form);
        blocks.forEach(function (block, bi) {
            var colorInp = block.querySelector('.js-woo-color');
            if (colorInp) {
                colorInp.setAttribute('name', 'attr_blocks[' + bi + '][color]');
            }
            var rows = block.querySelectorAll('.js-woo-rows-tbody .js-woo-row');
            rows.forEach(function (row, ri) {
                var sizeInp = row.querySelector('.js-woo-size');
                var priceInp = row.querySelector('.js-woo-price');
                var imgInp = row.querySelector('.js-woo-image');
                var existingInp = row.querySelector('.js-woo-has-existing-image');
                if (sizeInp) {
                    sizeInp.setAttribute('name', 'attr_blocks[' + bi + '][rows][' + ri + '][size]');
                }
                if (priceInp) {
                    priceInp.setAttribute('name', 'attr_blocks[' + bi + '][rows][' + ri + '][price]');
                }
                if (imgInp) {
                    imgInp.setAttribute('name', 'attr_blocks[' + bi + '][rows][' + ri + '][image]');
                }
                if (existingInp) {
                    existingInp.setAttribute('name', 'attr_blocks[' + bi + '][rows][' + ri + '][has_existing_image]');
                }
            });
        });
    }

    function pruneEmptyWooRows(form, forSubmit) {
        getAllWooBlocks(form).forEach(function (block) {
            var tb = block.querySelector('.js-woo-rows-tbody');
            if (!tb) {
                return;
            }
            var rows = tb.querySelectorAll('.js-woo-row');
            rows.forEach(function (tr) {
                var sizeInp = tr.querySelector('.js-woo-size');
                var priceInp = tr.querySelector('.js-woo-price');
                var imgInp = tr.querySelector('.js-woo-image');
                var size = sizeInp && sizeInp.value ? sizeInp.value.trim() : '';
                var price = priceInp && priceInp.value ? priceInp.value.trim() : '';
                var hasFile = imgInp && imgInp.files && imgInp.files.length > 0;
                var hasExistingImage = !!tr.querySelector('.js-woo-has-existing-image');
                if (size === '' && (price === '' || parseFloat(price) <= 0) && !hasFile && !hasExistingImage) {
                    if (tb.querySelectorAll('.js-woo-row').length > 1 || forSubmit) {
                        tr.remove();
                    }
                }
            });
            if (!forSubmit && tb.querySelectorAll('.js-woo-row').length === 0) {
                appendRowToTbody(tb);
            }
        });
    }

    function toggleDraftArea(form) {
        var draft = form.querySelector('#attr-blocks-draft');
        if (!draft) {
            return;
        }
        draft.style.display = draft.querySelectorAll('.js-woo-attr-block').length > 0 ? '' : 'none';
    }

    function toggleWooEmptyHint(form) {
        var container = form.querySelector('#attr-blocks-container');
        if (!container) {
            return;
        }
        var hint = container.querySelector('.js-woo-empty-hint');
        var hasBlocks = container.querySelectorAll('.js-woo-attr-block').length > 0;
        if (hint) {
            hint.style.display = hasBlocks ? 'none' : '';
        }
    }

    function rowHasContent(row) {
        var sizeInp = row.querySelector('.js-woo-size');
        var priceInp = row.querySelector('.js-woo-price');
        var imgInp = row.querySelector('.js-woo-image');
        var size = sizeInp && sizeInp.value ? sizeInp.value.trim() : '';
        var price = priceInp && priceInp.value ? parseFloat(priceInp.value) : 0;
        var hasFile = imgInp && imgInp.files && imgInp.files.length > 0;
        var hasExistingImage = !!row.querySelector('.js-woo-has-existing-image');

        return size !== '' || price > 0 || hasFile || hasExistingImage;
    }

    function rowHasRequiredImage(row) {
        var imgInp = row.querySelector('.js-woo-image');
        var hasFile = imgInp && imgInp.files && imgInp.files.length > 0;
        var hasExistingImage = !!row.querySelector('.js-woo-has-existing-image');

        return hasFile || hasExistingImage;
    }

    function validateVariationRows(form) {
        var blocks = getAllWooBlocks(form);
        if (!blocks.length) {
            return {
                valid: false,
                message: 'Add at least one variation with one attribute row.',
            };
        }

        var hasContentRow = false;

        for (var i = 0; i < blocks.length; i++) {
            var rows = blocks[i].querySelectorAll('.js-woo-rows-tbody .js-woo-row');
            for (var j = 0; j < rows.length; j++) {
                if (!rowHasContent(rows[j])) {
                    continue;
                }

                hasContentRow = true;

                if (!rowHasRequiredImage(rows[j])) {
                    return {
                        valid: false,
                        message: 'Each attribute row must include an image.',
                        fieldKey: 'attr_blocks.' + i + '.rows.' + j + '.image',
                    };
                }
            }
        }

        if (!hasContentRow) {
            return {
                valid: false,
                message: 'Add at least one variation attribute row with name, price, or image.',
            };
        }

        return { valid: true };
    }

    function appendRowToTbody(tbody) {
        var tpl = document.getElementById('woo-tpl-row');
        if (!tpl || !tpl.content) {
            return;
        }
        var frag = tpl.content.cloneNode(true);
        var tr = frag.querySelector('tr');
        if (tr) {
            tbody.appendChild(tr);
        }
    }

    function appendEmptyBlock(draftContainer) {
        var tpl = document.getElementById('woo-tpl-block');
        if (!tpl || !tpl.content) {
            return null;
        }
        var frag = tpl.content.cloneNode(true);
        var block = frag.querySelector('.js-woo-attr-block');
        if (!block) {
            return null;
        }
        draftContainer.appendChild(block);
        return block;
    }

    function onSubmitCapture(e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') {
            return;
        }
        if (FORM_IDS.indexOf(form.id) === -1) {
            return;
        }
        var typeSel = form.querySelector('#product_type_id');
        var slug = typeSel && typeSel.options[typeSel.selectedIndex]
            ? typeSel.options[typeSel.selectedIndex].getAttribute('data-slug')
            : '';
        if (slug !== 'variable') {
            return;
        }

        var variationCheck = validateVariationRows(form);
        if (!variationCheck.valid) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var variationMessage = variationCheck.message;
            var errors = {};
            if (variationCheck.fieldKey) {
                errors[variationCheck.fieldKey] = [variationMessage];
            } else {
                errors.attr_blocks = [variationMessage];
            }
            if (typeof showAjaxValidationErrors === 'function') {
                showAjaxValidationErrors(form, {
                    errors: errors,
                });
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Variations required',
                    text: variationMessage,
                });
            } else {
                window.alert(variationMessage);
            }
            return;
        }

        pruneEmptyWooRows(form, true);
        reindexWooAttrBlocks(form);
    }

    function onClick(e) {
        var t = e.target;
        if (!t.closest) {
            return;
        }
        var form = getProductForm(t);
        if (!form) {
            return;
        }
        var typeSel = form.querySelector('#product_type_id');
        var slug = typeSel && typeSel.options[typeSel.selectedIndex]
            ? typeSel.options[typeSel.selectedIndex].getAttribute('data-slug')
            : '';
        if (slug !== 'variable') {
            return;
        }

        if (t.closest('#btn-woo-add-color-group')) {
            e.preventDefault();
            var draft = form.querySelector('#attr-blocks-draft');
            if (draft) {
                var block = appendEmptyBlock(draft);
                reindexWooAttrBlocks(form);
                toggleDraftArea(form);
                toggleWooEmptyHint(form);
                if (block && typeof block.scrollIntoView === 'function') {
                    block.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
            return;
        }

        var addSizeBtn = t.closest('.js-woo-add-size');
        if (addSizeBtn) {
            e.preventDefault();
            var block = addSizeBtn.closest('.js-woo-attr-block');
            var tb = block && block.querySelector('.js-woo-rows-tbody');
            if (tb) {
                appendRowToTbody(tb);
                reindexWooAttrBlocks(form);
            }
            return;
        }

        var rmBlock = t.closest('.js-woo-remove-block');
        if (rmBlock) {
            e.preventDefault();
            rmBlock.closest('.js-woo-attr-block').remove();
            reindexWooAttrBlocks(form);
            toggleDraftArea(form);
            toggleWooEmptyHint(form);
            return;
        }

        var rmRow = t.closest('.js-woo-remove-row');
        if (rmRow) {
            e.preventDefault();
            var row = rmRow.closest('.js-woo-row');
            var tbody = row && row.closest('.js-woo-rows-tbody');
            if (!tbody || !row) {
                return;
            }
            if (tbody.querySelectorAll('.js-woo-row').length <= 1) {
                return;
            }
            row.remove();
            reindexWooAttrBlocks(form);
        }
    }

    document.addEventListener('submit', onSubmitCapture, true);
    document.addEventListener('click', onClick, false);
})();
