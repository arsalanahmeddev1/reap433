(function initStorefrontCart() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const formatMoney = (amount) => '$' + Number(amount || 0).toFixed(2);

  const updateCartBadge = (count) => {
    document.querySelectorAll('[data-cart-count]').forEach((el) => {
      el.textContent = String(count);
      el.hidden = false;
    });
    document.querySelectorAll('.cart-btn').forEach((btn) => {
      btn.setAttribute('aria-label', `Shopping cart, ${count} items`);
    });
  };

  const showToast = (message) => {
    const toast = document.createElement('div');
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    Object.assign(toast.style, {
      position: 'fixed',
      bottom: '24px',
      left: '50%',
      transform: 'translateX(-50%) translateY(12px)',
      background: 'var(--c-black-soft)',
      border: '1px solid var(--c-gold)',
      color: 'var(--c-cream)',
      padding: '14px 24px',
      borderRadius: '12px',
      fontFamily: 'var(--font-heading)',
      fontSize: '13px',
      fontWeight: '700',
      zIndex: '9999',
      opacity: '0',
      transition: 'all 300ms ease',
      boxShadow: '0 8px 32px rgba(0,0,0,0.5)',
      maxWidth: '90vw',
      textAlign: 'center',
    });
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateX(-50%) translateY(0)';
    });
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(-50%) translateY(12px)';
      setTimeout(() => toast.remove(), 350);
    }, 2800);
  };

  const postJson = async (url, method, body) => {
    const response = await fetch(url, {
      method,
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(body),
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      throw new Error(data.message || 'Request failed');
    }

    return data;
  };

  const flashAddedButton = (btn) => {
    if (!btn || btn.tagName === 'A') return;
    const orig = btn.textContent;
    btn.textContent = '✓ Added!';
    btn.style.background = 'linear-gradient(135deg, #4ade80, #16a34a)';
    setTimeout(() => {
      btn.textContent = orig;
      btn.style.background = '';
    }, 2200);
  };

  document.addEventListener('click', async (event) => {
    const btn = event.target.closest('.add-to-cart');
    if (!btn || btn.disabled) return;

    event.preventDefault();
    event.stopImmediatePropagation();

    const productId = btn.dataset.id;
    if (!productId) return;

    if (!window.__cartStoreUrl) {
      showToast('Cart is not configured');
      return;
    }

    const payload = {
      product_id: Number(productId),
      qty: 1,
    };

    if (btn.dataset.variationId) {
      payload.product_variation_id = Number(btn.dataset.variationId);
    }

    if (btn.dataset.linePrice) {
      payload.line_price = Number(btn.dataset.linePrice);
    }

    btn.disabled = true;

    try {
      const res = await postJson(window.__cartStoreUrl, 'POST', payload);
      if (res.success) {
        updateCartBadge(res.count);
        flashAddedButton(btn);
        showToast(res.message || 'Added to cart');
      }
    } catch (error) {
      showToast(error.message || 'Could not add to cart');
    } finally {
      btn.disabled = false;
    }
  });

  document.addEventListener('click', async (event) => {
    const plus = event.target.closest('.cart-qty-plus');
    const minus = event.target.closest('.cart-qty-minus');
    const removeBtn = event.target.closest('.cart-remove-item');

    if (plus || minus) {
      const row = (plus || minus).closest('[data-cart-item-id]');
      if (!row) return;
      const id = row.dataset.cartItemId;
      const qtyEl = row.querySelector('[data-cart-qty]');
      let qty = parseInt(qtyEl?.textContent || '1', 10);
      qty = plus ? qty + 1 : Math.max(1, qty - 1);

      try {
        const res = await postJson(`${window.__cartUpdateUrl}/${id}`, 'PATCH', { qty });
        if (res.success) {
          if (qtyEl) qtyEl.textContent = String(res.qty);
          const lineTotal = row.querySelector('[data-line-total]');
          if (lineTotal) lineTotal.textContent = formatMoney(res.itemSubtotal);
          const subtotal = document.querySelector('[data-cart-subtotal]');
          const total = document.querySelector('[data-cart-total]');
          if (subtotal) subtotal.textContent = formatMoney(res.cartSubtotal);
          if (total) total.textContent = formatMoney(res.cartTotal);
          updateCartBadge(res.cartCount);
        }
      } catch (error) {
        showToast(error.message || 'Could not update quantity');
      }
    }

    if (removeBtn) {
      const row = removeBtn.closest('[data-cart-item-id]');
      if (!row) return;
      const id = row.dataset.cartItemId;

      try {
        const res = await postJson(`${window.__cartUpdateUrl}/${id}`, 'DELETE', {});
        if (res.success) {
          row.remove();
          const subtotal = document.querySelector('[data-cart-subtotal]');
          const total = document.querySelector('[data-cart-total]');
          if (subtotal) subtotal.textContent = formatMoney(res.cartSubtotal);
          if (total) total.textContent = formatMoney(res.cartTotal);
          updateCartBadge(res.cartCount);

          if (res.cartCount === 0) {
            const list = document.querySelector('[data-cart-list]');
            if (list) {
              list.innerHTML = '<p class="cart-empty">Your cart is empty.</p>';
            }
          }
          showToast('Item removed');
        }
      } catch (error) {
        showToast(error.message || 'Could not remove item');
      }
    }
  });
})();
