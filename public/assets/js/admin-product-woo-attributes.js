/**
 * WooCommerce-style variable products with dynamic attributes.
 * Submits attr_definitions[] + variation_rows[] for ProductController.
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

    function isVariableForm(form) {
        var typeSel = form.querySelector('#product_type_id');
        var slug = typeSel && typeSel.options[typeSel.selectedIndex]
            ? typeSel.options[typeSel.selectedIndex].getAttribute('data-slug')
            : '';
        return slug === 'variable';
    }

    function getSection(form) {
        return form.querySelector('#product-variations-section');
    }

    function getAttributeBlocks(form) {
        return Array.from(form.querySelectorAll('#woo-attributes-list .js-woo-attr-block'));
    }

    function getAttributeDefinitions(form) {
        var defs = [];
        getAttributeBlocks(form).forEach(function (block) {
            var nameInp = block.querySelector('.js-woo-attr-name');
            var name = nameInp && nameInp.value ? nameInp.value.trim() : '';
            var values = [];
            block.querySelectorAll('.js-woo-tags .woo-tag-text').forEach(function (el) {
                var v = el.textContent.trim();
                if (v !== '') {
                    values.push(v);
                }
            });
            defs.push({ name: name, values: values, block: block });
        });
        return defs;
    }

    function tagExistsInBlock(block, value) {
        var needle = value.toLowerCase();
        return Array.from(block.querySelectorAll('.woo-tag-text')).some(function (el) {
            return el.textContent.trim().toLowerCase() === needle;
        });
    }

    function addTagToBlock(block, value) {
        var trimmed = (value || '').trim();
        if (trimmed === '' || tagExistsInBlock(block, trimmed)) {
            return;
        }
        var list = block.querySelector('.js-woo-tags');
        var tpl = document.getElementById('woo-tpl-tag');
        if (!list || !tpl || !tpl.content) {
            return;
        }
        var frag = tpl.content.cloneNode(true);
        var text = frag.querySelector('.woo-tag-text');
        if (!text) {
            return;
        }
        text.textContent = trimmed;
        list.appendChild(frag);
    }

    function appendAttributeBlock(form, data) {
        data = data || {};
        var list = form.querySelector('#woo-attributes-list');
        var tpl = document.getElementById('woo-tpl-attribute');
        if (!list || !tpl || !tpl.content) {
            return null;
        }
        var frag = tpl.content.cloneNode(true);
        var block = frag.querySelector('.js-woo-attr-block');
        if (!block) {
            return null;
        }
        list.appendChild(block);

        var nameInp = block.querySelector('.js-woo-attr-name');
        if (nameInp && data.name) {
            nameInp.value = data.name;
        }
        (data.values || []).forEach(function (v) {
            addTagToBlock(block, v);
        });

        toggleAttrEmptyHint(form);
        return block;
    }

    function removeAttributeBlock(block, form) {
        if (block && block.parentNode) {
            block.parentNode.removeChild(block);
        }
        toggleAttrEmptyHint(form);
        rebuildVariationTableHeader(form);
    }

    function toggleAttrEmptyHint(form) {
        var hint = form.querySelector('.js-woo-attr-empty-hint');
        var count = getAttributeBlocks(form).length;
        if (hint) {
            hint.style.display = count > 0 ? 'none' : '';
        }
    }

    function toggleVariationEmptyHint(form) {
        var hint = form.querySelector('.js-woo-empty-hint');
        var count = form.querySelectorAll('#woo-variations-tbody .js-woo-variation-row').length;
        if (hint) {
            hint.style.display = count > 0 ? 'none' : '';
        }
    }

    function rebuildVariationTableHeader(form) {
        var theadRow = form.querySelector('#woo-variations-thead-row');
        if (!theadRow) {
            return;
        }
        var defs = getAttributeDefinitions(form);
        theadRow.querySelectorAll('.js-woo-th-attr').forEach(function (el) {
            el.remove();
        });

        var priceTh = theadRow.querySelector('.js-woo-th-price');
        defs.forEach(function (def) {
            if (!def.name) {
                return;
            }
            var th = document.createElement('th');
            th.className = 'js-woo-th-attr';
            th.textContent = def.name;
            if (priceTh) {
                theadRow.insertBefore(th, priceTh);
            } else {
                theadRow.appendChild(th);
            }
        });
    }

    function variationKeyFromOptions(options) {
        return Object.keys(options)
            .sort()
            .map(function (k) {
                return k.toLowerCase() + ':' + (options[k] || '').toLowerCase();
            })
            .join('|');
    }

    function cartesianProduct(arrays) {
        if (!arrays.length) {
            return [];
        }
        return arrays.reduce(
            function (acc, curr) {
                var out = [];
                acc.forEach(function (a) {
                    curr.forEach(function (b) {
                        out.push(a.concat([b]));
                    });
                });
                return out;
            },
            [[]]
        );
    }

    function collectVariationRowData(tr) {
        var options = {};
        tr.querySelectorAll('.js-woo-var-attr').forEach(function (td) {
            var attr = td.getAttribute('data-attr-name') || '';
            options[attr] = td.textContent.trim();
        });
        var priceInp = tr.querySelector('.js-woo-price');
        var existingWrap = tr.querySelector('.js-woo-existing-image-wrap');
        var existingPreview = tr.querySelector('.js-woo-existing-image-preview');
        var hasExisting = existingWrap && !existingWrap.classList.contains('d-none');

        return {
            options: options,
            price: priceInp ? priceInp.value : '',
            imageUrl: hasExisting && existingPreview ? existingPreview.getAttribute('src') || '' : '',
            hasExistingImage: hasExisting,
        };
    }

    function appendVariationRow(form, options, data) {
        data = data || {};
        options = options || {};
        var tbody = form.querySelector('#woo-variations-tbody');
        var tpl = document.getElementById('woo-tpl-variation-row');
        if (!tbody || !tpl || !tpl.content) {
            return null;
        }

        var frag = tpl.content.cloneNode(true);
        var tr = frag.querySelector('.js-woo-variation-row');
        if (!tr) {
            return null;
        }

        var defs = getAttributeDefinitions(form);
        var priceTd = tr.querySelector('.js-woo-price').closest('td');

        defs.forEach(function (def) {
            if (!def.name) {
                return;
            }
            var td = document.createElement('td');
            td.className = 'js-woo-var-attr fw-semibold';
            td.setAttribute('data-attr-name', def.name);
            td.textContent = options[def.name] || '';
            tr.insertBefore(td, priceTd);
        });

        tr.setAttribute('data-options', JSON.stringify(options));

        var priceInp = tr.querySelector('.js-woo-price');
        if (priceInp && data.price !== undefined && data.price !== null && data.price !== '') {
            priceInp.value = data.price;
        }

        var imgInp = tr.querySelector('.js-woo-image');
        var existingWrap = tr.querySelector('.js-woo-existing-image-wrap');
        var existingPreview = tr.querySelector('.js-woo-existing-image-preview');

        if (data.hasExistingImage && data.imageUrl) {
            if (imgInp) {
                imgInp.removeAttribute('required');
            }
            if (existingWrap) {
                existingWrap.classList.remove('d-none');
            }
            if (existingPreview) {
                existingPreview.setAttribute('src', data.imageUrl);
            }
        } else if (imgInp) {
            imgInp.setAttribute('required', 'required');
        }

        tbody.appendChild(tr);
        return tr;
    }

    function generateVariations(form) {
        var defs = getAttributeDefinitions(form);
        if (!defs.length) {
            alertOrSwal('Add at least one attribute before generating variations.');
            return;
        }

        var names = [];
        for (var i = 0; i < defs.length; i++) {
            if (!defs[i].name) {
                alertOrSwal('Each attribute needs a name (e.g. Color, Size).');
                return;
            }
            var lower = defs[i].name.toLowerCase();
            if (names.indexOf(lower) !== -1) {
                alertOrSwal('Attribute names must be unique.');
                return;
            }
            names.push(lower);
            if (!defs[i].values.length) {
                alertOrSwal('Add at least one value for "' + defs[i].name + '".');
                return;
            }
        }

        rebuildVariationTableHeader(form);

        var existing = {};
        form.querySelectorAll('#woo-variations-tbody .js-woo-variation-row').forEach(function (tr) {
            var data = collectVariationRowData(tr);
            existing[variationKeyFromOptions(data.options)] = data;
        });

        var attrNames = defs.map(function (d) {
            return d.name;
        });
        var valueSets = defs.map(function (d) {
            return d.values.map(function (v) {
                return { attr: d.name, value: v };
            });
        });

        var combos = cartesianProduct(valueSets);
        var tbody = form.querySelector('#woo-variations-tbody');
        if (tbody) {
            tbody.innerHTML = '';
        }

        combos.forEach(function (combo) {
            var options = {};
            combo.forEach(function (item) {
                options[item.attr] = item.value;
            });
            var key = variationKeyFromOptions(options);
            var prev = existing[key] || {};
            appendVariationRow(form, options, prev);
        });

        toggleVariationEmptyHint(form);
    }

    function alertOrSwal(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Attributes required', text: msg });
        } else {
            window.alert(msg);
        }
    }

    function clearFormSync(form) {
        var sync = form.querySelector('#woo-form-sync');
        if (sync) {
            sync.innerHTML = '';
        }
        form.querySelectorAll('.js-woo-sync-field').forEach(function (el) {
            el.remove();
        });
    }

    function buildFormSyncInputs(form) {
        clearFormSync(form);

        var defs = getAttributeDefinitions(form);
        defs.forEach(function (def, di) {
            var nameInp = document.createElement('input');
            nameInp.type = 'hidden';
            nameInp.className = 'js-woo-sync-field';
            nameInp.name = 'attr_definitions[' + di + '][name]';
            nameInp.value = def.name;
            form.appendChild(nameInp);

            def.values.forEach(function (val, vi) {
                var valInp = document.createElement('input');
                valInp.type = 'hidden';
                valInp.className = 'js-woo-sync-field';
                valInp.name = 'attr_definitions[' + di + '][values][' + vi + ']';
                valInp.value = val;
                form.appendChild(valInp);
            });
        });

        var rows = form.querySelectorAll('#woo-variations-tbody .js-woo-variation-row');
        rows.forEach(function (tr, ri) {
            var data = collectVariationRowData(tr);
            Object.keys(data.options).forEach(function (attrName) {
                var optInp = document.createElement('input');
                optInp.type = 'hidden';
                optInp.className = 'js-woo-sync-field';
                optInp.name = 'variation_rows[' + ri + '][options][' + attrName + ']';
                optInp.value = data.options[attrName];
                form.appendChild(optInp);
            });

            var priceInp = tr.querySelector('.js-woo-price');
            if (priceInp) {
                priceInp.name = 'variation_rows[' + ri + '][price]';
            }

            var imgInp = tr.querySelector('.js-woo-image');
            if (imgInp) {
                imgInp.name = 'variation_rows[' + ri + '][image]';
            }

            var existingInp = tr.querySelector('.js-woo-has-existing-image');
            if (existingInp) {
                existingInp.name = 'variation_rows[' + ri + '][has_existing_image]';
            }
        });
    }

    function rowHasContent(tr) {
        var priceInp = tr.querySelector('.js-woo-price');
        var imgInp = tr.querySelector('.js-woo-image');
        var price = priceInp && priceInp.value ? parseFloat(priceInp.value) : 0;
        var hasFile = imgInp && imgInp.files && imgInp.files.length > 0;
        var hasExistingImage = !!tr.querySelector('.js-woo-has-existing-image');

        return price > 0 || hasFile || hasExistingImage;
    }

    function rowHasRequiredImage(tr) {
        var imgInp = tr.querySelector('.js-woo-image');
        var hasFile = imgInp && imgInp.files && imgInp.files.length > 0;
        var hasExistingImage = !!tr.querySelector('.js-woo-has-existing-image');

        return hasFile || hasExistingImage;
    }

    function validateVariationRows(form) {
        var defs = getAttributeDefinitions(form);
        if (!defs.length) {
            return { valid: false, message: 'Add at least one attribute with values.' };
        }

        for (var i = 0; i < defs.length; i++) {
            if (!defs[i].name || !defs[i].values.length) {
                return { valid: false, message: 'Each attribute needs a name and at least one value.' };
            }
        }

        var rows = form.querySelectorAll('#woo-variations-tbody .js-woo-variation-row');
        if (!rows.length) {
            return { valid: false, message: 'Click "Generate variations" after adding attribute values.' };
        }

        var hasContentRow = false;
        for (var j = 0; j < rows.length; j++) {
            if (!rowHasContent(rows[j])) {
                continue;
            }
            hasContentRow = true;
            if (!rowHasRequiredImage(rows[j])) {
                return {
                    valid: false,
                    message: 'Each variation must include an image.',
                    fieldKey: 'variation_rows.' + j + '.image',
                };
            }
        }

        if (!hasContentRow) {
            return { valid: false, message: 'Set a price or image for at least one variation row.' };
        }

        return { valid: true };
    }

    function loadInitialPayload(form) {
        var section = getSection(form);
        if (!section) {
            return;
        }
        var raw = section.getAttribute('data-initial-payload');
        if (!raw) {
            return;
        }
        var payload;
        try {
            payload = JSON.parse(raw);
        } catch (e) {
            return;
        }

        var attrs = payload.attributes || [];
        var variations = payload.variations || [];

        if (!attrs.length) {
            return;
        }

        attrs.forEach(function (attr) {
            appendAttributeBlock(form, {
                name: attr.name || '',
                values: attr.values || [],
            });
        });

        rebuildVariationTableHeader(form);

        var tbody = form.querySelector('#woo-variations-tbody');
        if (tbody) {
            tbody.innerHTML = '';
        }

        variations.forEach(function (v) {
            appendVariationRow(form, v.options || {}, {
                price: v.price || '',
                imageUrl: v.image_url || '',
                hasExistingImage: !!v.has_existing_image,
            });
        });

        toggleAttrEmptyHint(form);
        toggleVariationEmptyHint(form);
    }

    function onSubmitCapture(e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM' || FORM_IDS.indexOf(form.id) === -1) {
            return;
        }
        if (!isVariableForm(form)) {
            return;
        }

        var variationCheck = validateVariationRows(form);
        if (!variationCheck.valid) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var errors = {};
            if (variationCheck.fieldKey) {
                errors[variationCheck.fieldKey] = [variationCheck.message];
            } else {
                errors.attr_definitions = [variationCheck.message];
            }
            if (typeof showAjaxValidationErrors === 'function') {
                showAjaxValidationErrors(form, { errors: errors });
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Variations required', text: variationCheck.message });
            } else {
                window.alert(variationCheck.message);
            }
            return;
        }

        buildFormSyncInputs(form);
    }

    function onClick(e) {
        var t = e.target;
        if (!t.closest) {
            return;
        }
        var form = getProductForm(t);
        if (!form || !isVariableForm(form)) {
            return;
        }

        if (t.closest('#btn-woo-add-attribute')) {
            e.preventDefault();
            appendAttributeBlock(form, {});
            return;
        }

        if (t.closest('#btn-woo-generate-variations')) {
            e.preventDefault();
            generateVariations(form);
            return;
        }

        var removeAttr = t.closest('.js-woo-remove-attribute');
        if (removeAttr) {
            e.preventDefault();
            removeAttributeBlock(removeAttr.closest('.js-woo-attr-block'), form);
            return;
        }

        var removeTagBtn = t.closest('.woo-tag-remove');
        if (removeTagBtn) {
            e.preventDefault();
            var tag = removeTagBtn.closest('.woo-tag');
            if (tag && tag.parentNode) {
                tag.parentNode.removeChild(tag);
            }
            return;
        }

        var removeVarBtn = t.closest('.js-woo-remove-variation');
        if (removeVarBtn) {
            e.preventDefault();
            var row = removeVarBtn.closest('.js-woo-variation-row');
            if (row) {
                row.remove();
                toggleVariationEmptyHint(form);
            }
        }
    }

    function onKeydown(e) {
        if (e.key !== 'Enter') {
            return;
        }
        var input = e.target;
        if (!input.classList || !input.classList.contains('js-woo-tag-input')) {
            return;
        }
        var form = getProductForm(input);
        if (!form || !isVariableForm(form)) {
            return;
        }
        e.preventDefault();
        var block = input.closest('.js-woo-attr-block');
        if (block) {
            addTagToBlock(block, input.value);
            input.value = '';
        }
    }

    function initForms() {
        FORM_IDS.forEach(function (id) {
            var form = document.getElementById(id);
            if (form) {
                loadInitialPayload(form);
            }
        });
    }

    document.addEventListener('submit', onSubmitCapture, true);
    document.addEventListener('click', onClick, false);
    document.addEventListener('keydown', onKeydown, false);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForms);
    } else {
        initForms();
    }
})();
