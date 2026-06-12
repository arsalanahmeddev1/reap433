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

    const fillFromAddress = (data) => {
        if (!data) return;
        const map = {
            billing_name: data.full_name,
            billing_phone: data.phone || '',
            billing_address: [data.street_address, data.street_address_2].filter(Boolean).join(', '),
            billing_city: data.city,
            billing_state: data.state || '',
            billing_zip: data.zipcode,
            billing_country: data.country,
        };
        Object.entries(map).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.value = value || '';
        });
    };

    const selectedAddressRadio = () => form.querySelector('input[name="user_address_id"]:checked');

    const applySelectedAddress = () => {
        const radio = selectedAddressRadio();
        if (!radio) return;
        try {
            fillFromAddress(JSON.parse(radio.getAttribute('data-address')));
        } catch (e) {}
    };

    form.querySelectorAll('input[name="user_address_id"]').forEach((radio) => {
        radio.addEventListener('change', applySelectedAddress);
    });
    applySelectedAddress();

    if (cfg.stripeKey && window.Stripe) {
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

    const getFormPayload = () => {
        const data = new FormData(form);
        const payload = {};
        ['billing_name', 'billing_email', 'billing_phone', 'billing_address', 'billing_city', 'billing_state', 'billing_zip', 'billing_country'].forEach((key) => {
            payload[key] = (data.get(key) || '').toString().trim();
        });
        payload.shipping_name = payload.billing_name;
        payload.shipping_email = payload.billing_email;
        payload.shipping_phone = payload.billing_phone;
        payload.shipping_address = payload.billing_address;
        payload.shipping_city = payload.billing_city;
        payload.shipping_state = payload.billing_state;
        payload.shipping_zip = payload.billing_zip;
        payload.shipping_country = payload.billing_country;
        return payload;
    };

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const postJson = async (url, payload) => {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf(),
            },
            body: JSON.stringify(payload),
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

    const placeOrderDirect = async (billing) => {
        const json = await postJson(cfg.placeOrderUrl, billing);
        window.location.href = `${cfg.successUrl}/${json.order_id}`;
    };

    const placeOrderWithStripe = async (billing) => {
        if (!clientSecret) {
            await createPaymentIntent();
        }

        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card,
                billing_details: {
                    name: billing.billing_name,
                    email: billing.billing_email,
                    phone: billing.billing_phone,
                    address: {
                        line1: billing.billing_address,
                        city: billing.billing_city,
                        state: billing.billing_state,
                        postal_code: billing.billing_zip,
                        country: billing.billing_country.length === 2 ? billing.billing_country : 'US',
                    },
                },
            },
        });

        if (error) {
            throw new Error(error.message);
        }

        if (paymentIntent?.status !== 'succeeded') {
            throw new Error('Payment was not completed.');
        }

        const json = await postJson(cfg.completeUrl, {
            ...billing,
            payment_intent_id: paymentIntent.id,
        });

        window.location.href = `${cfg.successUrl}/${json.order_id}`;
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        showError('');

        const billing = getFormPayload();
        const originalText = payBtn.textContent;
        payBtn.disabled = true;
        payBtn.textContent = 'Processing…';

        try {
            if (cfg.stripeKey && stripe && card) {
                await placeOrderWithStripe(billing);
            } else {
                await placeOrderDirect(billing);
            }
        } catch (err) {
            showError(err.message || 'Something went wrong.');
            payBtn.disabled = false;
            payBtn.textContent = originalText;
        }
    });
})();
