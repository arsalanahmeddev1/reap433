@php
    $selectedCategoryIds = collect(old('categories', $selectedCategoryIds ?? []))->map(fn ($id) => (string) $id);
    $faqItems = old('faqs', $faqItems ?? [['question' => '', 'answer' => '']]);
    if (! is_array($faqItems) || count($faqItems) === 0) {
        $faqItems = [['question' => '', 'answer' => '']];
    }
@endphp

<div class="col-md-6 mb-3">
    <label class="form-label">{{ __('Categories') }} <span class="text-danger">*</span></label>
    <select
        name="categories[]"
        class="form-select js-collection-categories @error('categories') is-invalid @enderror @error('categories.*') is-invalid @enderror"
        multiple
        required
        data-placeholder="{{ __('Select categories') }}"
    >
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected($selectedCategoryIds->contains((string) $cat->id))>{{ $cat->name }}</option>
        @endforeach
    </select>
    @error('categories')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('categories.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
