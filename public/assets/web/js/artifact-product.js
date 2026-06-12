(function initArtifactProductMatrix() {
  const root = document.getElementById('product-detail-root');
  if (!root || !root.dataset.matrix) return;

  let matrix;
  try {
    matrix = JSON.parse(root.dataset.matrix);
  } catch (e) {
    return;
  }

  const mainImage = document.getElementById('product-main-image');
  const priceEl = document.getElementById('product-detail-price');
  const hintEl = document.getElementById('variation-hint');
  const addBtn = document.getElementById('product-add-to-cart');
  const variationInput = document.getElementById('selected-variation-id');
  const selected = {};

  const formatPrice = (amount) => '$' + Number(amount).toFixed(2);

  const applyVariation = (variation, unavailable = false) => {
    if (!variation) {
      if (priceEl) {
        if (matrix.fromPrice != null && matrix.toPrice != null) {
          priceEl.textContent = formatPrice(matrix.fromPrice) + ' - ' + formatPrice(matrix.toPrice);
        } else {
          priceEl.textContent = priceEl.dataset.defaultPrice || '';
        }
      }
      if (hintEl && !unavailable) {
        hintEl.textContent = 'Select color and size to see price';
      }
      if (variationInput) variationInput.value = '';
      if (addBtn) {
        addBtn.disabled = true;
        delete addBtn.dataset.variationId;
        delete addBtn.dataset.linePrice;
      }
      return;
    }

    if (priceEl) priceEl.textContent = formatPrice(variation.price);
    if (hintEl) hintEl.textContent = '';
    if (variationInput) variationInput.value = String(variation.id);
    if (addBtn) {
      addBtn.disabled = false;
      addBtn.dataset.variationId = variation.id ? String(variation.id) : '';
      addBtn.dataset.linePrice = variation.price != null ? String(variation.price) : '';
    }

    if (mainImage && variation.imageUrl) {
      mainImage.src = variation.imageUrl;
    }
  };

  const findMatrixVariation = () => {
    const dims = matrix.dimensions || [];
    if (!dims.length || dims.some((d) => !selected[d.id])) {
      return null;
    }

    return matrix.variations.find((v) =>
      dims.every((d) => {
        const key = String(d.id);
        return String(v.options[key] || '').trim() === String(selected[d.id] || '').trim();
      })
    ) || null;
  };

  const resolveColorSizeSelection = () => {
    const groups = matrix.attributeGroups;
    if (!groups) return;

    const color = selected[groups.colorAttrId] || '';
    const size = selected[groups.sizeAttrId] || '';
    const colorSelect = document.getElementById('var-color-select');
    const sizeSelect = document.getElementById('var-size-select');

    if (!color) {
      if (sizeSelect) {
        sizeSelect.value = '';
        sizeSelect.disabled = true;
      }
      delete selected[groups.sizeAttrId];
      applyVariation(null);
      return;
    }

    if (sizeSelect) {
      sizeSelect.disabled = false;
    }

    if (!size) {
      const colorRow = groups.colors.find((row) => row.value === color);
      if (colorRow && colorRow.variationId) {
        if (hintEl) hintEl.textContent = 'Now choose a size';
        applyVariation({
          id: colorRow.variationId,
          price: colorRow.price,
          imageUrl: colorRow.imageUrl,
        });
        if (addBtn) {
          addBtn.disabled = true;
          delete addBtn.dataset.variationId;
          if (colorRow.price != null) {
            addBtn.dataset.linePrice = String(colorRow.price);
          }
        }
        if (variationInput) variationInput.value = '';
        return;
      }
      applyVariation(null);
      return;
    }

    if (groups.hasCombinedSkus) {
      const sizeRows = groups.sizesByColor[color] || [];
      const match = sizeRows.find((row) => row.value === size && row.variationId);
      if (match) {
        applyVariation({
          id: match.variationId,
          price: match.price,
          imageUrl: match.imageUrl,
        });
        return;
      }
    }

    const matrixMatch = findMatrixVariation();
    if (matrixMatch) {
      applyVariation(matrixMatch);
      return;
    }

    const colorRow = groups.colors.find((row) => row.value === color);
    const sizeRow = groups.sizes.find((row) => row.value === size);
    if (colorRow && sizeRow) {
      const totalPrice = Number(colorRow.price || 0) + Number(sizeRow.price || 0);
      applyVariation({
        id: colorRow.variationId,
        price: totalPrice,
        imageUrl: colorRow.imageUrl || sizeRow.imageUrl || '',
      });
      if (variationInput && colorRow.variationId) {
        variationInput.value = String(colorRow.variationId);
      }
      if (root && sizeRow.variationId) {
        root.dataset.sizeVariationId = String(sizeRow.variationId);
      }
      return;
    }

    if (hintEl) {
      hintEl.textContent = 'Select color and size to see price';
    }
    applyVariation(null, true);
  };

  const updateSizeOptionsForColor = (color) => {
    const groups = matrix.attributeGroups;
    const sizeSelect = document.getElementById('var-size-select');
    if (!groups || !sizeSelect) return;

    const rows = groups.sizesByColor[color] || groups.sizes || [];
    const current = sizeSelect.value;

    sizeSelect.innerHTML = '<option value="">Choose size</option>';
    rows.forEach((row) => {
      const option = document.createElement('option');
      option.value = row.value;
      option.textContent = row.value;
      sizeSelect.appendChild(option);
    });

    if (current && [...sizeSelect.options].some((opt) => opt.value === current)) {
      sizeSelect.value = current;
    } else {
      sizeSelect.value = '';
      delete selected[groups.sizeAttrId];
    }
  };

  const groups = matrix.attributeGroups;
  if (groups && groups.colors && groups.colors.length && groups.sizes && groups.sizes.length) {
    const colorSelect = document.getElementById('var-color-select');
    const sizeSelect = document.getElementById('var-size-select');

    if (colorSelect) {
      colorSelect.addEventListener('change', () => {
        const color = colorSelect.value;
        if (color) {
          selected[groups.colorAttrId] = color;
          const colorRow = groups.colors.find((row) => row.value === color);
          if (colorRow && colorRow.imageUrl && mainImage) {
            mainImage.src = colorRow.imageUrl;
          }
        } else {
          delete selected[groups.colorAttrId];
        }
        updateSizeOptionsForColor(color);
        resolveColorSizeSelection();
      });
    }

    if (sizeSelect) {
      sizeSelect.addEventListener('change', () => {
        const size = sizeSelect.value;
        if (size) {
          selected[groups.sizeAttrId] = size;
        } else {
          delete selected[groups.sizeAttrId];
        }
        resolveColorSizeSelection();
      });
    }
  } else if (matrix.dimensions && matrix.dimensions.length && matrix.variations && matrix.variations.length) {
    root.querySelectorAll('.variation-select').forEach((select) => {
      select.addEventListener('change', () => {
        const dimId = Number(select.dataset.dimensionId);
        const value = select.value;

        if (value) {
          selected[dimId] = value;
        } else {
          delete selected[dimId];
        }

        const match = findMatrixVariation();
        if (match) {
          applyVariation(match);
        } else {
          if (hintEl && matrix.dimensions.every((d) => selected[d.id])) {
            hintEl.textContent = 'This combination is not available.';
          }
          applyVariation(null, true);
        }
      });
    });
  }

  applyVariation(null);
})();

(function initArtifactGalleryThumbs() {
  const main = document.getElementById('product-main-image');
  document.querySelectorAll('.product-detail-thumbs img').forEach((thumb) => {
    thumb.addEventListener('click', () => {
      if (main && thumb.dataset.mainSrc) {
        main.src = thumb.dataset.mainSrc;
      }
    });
  });
})();
