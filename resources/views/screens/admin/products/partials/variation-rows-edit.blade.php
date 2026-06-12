{{-- One row = one SKU: every attribute (Size, Color, …) + price + optional image --}}
@foreach ($product->variations as $index => $variation)
    <div class="variation-option-row border rounded p-3 mb-3 js-variation-row" data-index="{{ $index }}">
        <div class="row align-items-end">
            @foreach ($variationAttributes as $attr)
                @php
                    $val = $variation->values->firstWhere('product_attribute_id', $attr->id)?->value ?? '';
                @endphp
                <div class="col-xl-2 col-md-6 mb-3">
                    <label class="form-label">{{ $attr->name }} <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        name="variation_rows[{{ $index }}][options][{{ $attr->id }}]"
                        value="{{ old('variation_rows.'.$index.'.options.'.$attr->id, $val) }}"
                        placeholder="{{ __('e.g. :sample', ['sample' => $attr->name === 'Color' ? 'Red' : 'Small']) }}"
                        required
                    />
                </div>
            @endforeach
            <div class="col-xl-2 col-md-6 mb-3">
                <label class="form-label">{{ __('Price') }} <span class="text-danger">*</span></label>
                <input
                    type="number"
                    class="form-control"
                    name="variation_rows[{{ $index }}][price]"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    value="{{ old('variation_rows.'.$index.'.price', $variation->price) }}"
                    required
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <label class="form-label">{{ __('Variant image') }}</label>
                <input type="file" class="form-control" name="variation_rows[{{ $index }}][image]" accept="image/*" />
                @if ($variation->image)
                    @php $optSrc = $variation->image->publicUrl(); @endphp
                    @if ($optSrc !== '')
                        <small class="text-muted d-block mt-1">{{ __('Current:') }}</small>
                        <img src="{{ $optSrc }}" alt="" class="img-thumbnail mt-1" style="height: 100px; width: 100px;" loading="lazy">
                    @endif
                @endif
            </div>
            <div class="col-xl-1 col-md-12 mb-3 text-xl-end">
                <button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-variation-row">{{ __('Remove') }}</button>
            </div>
        </div>
    </div>
@endforeach
