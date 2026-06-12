<div class="mb-6">
    <label class="manrope-font mb-2 block text-sm font-medium text-[var(--text-color)]" for="card-element">{{ __('Card details') }}</label>
    <div
        id="card-element"
        class="min-h-[52px] w-full rounded-lg border border-neutral-300 bg-white px-3 py-3 transition focus-within:border-[var(--primary-color)] focus-within:ring-2 focus-within:ring-[var(--primary-color)]/25"
    ></div>
    <div id="card-errors" class="manrope-font mt-2 text-sm text-red-600"></div>
</div>

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe("{{ config('services.stripe.key') ?? config('services.stripe.public') }}");
        var elements = stripe.elements();

        var card = elements.create("card", {
            style: {
                base: {
                    color: "#1a1a1a",
                    fontSize: "16px",
                    fontFamily: '"Manrope", ui-sans-serif, system-ui, sans-serif',
                    "::placeholder": {
                        color: "#9ca3af",
                    },
                },
                invalid: {
                    color: "#dc2626",
                },
            },
        });

        card.mount("#card-element");
    </script>
@endpush
