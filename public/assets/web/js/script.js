/* ═══════════════════════════════════════════
   REAP433 — Interactive JavaScript
   Clean, performant, purposeful.
═══════════════════════════════════════════ */

'use strict';

// ─────────────────────────────────────────
// NAV: Scroll State
// ─────────────────────────────────────────
(function initNavScroll() {
  const header = document.getElementById('nav-header');
  if (!header) return;

  const onScroll = () => {
    header.classList.toggle('scrolled', window.scrollY > 20);
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

// ─────────────────────────────────────────
// NAV: Mobile Menu
// ─────────────────────────────────────────
(function initMobileMenu() {
  const burger    = document.getElementById('nav-hamburger');
  const menu      = document.getElementById('mobile-menu');
  const closeBtn  = document.getElementById('mobile-menu-close');
  const links     = menu ? menu.querySelectorAll('.mobile-nav-link') : [];

  if (!burger || !menu) return;

  const open = () => {
    menu.removeAttribute('hidden');
    burger.classList.add('open');
    burger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    menu.focus?.();
  };

  const close = () => {
    menu.setAttribute('hidden', '');
    burger.classList.remove('open');
    burger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    burger.focus();
  };

  burger.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);

  links.forEach(link => link.addEventListener('click', close));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !menu.hasAttribute('hidden')) close();
  });
})();

// ─────────────────────────────────────────
// NAV: Shop / Impact Mode Toggle
// ─────────────────────────────────────────
(function initModeToggle() {
  const shopBtn   = document.getElementById('shop-mode-btn');
  const impactBtn = document.getElementById('impact-mode-btn');
  const shopLinks = document.querySelectorAll('.shop-nav');
  const impactLinks = document.querySelectorAll('.impact-nav');

  if (!shopBtn || !impactBtn) return;

  const setMode = (mode) => {
    const isShop = mode === 'shop';

    shopBtn.classList.toggle('active', isShop);
    impactBtn.classList.toggle('active', !isShop);
    shopBtn.setAttribute('aria-pressed', String(isShop));
    impactBtn.setAttribute('aria-pressed', String(!isShop));

    shopLinks.forEach(l => { l.style.display = isShop ? '' : 'none'; });
    impactLinks.forEach(l => { l.style.display = isShop ? 'none' : ''; });

    if (!isShop) {
      document.getElementById('hub')?.scrollIntoView({ behavior: 'smooth' });
    } else {
      document.getElementById('shop')?.scrollIntoView({ behavior: 'smooth' });
    }
  };

  shopBtn.addEventListener('click',   () => setMode('shop'));
  impactBtn.addEventListener('click', () => setMode('impact'));

  // Sync all .mode-btn toggles
  document.querySelectorAll('.mode-btn').forEach(btn => {
    btn.addEventListener('click', () => setMode(btn.dataset.mode));
  });
})();

// ─────────────────────────────────────────
// SCROLL REVEAL — Intersection Observer
// ─────────────────────────────────────────
(function initScrollReveal() {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (prefersReduced) {
    document.querySelectorAll('[data-scroll-reveal]').forEach(el => {
      el.classList.add('revealed');
    });
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el    = entry.target;
      const delay = parseInt(el.dataset.delay || '0', 10);

      setTimeout(() => {
        el.classList.add('revealed');
      }, delay);

      observer.unobserve(el);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('[data-scroll-reveal]').forEach(el => observer.observe(el));
})();

// ─────────────────────────────────────────
// PRODUCT FILTER
// ─────────────────────────────────────────
(function initProductFilter() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const products   = document.querySelectorAll('.product-card');

  if (!filterBtns.length) return;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-pressed', 'false');
      });

      btn.classList.add('active');
      btn.setAttribute('aria-pressed', 'true');

      const filter = btn.dataset.filter;

      products.forEach((card, i) => {
        const cats = card.dataset.category || '';
        const show = filter === 'all' || cats.includes(filter);

        if (show) {
          card.hidden = false;
          card.style.display = '';
          card.style.transition = `opacity 300ms ease ${i * 40}ms, transform 300ms ease ${i * 40}ms`;
          card.style.opacity = '1';
          card.style.transform = '';
          card.style.pointerEvents = '';
        } else {
          card.style.opacity = '0';
          card.style.transform = 'scale(0.95)';
          card.style.pointerEvents = 'none';
          card.style.display = 'none';
          card.hidden = true;
        }
      });
    });
  });
})();

// ─────────────────────────────────────────
// BLOG CATEGORY FILTER
// ─────────────────────────────────────────
(function initBlogFilter() {
  const catBtns = document.querySelectorAll('.hub-cat');
  const posts   = document.querySelectorAll('.blog-card');

  if (!catBtns.length) return;

  catBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      catBtns.forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
      });

      btn.classList.add('active');
      btn.setAttribute('aria-selected', 'true');

      const cat = btn.dataset.cat;

      posts.forEach(post => {
        const postCat = post.dataset.cat || '';
        const visible = cat === 'all' || postCat === cat;

        post.style.transition = 'opacity 300ms ease, transform 300ms ease';

        if (visible) {
          post.style.opacity = '1';
          post.style.transform = '';
          post.style.pointerEvents = '';
        } else {
          post.style.opacity  = '0.2';
          post.style.transform = 'scale(0.98)';
          post.style.pointerEvents = 'none';
        }
      });
    });
  });
})();

// ─────────────────────────────────────────
// FEEDBACK FORM — Validation & Submit
// ─────────────────────────────────────────
(function initFeedbackForm() {
  const form        = document.getElementById('feedback-form');
  const successMsg  = document.getElementById('form-success');
  const submitBtn   = document.getElementById('submit-btn');
  const textarea    = document.getElementById('f-message');
  const charCount   = document.getElementById('f-char-count');
  const MAX_CHARS   = 500;

  if (!form) return;

  // Character counter
  textarea?.addEventListener('input', () => {
    const len = textarea.value.length;
    const over = len > MAX_CHARS;
    if (charCount) {
      charCount.textContent = `${len} / ${MAX_CHARS}`;
      charCount.style.color = over ? '#ef4444' : '';
    }
  });

  // Real-time validation
  const fields = {
    'f-name':    { test: v => v.trim().length >= 2,        msg: 'Please enter your full name.' },
    'f-email':   { test: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), msg: 'Please enter a valid email address.' },
    'f-message': { test: v => v.trim().length >= 10,       msg: 'Please write at least 10 characters.' },
  };

  Object.entries(fields).forEach(([id, { test, msg }]) => {
    const input = document.getElementById(id);
    const error = document.getElementById(`${id}-error`);
    if (!input || !error) return;

    const validate = () => {
      const valid = test(input.value);
      error.textContent = valid ? '' : msg;
      input.classList.toggle('error', !valid && input.value !== '');
      return valid;
    };

    input.addEventListener('blur', validate);
    input.addEventListener('input', () => {
      if (input.classList.contains('error')) validate();
    });
  });

  // Submit
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    let allValid = true;
    Object.entries(fields).forEach(([id, { test, msg }]) => {
      const input = document.getElementById(id);
      const error = document.getElementById(`${id}-error`);
      if (!input || !error) return;

      if (!test(input.value)) {
        error.textContent = msg;
        input.classList.add('error');
        if (allValid) input.focus();
        allValid = false;
      }
    });

    if (!allValid) return;

    // Simulate async submit
    const btnText = submitBtn.querySelector('.btn-text');
    submitBtn.disabled = true;
    if (btnText) btnText.textContent = 'Sending…';

    setTimeout(() => {
      submitBtn.disabled = false;
      if (btnText) btnText.textContent = 'Submit Your Voice';
      form.reset();
      if (charCount) charCount.textContent = `0 / ${MAX_CHARS}`;
      successMsg?.removeAttribute('hidden');

      setTimeout(() => {
        successMsg?.setAttribute('hidden', '');
      }, 6000);
    }, 1200);
  });
})();

// ─────────────────────────────────────────
// STATS COUNTER — Animated on Scroll
// ─────────────────────────────────────────
(function initCounters() {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const easeOut = (t) => 1 - Math.pow(1 - t, 3);

  const animateCounter = (el) => {
    const target   = parseInt(el.dataset.count, 10);
    const duration = 1800;
    const start    = performance.now();

    const step = (now) => {
      const elapsed  = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const val      = Math.round(easeOut(progress) * target);

      el.textContent = val.toLocaleString();

      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString();
    };

    requestAnimationFrame(step);
  };

  if (prefersReduced) {
    document.querySelectorAll('[data-count]').forEach(el => {
      el.textContent = parseInt(el.dataset.count, 10).toLocaleString();
    });
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      animateCounter(entry.target);
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-count]').forEach(el => observer.observe(el));
})();

// ─────────────────────────────────────────
// COMMENT LIKES
// ─────────────────────────────────────────
(function initCommentLikes() {
  document.querySelectorAll('.comment-like').forEach(btn => {
    let liked = false;

    btn.addEventListener('click', () => {
      const countEl = btn.querySelector('span');
      if (!countEl) return;

      const count = parseInt(countEl.textContent, 10);
      liked = !liked;

      countEl.textContent = liked ? count + 1 : count - 1;
      btn.style.color = liked ? 'var(--c-gold)' : '';
      btn.style.background = liked ? 'var(--c-gold-muted)' : '';
      btn.setAttribute('aria-label', btn.getAttribute('aria-label')?.replace(/\d+/, liked ? count + 1 : count - 1) || '');
    });
  });
})();

// ─────────────────────────────────────────
// NEWSLETTER FORM
// ─────────────────────────────────────────
(function initNewsletter() {
  document.querySelectorAll('.newsletter-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = form.querySelector('.newsletter-input');
      const btn   = form.querySelector('.newsletter-submit');
      if (!input || !btn) return;

      if (!input.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
        input.style.borderColor = '#ef4444';
        input.focus();
        setTimeout(() => { input.style.borderColor = ''; }, 2000);
        return;
      }

      btn.textContent = '✓ Subscribed';
      btn.style.background = 'linear-gradient(135deg, #4ade80, #16a34a)';
      btn.style.color = '#0f0f0f';
      input.value = '';
      input.disabled = true;
      btn.disabled = true;

      setTimeout(() => {
        btn.textContent = 'Subscribe';
        btn.style.background = '';
        btn.style.color = '';
        input.disabled = false;
        btn.disabled = false;
      }, 5000);
    });
  });
})();

// ─────────────────────────────────────────
// PRODUCT QUICK VIEW — placeholder UX
// ─────────────────────────────────────────
(function initQuickView() {
  document.querySelectorAll('.product-quick-view').forEach(btn => {
    if (btn.tagName === 'A' && btn.getAttribute('href')) {
      return;
    }

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const card  = btn.closest('.product-card');
      const name  = card?.querySelector('.product-name')?.textContent || 'Product';
      const price = card?.querySelector('.product-price')?.textContent || '';

      // Minimal toast notification
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
        backdropFilter: 'blur(20px)',
        opacity: '0',
        transition: 'all 300ms ease',
        boxShadow: '0 8px 32px rgba(0,0,0,0.5)',
        maxWidth: '90vw',
        textAlign: 'center',
      });

      toast.innerHTML = `<span style="color:var(--c-gold)">Quick View:</span> ${name} — ${price}`;
      document.body.appendChild(toast);

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          toast.style.opacity = '1';
          toast.style.transform = 'translateX(-50%) translateY(0)';
        });
      });

      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(12px)';
        setTimeout(() => toast.remove(), 350);
      }, 3000);
    });
  });
})();
