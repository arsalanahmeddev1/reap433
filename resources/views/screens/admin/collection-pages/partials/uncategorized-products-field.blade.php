@php
    $selectedUncategorizedProductIds = collect(old('uncategorized_products', $selectedUncategorizedProductIds ?? []))
        ->map(fn ($id) => (string) $id);
@endphp

<div class="col-md-6 mb-3">
    <label class="form-label">{{ __('No category products') }}</label>
    <select
        name="uncategorized_products[]"
        class="form-select js-collection-uncategorized-products @error('uncategorized_products') is-invalid @enderror @error('uncategorized_products.*') is-invalid @enderror"
        multiple
        data-placeholder="{{ __('Select products with no category') }}"
    >
        @forelse ($uncategorizedProducts as $product)
            <option value="{{ $product->id }}" @selected($selectedUncategorizedProductIds->contains((string) $product->id))>
                {{ $product->name }}
            </option>
        @empty
            <option value="" disabled>{{ __('No uncategorized products found') }}</option>
        @endforelse
    </select>
    <small class="text-muted d-block mt-1">
        {{ __('Optional. Selected products without a category will also show on this collection page.') }}
    </small>
    @error('uncategorized_products')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('uncategorized_products.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
