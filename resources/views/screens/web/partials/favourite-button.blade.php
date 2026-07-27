@php
    $productId = (int) $product->id;
    $isFavourite = $isFavourite ?? is_favourite_product($productId);
    $buttonClass = trim('favourite-btn '.($class ?? '').($isFavourite ? ' is-favourite' : ''));
@endphp
@auth
    <button
        type="button"
        class="{{ $buttonClass }}"
        data-favourite-toggle
        data-product-id="{{ $productId }}"
        data-toggle-url="{{ route('favourites.toggle', $product) }}"
        aria-pressed="{{ $isFavourite ? 'true' : 'false' }}"
        aria-label="{{ $isFavourite ? __('Remove from favourites') : __('Add to favourites') }}"
        title="{{ $isFavourite ? __('Remove from favourites') : __('Add to favourites') }}"
    >
        <svg class="favourite-btn__icon favourite-btn__icon--outline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
            <path fill="none" stroke="currentColor" stroke-width="1.8" d="M12 21s-6.7-4.35-9.33-7.6C.8 11.2.8 8.1 2.7 6.3c1.7-1.6 4.3-1.5 5.9.2L12 10l3.4-3.5c1.6-1.7 4.2-1.8 5.9-.2 1.9 1.8 1.9 4.9.03 7.1C18.7 16.65 12 21 12 21z"/>
        </svg>
        <svg class="favourite-btn__icon favourite-btn__icon--filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
            <path fill="currentColor" d="M12 21s-6.7-4.35-9.33-7.6C.8 11.2.8 8.1 2.7 6.3c1.7-1.6 4.3-1.5 5.9.2L12 10l3.4-3.5c1.6-1.7 4.2-1.8 5.9-.2 1.9 1.8 1.9 4.9.03 7.1C18.7 16.65 12 21 12 21z"/>
        </svg>
    </button>
@else
    <a
        href="{{ route('favourites.continue-login', ['redirect' => url()->current()]) }}"
        class="{{ $buttonClass }}"
        aria-label="{{ __('Add to favourites') }}"
        title="{{ __('Add to favourites') }}"
    >
        <svg class="favourite-btn__icon favourite-btn__icon--outline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
            <path fill="none" stroke="currentColor" stroke-width="1.8" d="M12 21s-6.7-4.35-9.33-7.6C.8 11.2.8 8.1 2.7 6.3c1.7-1.6 4.3-1.5 5.9.2L12 10l3.4-3.5c1.6-1.7 4.2-1.8 5.9-.2 1.9 1.8 1.9 4.9.03 7.1C18.7 16.65 12 21 12 21z"/>
        </svg>
    </a>
@endauth
