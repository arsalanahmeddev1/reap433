{{--
    WooCommerce-style variable product UI with dynamic attributes.
    @var array{attributes: list<array{name: string, values: list<string>}>, variations: list<array{options: array<string, string>, price: mixed, image_url: string, has_existing_image: bool}>} $wooInitialPayload
--}}
@php
    $wooInitialPayload = $wooInitialPayload ?? ['attributes' => [], 'variations' => []];
@endphp
<div
    class="woo-product-variations"
    id="product-variations-section"
    data-initial-payload='@json($wooInitialPayload)'
>
    {{-- Attributes (WooCommerce) --}}
    <div class="card shadow-none border mb-3 woo-panel">
        <div class="card-header card-no-border d-flex flex-wrap justify-content-between align-items-center gap-2 pb-2">
            <div>
                <h6 class="mb-1 f-w-600">{{ __('Attributes') }}</h6>
                <p class="text-muted small mb-0">{{ __('Add attributes (e.g. Color, Size, Material), enter values, then generate variations.') }}</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-woo-add-attribute">
                <i class="fa-solid fa-plus pe-1"></i>{{ __('Add attribute') }}
            </button>
        </div>
        <div class="card-body pt-2">
            <div id="woo-attributes-list" class="d-flex flex-column gap-3"></div>
            <p class="text-muted small mb-0 mt-3 js-woo-attr-empty-hint">{{ __('No attributes yet. Click "Add attribute" to get started.') }}</p>
        </div>
    </div>

    {{-- Variations (WooCommerce) --}}
    <div class="card shadow-none border woo-panel">
        <div class="card-header card-no-border d-flex flex-wrap justify-content-between align-items-center gap-2 pb-2">
            <div>
                <h6 class="mb-1 f-w-600">{{ __('Variations') }} <span class="text-danger">*</span></h6>
                <p class="text-muted small mb-0">{{ __('Each row is one combination of attribute values with its own price and image.') }}</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-woo-generate-variations">
                <i class="fa-solid fa-wand-magic-sparkles pe-1"></i>{{ __('Generate variations') }}
            </button>
        </div>
        <div class="card-body pt-2">
            <div class="table-responsive woo-variations-table-wrap">
                <table class="table table-hover align-middle mb-0" id="woo-variations-table">
                    <thead>
                        <tr class="c-o-light f-w-600 small" id="woo-variations-thead-row">
                            <th class="js-woo-th-price" style="width: 130px">{{ __('Price') }}</th>
                            <th class="js-woo-th-fixed">{{ __('Image') }} <span class="text-danger">*</span></th>
                            <th class="js-woo-th-fixed text-end" style="width: 72px"></th>
                        </tr>
                    </thead>
                    <tbody id="woo-variations-tbody"></tbody>
                </table>
            </div>
            <p class="text-muted small mb-0 mt-3 js-woo-empty-hint">{{ __('Add attributes and values above, then click "Generate variations".') }}</p>
        </div>
    </div>

    <div id="attr-blocks-draft" class="d-none" aria-hidden="true"></div>
    <div id="attr-blocks-container" class="d-none" aria-hidden="true"></div>
    <div id="woo-form-sync" class="d-none" aria-hidden="true"></div>
</div>

<template id="woo-tpl-attribute">
    <div class="woo-attr-definition border rounded p-3 js-woo-attr-block">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
            <div class="flex-grow-1" style="min-width: 200px;">
                <label class="form-label f-w-500 small mb-1">{{ __('Attribute name') }}</label>
                <input
                    type="text"
                    class="form-control form-control-sm js-woo-attr-name"
                    placeholder="{{ __('e.g. Color, Size, Material') }}"
                    maxlength="255"
                />
            </div>
            <button type="button" class="btn btn-link text-danger btn-sm p-0 js-woo-remove-attribute">{{ __('Remove attribute') }}</button>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="text-muted small">{{ __('Used for variations') }}</span>
        </div>
        <div class="woo-tags-wrap">
            <div class="woo-tags-list js-woo-tags"></div>
            <input
                type="text"
                class="form-control form-control-sm woo-tag-input js-woo-tag-input"
                placeholder="{{ __('Add value — press Enter') }}"
                autocomplete="off"
            />
        </div>
    </div>
</template>

<template id="woo-tpl-variation-row">
    <tr class="js-woo-variation-row">
        <td>
            <input type="number" class="form-control form-control-sm js-woo-price" step="0.01" min="0" placeholder="0.00" />
        </td>
        <td>
            <input type="file" class="form-control form-control-sm js-woo-image" accept="image/*" />
            <div class="js-woo-existing-image-wrap d-none mt-1">
                <input type="hidden" class="js-woo-has-existing-image" value="1" />
                <small class="text-muted d-block">{{ __('Current:') }}</small>
                <img src="" alt="" class="img-thumbnail js-woo-existing-image-preview" style="height: 48px; width: 48px;" loading="lazy" />
            </div>
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-link text-danger btn-sm p-0 js-woo-remove-variation">{{ __('Remove') }}</button>
        </td>
    </tr>
</template>

<template id="woo-tpl-tag">
    <span class="woo-tag">
        <span class="woo-tag-text"></span>
        <button type="button" class="woo-tag-remove" aria-label="{{ __('Remove') }}">&times;</button>
    </span>
</template>
