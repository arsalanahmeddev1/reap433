(function () {
    const root = document.getElementById('product-customizer');
    if (!root || typeof fabric === 'undefined') return;

    const options = JSON.parse(root.dataset.options || '{}');
    const saveUrl = root.dataset.saveUrl;
    const csrf = root.dataset.csrf;
    const fee = parseFloat(root.dataset.fee || '0') || 0;

    const productImage = document.getElementById('pc-product-image');
    const printAreaEl = document.getElementById('pc-print-area');
    const colorsEl = document.getElementById('pc-colors');
    const sizesEl = document.getElementById('pc-sizes');
    const placementEl = document.getElementById('pc-placement');
    const statusEl = document.getElementById('pc-status');
    const addCartBtn = document.getElementById('pc-add-cart');
    const wrap = root.querySelector('.product-customizer__canvas-wrap');

    // Logical design space (stable coordinates for objects + export).
    const BASE_W = 500;
    const BASE_H = 560;
    const PRINT_AREA = { left: 100, top: 80, width: 300, height: 360 };

    let selectedColor = options.colors?.[0]?.name || null;
    let selectedSize = null;
    let selectedVariant = null;
    let customizationUuid = null;
    let history = [];
    let historyIndex = -1;
    let applyingHistory = false;

    const canvas = new fabric.Canvas('pc-canvas', {
        width: BASE_W,
        height: BASE_H,
        preserveObjectStacking: true,
        selection: true,
        allowTouchScrolling: false,
        stopContextMenu: true,
    });

    // Ensure interactive layer sits above the product photo.
    const container = canvas.wrapperEl;
    if (container) {
        container.classList.add('product-customizer__fabric');
    }

    function setStatus(msg) {
        if (statusEl) statusEl.textContent = msg || '';
    }

    function enableObject(obj) {
        if (!obj) return;
        obj.set({
            selectable: true,
            evented: true,
            hasControls: true,
            hasBorders: true,
            lockScalingFlip: true,
            cornerStyle: 'circle',
            cornerColor: '#C9A227',
            borderColor: '#C9A227',
            transparentCorners: false,
        });
    }

    function clampToPrintArea(obj) {
        if (!obj) return;
        obj.setCoords();
        const bound = obj.getBoundingRect(true, true);
        let dx = 0;
        let dy = 0;

        if (bound.left < PRINT_AREA.left) {
            dx = PRINT_AREA.left - bound.left;
        }
        if (bound.top < PRINT_AREA.top) {
            dy = PRINT_AREA.top - bound.top;
        }
        if (bound.left + bound.width > PRINT_AREA.left + PRINT_AREA.width) {
            dx = (PRINT_AREA.left + PRINT_AREA.width) - (bound.left + bound.width);
        }
        if (bound.top + bound.height > PRINT_AREA.top + PRINT_AREA.height) {
            dy = (PRINT_AREA.top + PRINT_AREA.height) - (bound.top + bound.height);
        }

        if (dx !== 0 || dy !== 0) {
            obj.set({
                left: (obj.left || 0) + dx,
                top: (obj.top || 0) + dy,
            });
            obj.setCoords();
        }
    }

    /**
     * Keep Fabric at fixed BASE size so drag/resize hit-testing stays accurate.
     */
    function layoutCanvas() {
        if (!wrap || !printAreaEl || !container) return;

        canvas.setDimensions({ width: BASE_W, height: BASE_H });
        canvas.setZoom(1);
        canvas.calcOffset();

        wrap.style.width = '100%';
        wrap.style.maxWidth = BASE_W + 'px';
        wrap.style.height = BASE_H + 'px';

        container.style.transform = 'none';
        container.style.width = BASE_W + 'px';
        container.style.height = BASE_H + 'px';
        container.style.left = '0';
        container.style.top = '0';

        printAreaEl.style.left = PRINT_AREA.left + 'px';
        printAreaEl.style.top = PRINT_AREA.top + 'px';
        printAreaEl.style.width = PRINT_AREA.width + 'px';
        printAreaEl.style.height = PRINT_AREA.height + 'px';

        canvas.requestRenderAll();
    }

    // Recalculate offsets before interactions (helps after layout/scroll).
    canvas.on('mouse:down', () => canvas.calcOffset());
    window.addEventListener('scroll', () => canvas.calcOffset(), true);

    function findVariant() {
        const list = options.variants || [];
        return list.find((v) => {
            const colorOk = !selectedColor || String(v.color || 'Default') === String(selectedColor);
            const sizeOk = !selectedSize || String(v.size || 'One size') === String(selectedSize);
            return colorOk && sizeOk;
        }) || null;
    }

    function syncVariantUI() {
        selectedVariant = findVariant();
        if (selectedVariant?.thumbnail_url) {
            productImage.src = selectedVariant.thumbnail_url;
        }
        addCartBtn.disabled = !selectedVariant;
        if (!selectedVariant) {
            setStatus('Select an available color and size.');
            return;
        }

        const currency = selectedVariant.currency || 'USD';
        const salePrice = Number(selectedVariant.price || 0).toFixed(2);
        const originalPrice = Number(selectedVariant.original_price || selectedVariant.price || 0).toFixed(2);
        const hasDiscount = Number(originalPrice) > Number(salePrice);
        const priceLabel = hasDiscount
            ? `${currency} ${originalPrice} → ${salePrice}`
            : `${currency} ${salePrice}`;

        setStatus(
            `Selected: ${selectedVariant.color || ''} / ${selectedVariant.size || ''} — ${priceLabel}${fee ? ` (+${fee.toFixed(2)} fee)` : ''}`
        );

    const COLOR_HEX = {
        berry: '#8e3a59',
        black: '#1a1a1a',
        'black camo': '#2c2c2c',
        'black/ grey': '#2a2a2a',
        'black/ red': '#1a1a1a',
        'black/natural': '#1a1a1a',
        'carbon grey': '#5a5a5a',
        'charcoal grey': '#4a4a4a',
        'charcoal heather': '#5c5c5c',
        'cool heather': '#9aa0a6',
        'dusty rose': '#c9a0a0',
        'forest green': '#2d4a3e',
        grey: '#8a8a8a',
        'harbor blue': '#4a6d8c',
        'heather blue lagoon': '#6a9aaa',
        'heather deep teal': '#3d6b6e',
        'heather grey': '#b0b0b0',
        'heather grey / black': '#9a9a9a',
        'heather mauve': '#a67f8a',
        'heather navy': '#3d4a5c',
        'heather red': '#a85a5a',
        'heather stone': '#a8a29e',
        'heather true royal': '#4a5f9a',
        khaki: '#c3b091',
        latte: '#c4a484',
        leaf: '#5a7a4a',
        'light violet': '#c4b0d4',
        maroon: '#6b2d3c',
        matte: '#2b2b2b',
        mauve: '#9a6b7a',
        'military green': '#4a5d23',
        natural: '#e8dfd0',
        navy: '#1b2a4a',
        'navy blazer': '#1e2f4d',
        peach: '#e8b4a0',
        'pigment alpine green': '#4a6b55',
        'pigment black': '#1a1a1a',
        'pigment light blue': '#8bb5d4',
        pink: '#e8a0b0',
        red: '#c0392b',
        'royal blue': '#2b4c9b',
        sandshell: '#e6d5b8',
        'solid black blend': '#1a1a1a',
        'solid white blend': '#f5f5f5',
        stone: '#b5aea3',
        storm: '#6e7278',
        turquoise: '#40e0d0',
        'vintage gold': '#c9a227',
        white: '#f5f5f5',
        'white front, silver back': '#e8e8e8',
        'white sage and lavender': '#d4cfc8',
        wood: '#8b6914',
        yellow: '#f1c40f',
    };

    const LIGHT_COLORS = new Set([
        'white', 'solid white blend', 'natural', 'sandshell', 'yellow', 'peach', 'latte', 'khaki',
    ]);

    function colorHex(name) {
        if (!name) return '#888888';
        return COLOR_HEX[String(name).toLowerCase()] || null;
    }

    function renderColors() {
        colorsEl.innerHTML = '';
        (options.colors || []).forEach((color) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const isLight = LIGHT_COLORS.has(String(color.name || '').toLowerCase());
            btn.className = 'product-customizer__swatch'
                + (color.name === selectedColor ? ' is-active' : '')
                + (isLight ? ' is-light' : '');
            btn.title = color.name;
            btn.setAttribute('aria-label', color.name);
            btn.setAttribute('role', 'option');
            btn.setAttribute('aria-selected', color.name === selectedColor ? 'true' : 'false');

            const hex = colorHex(color.name);
            if (hex) {
                btn.style.backgroundColor = hex;
            } else if (color.thumbnail_url) {
                btn.style.backgroundImage = `url(${color.thumbnail_url})`;
                btn.style.backgroundSize = 'cover';
                btn.style.backgroundPosition = 'center';
            } else {
                btn.style.backgroundColor = '#888888';
            }

            btn.addEventListener('click', () => {
                selectedColor = color.name;
                const sizes = color.sizes || [];
                if (!sizes.includes(selectedSize)) selectedSize = sizes[0] || null;
                renderColors();
                renderSizes();
                syncVariantUI();
            });
            colorsEl.appendChild(btn);
        });

        let label = document.getElementById('pc-color-label');
        if (!label) {
            label = document.createElement('p');
            label.id = 'pc-color-label';
            label.className = 'product-customizer__hint';
            colorsEl.parentNode?.appendChild(label);
        }
        label.textContent = selectedColor ? `Selected: ${selectedColor}` : '';
    }

    function renderSizes() {
        sizesEl.innerHTML = '';
        const color = (options.colors || []).find((c) => c.name === selectedColor);
        const sizes = color?.sizes || [];
        if (!selectedSize) selectedSize = sizes[0] || null;
        sizes.forEach((size) => {
            const available = (options.variants || []).some((v) =>
                String(v.color || 'Default') === String(selectedColor) &&
                String(v.size || 'One size') === String(size) &&
                (v.availability_status === 'active' || !v.availability_status)
            );
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'product-customizer__size' + (size === selectedSize ? ' is-active' : '');
            btn.textContent = size;
            btn.disabled = !available;
            btn.addEventListener('click', () => {
                if (!available) return;
                selectedSize = size;
                renderSizes();
                syncVariantUI();
            });
            sizesEl.appendChild(btn);
        });
    }

    function renderPlacements() {
        placementEl.innerHTML = '';
        const skip = new Set(['preview', 'mockup', 'default']);
        let placements = (options.placements || []).filter((p) => p && p.type && !skip.has(String(p.type).toLowerCase()));
        if (!placements.length) {
            placements = [{ type: 'front', label: 'Front' }];
        }
        placements.forEach((p) => {
            const opt = document.createElement('option');
            opt.value = p.type;
            opt.textContent = p.label || p.type;
            placementEl.appendChild(opt);
        });
    }

    function pushHistory() {
        if (applyingHistory) return;
        const json = JSON.stringify(canvas.toJSON(['selectable', 'evented']));
        history = history.slice(0, historyIndex + 1);
        history.push(json);
        if (history.length > 40) history.shift();
        historyIndex = history.length - 1;
    }

    function loadHistory(index) {
        if (index < 0 || index >= history.length) return;
        applyingHistory = true;
        canvas.loadFromJSON(history[index], () => {
            canvas.getObjects().forEach((obj) => {
                enableObject(obj);
                clampToPrintArea(obj);
            });
            canvas.requestRenderAll();
            applyingHistory = false;
            historyIndex = index;
        });
    }

    canvas.on('object:moving', (e) => clampToPrintArea(e.target));
    canvas.on('object:scaling', (e) => clampToPrintArea(e.target));
    canvas.on('object:rotating', (e) => clampToPrintArea(e.target));
    canvas.on('object:modified', (e) => {
        clampToPrintArea(e.target);
        pushHistory();
        canvas.requestRenderAll();
    });
    canvas.on('object:added', (e) => {
        enableObject(e.target);
        if (!applyingHistory) pushHistory();
    });

    document.getElementById('pc-upload')?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        if (!['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
            setStatus('Please upload a PNG or JPG image.');
            e.target.value = '';
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            setStatus('Image must be 10MB or smaller.');
            e.target.value = '';
            return;
        }

        setStatus('Loading design…');
        const reader = new FileReader();
        reader.onload = () => {
            const imgEl = new Image();
            imgEl.onload = () => {
                if (imgEl.width < 100 || imgEl.height < 100) {
                    setStatus('Image dimensions must be at least 100×100 pixels.');
                    return;
                }
                const img = new fabric.Image(imgEl);
                const maxW = PRINT_AREA.width * 0.75;
                const maxH = PRINT_AREA.height * 0.75;
                const scale = Math.min(maxW / img.width, maxH / img.height, 1);
                img.set({
                    left: PRINT_AREA.left + (PRINT_AREA.width - img.width * scale) / 2,
                    top: PRINT_AREA.top + (PRINT_AREA.height - img.height * scale) / 2,
                    scaleX: scale,
                    scaleY: scale,
                    originX: 'left',
                    originY: 'top',
                });
                enableObject(img);
                canvas.add(img);
                canvas.setActiveObject(img);
                clampToPrintArea(img);
                canvas.requestRenderAll();
                setStatus('Design added — drag, resize, or rotate it inside the print area.');
            };
            imgEl.onerror = () => setStatus('Could not read that image file.');
            imgEl.src = reader.result;
        };
        reader.onerror = () => setStatus('Could not read that image file.');
        reader.readAsDataURL(file);
    });

    document.getElementById('pc-add-text')?.addEventListener('click', () => {
        const content = (document.getElementById('pc-text')?.value || '').trim();
        if (!content) {
            setStatus('Enter some text first.');
            return;
        }
        const text = new fabric.Textbox(content, {
            left: PRINT_AREA.left + 24,
            top: PRINT_AREA.top + 40,
            width: PRINT_AREA.width - 48,
            fontFamily: document.getElementById('pc-font')?.value || 'Arial',
            fontSize: parseInt(document.getElementById('pc-font-size')?.value || '28', 10),
            fill: document.getElementById('pc-font-color')?.value || '#111111',
            textAlign: document.getElementById('pc-text-align')?.value || 'center',
            editable: true,
            backgroundColor: '',
            originX: 'left',
            originY: 'top',
        });
        enableObject(text);
        canvas.add(text);
        canvas.setActiveObject(text);
        clampToPrintArea(text);
        canvas.requestRenderAll();
        setStatus('Text added — drag or edit it on the canvas.');
    });

    // Live-update selected text styles.
    ['pc-font', 'pc-font-size', 'pc-font-color', 'pc-text-align'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', () => {
            const obj = canvas.getActiveObject();
            if (!obj || !String(obj.type || '').includes('text')) return;
            if (id === 'pc-font') obj.set('fontFamily', document.getElementById(id).value);
            if (id === 'pc-font-size') obj.set('fontSize', parseInt(document.getElementById(id).value || '28', 10));
            if (id === 'pc-font-color') obj.set('fill', document.getElementById(id).value);
            if (id === 'pc-text-align') obj.set('textAlign', document.getElementById(id).value);
            canvas.requestRenderAll();
            pushHistory();
        });
    });

    root.querySelectorAll('[data-action]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            const obj = canvas.getActiveObject();

            if (action === 'delete' && obj) {
                canvas.remove(obj);
                canvas.discardActiveObject();
                pushHistory();
            }
            if (action === 'duplicate' && obj) {
                obj.clone((cloned) => {
                    enableObject(cloned);
                    cloned.set({ left: (obj.left || 0) + 16, top: (obj.top || 0) + 16 });
                    canvas.add(cloned);
                    canvas.setActiveObject(cloned);
                    clampToPrintArea(cloned);
                    canvas.requestRenderAll();
                    pushHistory();
                });
            }
            if (action === 'bringForward' && obj) {
                canvas.bringForward(obj);
                canvas.requestRenderAll();
                pushHistory();
            }
            if (action === 'sendBackward' && obj) {
                canvas.sendBackwards(obj);
                canvas.requestRenderAll();
                pushHistory();
            }
            if (action === 'undo') loadHistory(historyIndex - 1);
            if (action === 'redo') loadHistory(historyIndex + 1);
            if (action === 'reset') {
                canvas.clear();
                canvas.discardActiveObject();
                pushHistory();
                setStatus('Canvas cleared.');
            }
            canvas.requestRenderAll();
        });
    });

    /**
     * Print = cropped print-area artwork for Printful.
     * Preview = full-canvas design layer only (transparent bg). The product photo
     * lives in a separate <img>, so the server composites mockup + design for cart.
     */
    async function exportDesignDataUrls() {
        canvas.discardActiveObject();
        canvas.backgroundColor = null;
        canvas.requestRenderAll();

        const print = canvas.toDataURL({
            format: 'png',
            multiplier: 3,
            left: PRINT_AREA.left,
            top: PRINT_AREA.top,
            width: PRINT_AREA.width,
            height: PRINT_AREA.height,
            enableRetinaScaling: false,
        });

        const preview = canvas.toDataURL({
            format: 'png',
            multiplier: 1,
            enableRetinaScaling: false,
        });

        return { print, preview };
    }

    async function saveCustomization(finalizeAndCart) {
        if (!selectedVariant) {
            setStatus('Please select a valid color and size.');
            return;
        }
        if (canvas.getObjects().length === 0) {
            setStatus('Add a design image or text before continuing.');
            return;
        }

        setStatus(finalizeAndCart ? 'Saving and adding to cart…' : 'Generating preview…');
        addCartBtn.disabled = true;

        const exports = await exportDesignDataUrls();
        const active = canvas.getActiveObject();
        const textSettings = active && String(active.type || '').includes('text')
            ? {
                content: active.text || '',
                fontFamily: active.fontFamily,
                fontSize: active.fontSize,
                fill: active.fill,
                textAlign: active.textAlign,
                angle: active.angle,
                left: active.left,
                top: active.top,
                scaleX: active.scaleX,
                scaleY: active.scaleY,
            }
            : null;

        const payload = {
            uuid: customizationUuid,
            printful_variant_id: selectedVariant.id,
            color: selectedColor,
            size: selectedSize,
            placement: placementEl.value || 'front',
            canvas_json: JSON.stringify(canvas.toJSON()),
            text_settings: textSettings,
            image_settings: null,
            print_area: PRINT_AREA,
            preview_data_url: exports.preview,
            print_data_url: exports.print,
        };

        try {
            const res = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            if (!res.ok || !json.success) {
                throw new Error(json.message || 'Save failed');
            }
            customizationUuid = json.data.uuid;
            setStatus('Preview saved.');

            if (finalizeAndCart) {
                const addRes = await fetch('/customizations/' + encodeURIComponent(customizationUuid) + '/add-to-cart', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });
                const addJson = await addRes.json();
                if (!addRes.ok || !addJson.success) {
                    throw new Error(addJson.message || 'Could not add to cart');
                }
                window.location.href = addJson.redirect || '/cart';
                return;
            }
        } catch (err) {
            setStatus(err.message || 'Something went wrong.');
        } finally {
            addCartBtn.disabled = !selectedVariant;
        }
    }

    document.getElementById('pc-save-preview')?.addEventListener('click', () => saveCustomization(false));
    addCartBtn?.addEventListener('click', () => saveCustomization(true));

    window.addEventListener('resize', layoutCanvas);

    renderColors();
    renderSizes();
    renderPlacements();
    syncVariantUI();
    layoutCanvas();
    pushHistory();
    setStatus('Upload a design or add text, then drag it inside the gold print area.');
})();
