@php
    $faqItems = old('faqs', $faqItems ?? [['question' => '', 'answer' => '']]);
    if (! is_array($faqItems) || count($faqItems) === 0) {
        $faqItems = [['question' => '', 'answer' => '']];
    }
@endphp

<div class="col-12 mb-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <label class="form-label mb-0">{{ __('FAQs') }}</label>
        <button type="button" class="btn btn-sm btn-outline-primary" id="collection-faq-add">{{ __('Add FAQ') }}</button>
    </div>
    <div id="collection-faq-list">
        @foreach ($faqItems as $index => $faq)
            <div class="collection-faq-row">
                <div class="mb-2">
                    <label class="form-label">{{ __('Question') }}</label>
                    <input
                        type="text"
                        class="form-control"
                        data-faq-field="question"
                        name="faqs[{{ $index }}][question]"
                        value="{{ $faq['question'] ?? '' }}"
                        maxlength="255"
                        placeholder="{{ __('FAQ question') }}"
                    />
                </div>
                <div>
                    <label class="form-label">{{ __('Answer') }}</label>
                    <textarea
                        class="form-control"
                        data-faq-field="answer"
                        name="faqs[{{ $index }}][answer]"
                        rows="3"
                        placeholder="{{ __('FAQ answer') }}"
                    >{{ $faq['answer'] ?? '' }}</textarea>
                </div>
                <div class="faq-row-actions">
                    <button type="button" class="btn btn-sm btn-outline-danger js-faq-remove">{{ __('Remove') }}</button>
                </div>
            </div>
        @endforeach
    </div>
    @error('faqs')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('faqs.*.question')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('faqs.*.answer')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<template id="collection-faq-template">
    <div class="collection-faq-row">
        <div class="mb-2">
            <label class="form-label">{{ __('Question') }}</label>
            <input
                type="text"
                class="form-control"
                data-faq-field="question"
                name="faqs[__INDEX__][question]"
                value=""
                maxlength="255"
                placeholder="{{ __('FAQ question') }}"
            />
        </div>
        <div>
            <label class="form-label">{{ __('Answer') }}</label>
            <textarea
                class="form-control"
                data-faq-field="answer"
                name="faqs[__INDEX__][answer]"
                rows="3"
                placeholder="{{ __('FAQ answer') }}"
            ></textarea>
        </div>
        <div class="faq-row-actions">
            <button type="button" class="btn btn-sm btn-outline-danger js-faq-remove">{{ __('Remove') }}</button>
        </div>
    </div>
</template>
