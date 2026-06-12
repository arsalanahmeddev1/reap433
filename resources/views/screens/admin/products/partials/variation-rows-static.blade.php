{{-- Read-only SKU rows; expects $variations with values + image --}}
@foreach ($variations as $variation)
    <div class="variation-option-row border rounded p-3 mb-3 js-variation-row">
        <div class="row align-items-end">
            @foreach ($variation->values->sortBy('product_attribute_id') as $vv)
                <div class="col-xl-2 col-md-6 mb-3">
                    <label class="form-label">{{ $vv->productAttribute?->name ?? '—' }}</label>
                    <input type="text" class="form-control" value="{{ $vv->value }}" disabled />
                </div>
            @endforeach
            <div class="col-xl-2 col-md-6 mb-3">
                <label class="form-label">{{ __('Price') }}</label>
                <input type="text" class="form-control" value="{{ number_format((float) $variation->price, 2) }}" disabled />
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <label class="form-label">{{ __('Variant image') }}</label>
                @if ($variation->image)
                    @php $vSrc = $variation->image->publicUrl(); @endphp
                    @if ($vSrc !== '')
                        <div>
                            <a href="{{ $vSrc }}" target="_blank" rel="noopener">
                                <img src="{{ $vSrc }}" alt="" class="img-thumbnail mt-1" style="max-height: 80px;">
                            </a>
                        </div>
                    @else
                        <p class="text-muted small mb-0">—</p>
                    @endif
                @else
                    <p class="text-muted small mb-0">—</p>
                @endif
            </div>
        </div>
    </div>
@endforeach
