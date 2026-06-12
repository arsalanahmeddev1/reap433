{{--
    Grouped variations: one card per variation, attribute rows with name, price, and image.
    New variations are added above the scrollable list of saved variations.
    @var list<array{color: string, rows: list<array{size: string, price: float|string, image_url: string, image_path: string|null}>}> $wooInitialBlocks
--}}
<div class="woo-attr-blocks" id="product-variations-section">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
        <div>
            <label class="form-label mb-0">{{ __('Variations') }} <span class="text-danger">*</span></label>
            <p class="text-muted small mb-0">{{ __('Add variations and attribute rows. Each attribute row requires an image.') }}</p>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-woo-add-color-group">
            <i class="fa-solid fa-plus pe-1"></i>{{ __('+ Add Variation') }}
        </button>
    </div>

    <div id="attr-blocks-draft" class="woo-variations-draft d-flex flex-column gap-3 mb-3"></div>

    <div class="woo-variations-scroll-box border rounded p-3">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <span class="form-label mb-0 small text-muted">{{ __('Added variations') }}</span>
        </div>
        <div id="attr-blocks-container" class="d-flex flex-column gap-3">
            @if (count($wooInitialBlocks) === 0)
                <p class="text-muted small mb-0 js-woo-empty-hint">{{ __('No variations yet. Click "Add Variation" above to get started.') }}</p>
            @endif
            @foreach ($wooInitialBlocks as $bi => $block)
                <div class="card js-woo-attr-block shadow-none border" data-woo-block>
                    <div class="card-header card-no-border pb-0 d-flex flex-wrap justify-content-end align-items-center gap-2">
                        <button type="button" class="btn btn-link text-danger btn-sm p-0 js-woo-remove-block">{{ __('Remove Variation') }}</button>
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-3">
                            <label class="form-label" for="variation-name-{{ $bi }}">{{ __('Variation name') }}</label>
                            <input
                                type="text"
                                class="form-control js-woo-color"
                                id="variation-name-{{ $bi }}"
                                name="attr_blocks[{{ $bi }}][color]"
                                value="{{ old('attr_blocks.'.$bi.'.color', $block['color'] ?? '') }}"
                                placeholder="{{ __('e.g. Black') }}"
                            />
                        </div>

                        <label class="form-label mb-2 d-block">{{ __('Variation Attributes') }}</label>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr class="c-o-light f-w-600 small">
                                        <th style="width: 20%">{{ __('name') }}</th>
                                        <th style="width: 20%">{{ __('price') }}</th>
                                        <th>{{ __('image') }} <span class="text-danger">*</span></th>
                                        <th class="text-end" style="width: 72px"></th>
                                    </tr>
                                </thead>
                                <tbody class="js-woo-rows-tbody">
                                    @foreach ($block['rows'] ?? [] as $ri => $row)
                                        <tr class="js-woo-row">
                                            <td>
                                                <input
                                                    type="text"
                                                    class="form-control js-woo-size"
                                                    name="attr_blocks[{{ $bi }}][rows][{{ $ri }}][size]"
                                                    value="{{ old('attr_blocks.'.$bi.'.rows.'.$ri.'.size', $row['size'] ?? '') }}"
                                                    placeholder="{{ __('e.g. M') }}"
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="form-control js-woo-price"
                                                    name="attr_blocks[{{ $bi }}][rows][{{ $ri }}][price]"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00"
                                                    value="{{ old('attr_blocks.'.$bi.'.rows.'.$ri.'.price', $row['price'] ?? '') }}"
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="file"
                                                    class="form-control js-woo-image"
                                                    name="attr_blocks[{{ $bi }}][rows][{{ $ri }}][image]"
                                                    accept="image/*"
                                                    @if (empty($row['image_url'])) required @endif
                                                />
                                                @if (! empty($row['image_url']))
                                                    <input type="hidden" class="js-woo-has-existing-image" name="attr_blocks[{{ $bi }}][rows][{{ $ri }}][has_existing_image]" value="1" />
                                                    <small class="text-muted d-block mt-1">{{ __('Current:') }}</small>
                                                    <img src="{{ $row['image_url'] }}" alt="" class="img-thumbnail mt-1 js-woo-existing-image-preview" style="height: 72px; width: 72px;" loading="lazy">
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-link text-danger btn-sm p-0 js-woo-remove-row">{{ __('Remove') }}</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary js-woo-add-size mt-2">
                            <i class="fa-solid fa-plus pe-1"></i>{{ __('+ Add attribute') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<template id="woo-tpl-row">
    <tr class="js-woo-row">
        <td>
            <input type="text" class="form-control js-woo-size" placeholder="{{ __('e.g. M') }}" />
        </td>
        <td>
            <input type="number" class="form-control js-woo-price" step="0.01" min="0" placeholder="0.00" />
        </td>
        <td>
            <input type="file" class="form-control js-woo-image" accept="image/*" required />
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-link text-danger btn-sm p-0 js-woo-remove-row">{{ __('Remove') }}</button>
        </td>
    </tr>
</template>

<template id="woo-tpl-block">
    <div class="card js-woo-attr-block shadow-none border js-woo-draft-block" data-woo-block>
        <div class="card-header card-no-border pb-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="small text-muted">{{ __('New variation') }}</span>
            <button type="button" class="btn btn-link text-danger btn-sm p-0 js-woo-remove-block">{{ __('Remove Variation') }}</button>
        </div>
        <div class="card-body pt-3">
            <div class="mb-3">
                <label class="form-label">{{ __('Variation name') }}</label>
                <input type="text" class="form-control js-woo-color" placeholder="{{ __('e.g. Black') }}" />
            </div>
            <label class="form-label mb-2 d-block">{{ __('Variation Attributes') }}</label>
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr class="c-o-light f-w-600 small">
                            <th style="width: 20%">{{ __('name') }}</th>
                            <th style="width: 20%">{{ __('price') }}</th>
                            <th>{{ __('image') }} <span class="text-danger">*</span></th>
                            <th class="text-end" style="width: 72px"></th>
                        </tr>
                    </thead>
                    <tbody class="js-woo-rows-tbody"></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary js-woo-add-size mt-2">
                <i class="fa-solid fa-plus pe-1"></i>{{ __('+ Add attribute') }}
            </button>
        </div>
    </div>
</template>
