@extends('layouts.web.master')

@section('title', 'Customize · '.$product->name)

@section('content')
<main id="main" class="product-customizer-page">
    <section class="section-pad">
        <div class="container">
            <a href="{{ route('printful-products.show', $product) }}" class="printful-product-back">&larr; Back to product</a>

            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">Product Customizer</span>
                <h1 class="section-title">{{ $product->name }}</h1>
                <p class="section-sub">Choose color and size, add your design or text, then add the customized item to your cart.</p>
            </div>

            @if (session('error'))
                <div class="checkout-flash checkout-flash--error" role="alert">{{ session('error') }}</div>
            @endif

            <div
                id="product-customizer"
                class="product-customizer"
                data-product-id="{{ $product->id }}"
                data-options='@json($options)'
                data-save-url="{{ route('printful-products.customize.store', $product) }}"
                data-fee="{{ $customizationFee }}"
                data-csrf="{{ csrf_token() }}"
            >
                <div class="product-customizer__preview">
                    <div class="product-customizer__canvas-wrap">
                        <img id="pc-product-image" class="product-customizer__product-image" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}">
                        <div class="product-customizer__print-area" id="pc-print-area" aria-hidden="true"></div>
                        <canvas id="pc-canvas" width="500" height="560"></canvas>
                    </div>
                    <p id="pc-status" class="product-customizer__status" role="status"></p>
                </div>

                <div class="product-customizer__controls">
                    <div class="product-customizer__panel">
                        <h2>Color</h2>
                        <div id="pc-colors" class="product-customizer__swatches" role="listbox" aria-label="Colors"></div>
                    </div>

                    <div class="product-customizer__panel">
                        <h2>Size</h2>
                        <div id="pc-sizes" class="product-customizer__sizes" role="listbox" aria-label="Sizes"></div>
                    </div>

                    <div class="product-customizer__panel">
                        <h2>Placement</h2>
                        <select id="pc-placement" class="product-customizer__select" aria-label="Print placement"></select>
                    </div>

                    <div class="product-customizer__panel">
                        <h2>Upload design</h2>
                        <input type="file" id="pc-upload" accept="image/png,image/jpeg,image/jpg">
                        <p class="product-customizer__hint">PNG or JPG, min 100×100px, max 10MB.</p>
                    </div>

                    <div class="product-customizer__panel">
                        <h2>Add text</h2>
                        <input type="text" id="pc-text" class="product-customizer__input" maxlength="80" placeholder="Your text">
                        <div class="product-customizer__row">
                            <label>Font
                                <select id="pc-font" class="product-customizer__select">
                                    <option value="Arial">Arial</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                            </label>
                            <label>Size
                                <input type="number" id="pc-font-size" class="product-customizer__input" value="28" min="10" max="120">
                            </label>
                            <label>Color
                                <input type="color" id="pc-font-color" value="#111111">
                            </label>
                        </div>
                        <label>Align
                            <select id="pc-text-align" class="product-customizer__select">
                                <option value="left">Left</option>
                                <option value="center" selected>Center</option>
                                <option value="right">Right</option>
                            </select>
                        </label>
                        <button type="button" id="pc-add-text" class="btn btn-outline-sm">Add text</button>
                    </div>

                    <div class="product-customizer__panel product-customizer__toolbar">
                        <h2>Canvas tools</h2>
                        <div class="product-customizer__tool-row">
                            <button type="button" data-action="bringForward" class="btn btn-outline-sm">Forward</button>
                            <button type="button" data-action="sendBackward" class="btn btn-outline-sm">Backward</button>
                            <button type="button" data-action="duplicate" class="btn btn-outline-sm">Duplicate</button>
                            <button type="button" data-action="delete" class="btn btn-outline-sm">Delete</button>
                            <button type="button" data-action="undo" class="btn btn-outline-sm">Undo</button>
                            <button type="button" data-action="redo" class="btn btn-outline-sm">Redo</button>
                            <button type="button" data-action="reset" class="btn btn-outline-sm">Reset</button>
                        </div>
                    </div>

                    <div class="product-customizer__actions">
                        <button type="button" id="pc-save-preview" class="btn btn-outline-sm">Generate Final Preview</button>
                        <button type="button" id="pc-add-cart" class="btn btn-gold" disabled>Add customized to cart</button>
                        @if ($customizationFee > 0)
                            <p class="product-customizer__hint">Customization fee: ${{ number_format($customizationFee, 2) }} (added at checkout price)</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
.product-customizer {
    display: grid;
    grid-template-columns: minmax(280px, 1.1fr) minmax(280px, 0.9fr);
    gap: var(--space-xl);
    align-items: start;
}
.product-customizer__canvas-wrap {
    position: relative;
    background: var(--c-black-soft);
    border: 1px solid var(--c-black-border);
    border-radius: var(--radius-md);
    overflow: hidden;
    width: 500px;
    max-width: 100%;
    height: 560px;
    max-height: 70vh;
    touch-action: none;
    margin: 0 auto;
}
.product-customizer__product-image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
    z-index: 1;
    user-select: none;
}
.product-customizer__print-area {
    position: absolute;
    border: 1px dashed rgba(201, 162, 39, 0.85);
    box-shadow: inset 0 0 0 9999px rgba(0,0,0,0.08);
    pointer-events: none;
    z-index: 2;
}
.product-customizer__fabric,
.product-customizer__canvas-wrap .canvas-container {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    z-index: 3 !important;
    touch-action: none;
}
.product-customizer__canvas-wrap .upper-canvas {
    z-index: 4 !important;
    touch-action: none;
}
#pc-canvas,
.product-customizer__canvas-wrap .lower-canvas {
    display: block;
    background: transparent !important;
}
.product-customizer__panel {
    background: var(--c-black-soft);
    border: 1px solid var(--c-black-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    margin-bottom: var(--space-md);
}
.product-customizer__panel h2 {
    font-size: 0.95rem;
    margin: 0 0 0.75rem;
}
.product-customizer__swatches,
.product-customizer__sizes,
.product-customizer__tool-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.product-customizer__swatch {
    width: 28px;
    height: 28px;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    cursor: pointer;
    flex: 0 0 auto;
    background-repeat: no-repeat;
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}
.product-customizer__swatch.is-light {
    border-color: rgba(255, 255, 255, 0.45);
}
.product-customizer__size {
    border: 1px solid var(--c-black-border);
    background: var(--c-black-mid);
    color: var(--c-text-primary);
    padding: 0.4rem 0.7rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
}
.product-customizer__swatch.is-active,
.product-customizer__swatch:hover {
    box-shadow: 0 0 0 2px var(--c-gold);
    transform: translateY(-1px);
}
.product-customizer__size.is-active {
    border-color: var(--c-gold);
    box-shadow: 0 0 0 1px var(--c-gold);
}
.product-customizer__size:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
#pc-color-label {
    margin: 0.5rem 0 0;
}
.product-customizer__input,
.product-customizer__select {
    width: 100%;
    margin-top: 0.35rem;
    margin-bottom: 0.5rem;
    padding: 0.5rem 0.65rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--c-black-border);
    background: var(--c-black-mid);
    color: var(--c-text-primary);
}
.product-customizer__row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.product-customizer__hint,
.product-customizer__status {
    font-size: 0.8125rem;
    color: var(--c-text-secondary);
}
.product-customizer__actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
@media (max-width: 900px) {
    .product-customizer {
        grid-template-columns: 1fr;
    }
    .product-customizer__row {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fabric@5.3.0/dist/fabric.min.js"></script>
<script src="{{ asset('assets/web/js/product-customizer.js') }}"></script>
@endpush
