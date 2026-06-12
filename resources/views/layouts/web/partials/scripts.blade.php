<script>
    window.__cartStoreUrl = @json(route('cart.store'));
    window.__cartUpdateUrl = @json(url('/cart/items'));
</script>
<script src="{{ asset('assets/web/js/script.js') }}"></script>
<script src="{{ asset('assets/web/js/cart.js') }}"></script>
@stack('scripts')