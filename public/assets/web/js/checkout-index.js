(function () {
    const cfg = window.__checkout;
    const form = document.getElementById('checkout-form');
    if (!form || !cfg) return;

    const cardErrors = document.getElementById('card-errors');
    const formError = document.getElementById('checkout-form-error');
    const payBtn = form.querySelector('.checkout-pay-btn');
    let stripe = null;
    let card = null;
    let clientSecret = null;

    const showError = (msg) => {
        if (!formError) return;
        formError.textContent = msg;
        formError.hidden = !msg;
    };

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (cfg.stripeEnabled && cfg.stripeKey && window.Stripe) {
        stripe = Stripe(cfg.stripeKey);
        const elements = stripe.elements();
        card = elements.create('card', {
            style: {
                base: {
                    color: '#F8F4ED',
                    fontSize: '16px',
                    fontFamily: '"Inter", system-ui, sans-serif',
                    '::placeholder': { color: 'rgba(248, 244, 237, 0.35)' },
                },
                invalid: { color: '#f87171' },
            },
        });
        const cardMount = document.getElementById('card-element');
        if (cardMount) {
            card.mount('#card-element');
            card.on('change', (event) => {
                if (cardErrors) cardErrors.textContent = event.error ? event.error.message : '';
            });
        }
    }

    const postJson = async (url, payload) => {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf(),
            },
            body: JSON.stringify(payload || {}),
        });
        const json = await res.json();
        if (!res.ok) {
            const message = json.message || json.error || Object.values(json.errors || {}).flat().join(' ') || 'Request failed.';
            throw new Error(message);
        }
        return json;
    };

    const createPaymentIntent = async () => {
        const json = await postJson(cfg.paymentIntentUrl, {});
        clientSecret = json.clientSecret;
        return json;
    };

    const confirmCardPayment = async () => {
        if (!clientSecret) {
            await createPaymentIntent();
        }

        const billingDetails = {
            name: form.querySelector('[name="full_name"]')?.value || '',
            email: form.querySelector('[name="email"]')?.value || '',
            phone: form.querySelector('[name="phone"]')?.value || undefined,
            address: {
                line1: form.querySelector('[name="address1"]')?.value || '',
                line2: form.querySelector('[name="address2"]')?.value || undefined,
                city: form.querySelector('[name="city"]')?.value || '',
                state: (form.querySelector('[name="state_code"]')?.value || '').slice(0, 2).toUpperCase(),
                postal_code: form.querySelector('[name="zip"]')?.value || '',
                country: 'US',
            },
        };

        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card,
                billing_details: billingDetails,
            },
        });

        if (error) {
            throw new Error(error.message);
        }

        if (paymentIntent?.status !== 'succeeded') {
            throw new Error('Payment was not completed.');
        }

        return paymentIntent.id;
    };

    form.addEventListener('submit', async (e) => {
        if (form.querySelector('[name="payment_intent_id"]')?.value) {
            return;
        }

        if (!cfg.stripeEnabled || !stripe || !card) {
            return;
        }

        e.preventDefault();
        showError('');

        if (!form.reportValidity()) {
            return;
        }

        const originalText = payBtn.textContent;
        payBtn.disabled = true;
        payBtn.textContent = 'Processing…';

        try {
            const paymentIntentId = await confirmCardPayment();
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'payment_intent_id';
            hidden.value = paymentIntentId;
            form.appendChild(hidden);
            form.submit();
        } catch (err) {
            showError(err.message || 'Something went wrong.');
            payBtn.disabled = false;
            payBtn.textContent = originalText;
        }
    });
})();
