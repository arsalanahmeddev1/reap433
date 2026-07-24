@extends('layouts.web.auth')
@section('title', __('Become a Whole Seller'))

@section('content')
    <p class="auth-eyebrow">{{ __('Join REAP433') }}</p>
    <h1 class="auth-title">{{ __('Become a Whole Seller') }}</h1>
    <p class="auth-sub">{{ __('Submit your business details. Your account will wait for admin approval.') }}</p>

    <form class="auth-form auth-form--wholesaler" method="POST" action="{{ route('register.wholesaler.store') }}" autocomplete="off" novalidate>
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="business_name">{{ __('Business name') }}</label>
            <input
                id="business_name"
                class="auth-input @error('business_name') is-invalid @enderror"
                type="text"
                name="business_name"
                value="{{ old('business_name') }}"
                required
                autofocus
                maxlength="255"
                placeholder="Your business name"
            />
            @error('business_name')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="business_phone">{{ __('Business phone') }}</label>
            <input
                id="business_phone"
                class="auth-input @error('business_phone') is-invalid @enderror"
                type="tel"
                name="business_phone"
                value="{{ old('business_phone') }}"
                required
                inputmode="numeric"
                maxlength="14"
                placeholder="(555) 123-4567"
            />
            <p class="auth-hint">{{ __('US format: (555) 123-4567') }}</p>
            @error('business_phone')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="business_email">{{ __('Business email') }}</label>
            <input
                id="business_email"
                class="auth-input @error('business_email') is-invalid @enderror"
                type="email"
                name="business_email"
                value="{{ old('business_email') }}"
                required
                maxlength="255"
                placeholder="business@example.com"
            />
            @error('business_email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="business_location">{{ __('Business location') }}</label>
            <input
                id="business_location"
                class="auth-input @error('business_location') is-invalid @enderror"
                type="text"
                name="business_location"
                value="{{ old('business_location') }}"
                required
                maxlength="255"
                placeholder="City, state / address"
            />
            @error('business_location')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field auth-field--full">
            <label class="auth-label" for="business_description">{{ __('Business description') }}</label>
            <textarea
                id="business_description"
                class="auth-input auth-textarea @error('business_description') is-invalid @enderror"
                name="business_description"
                rows="4"
                required
                maxlength="5000"
                placeholder="Tell us about your business"
            >{{ old('business_description') }}</textarea>
            @error('business_description')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="name">{{ __('Contact name') }}</label>
            <input
                id="name"
                class="auth-input @error('name') is-invalid @enderror"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autocomplete="name"
                maxlength="255"
                placeholder="Your name"
            />
            @error('name')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Login email') }}</label>
            <input
                id="email"
                class="auth-input @error('email') is-invalid @enderror"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                maxlength="255"
                placeholder="you@example.com"
            />
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">{{ __('Password') }}</label>
            <div class="auth-password-wrap">
                <input
                    id="password"
                    class="auth-input @error('password') is-invalid @enderror"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    minlength="8"
                    placeholder="********"
                />
                <button type="button" class="auth-toggle-password" data-toggle-password="#password">Show</button>
            </div>
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <div class="auth-password-wrap">
                <input
                    id="password_confirmation"
                    class="auth-input"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    minlength="8"
                    placeholder="********"
                />
                <button type="button" class="auth-toggle-password" data-toggle-password="#password_confirmation">Show</button>
            </div>
        </div>

        <div class="auth-field auth-field--full">
            <button type="submit" class="btn btn-gold auth-submit">{{ __('Submit for approval') }}</button>
        </div>
    </form>

    <p class="auth-switch">
        <a href="{{ route('register') }}">{{ __('Back to options') }}</a>
        ·
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
    </p>
@endsection

@push('styles')
<style>
    .auth-page:has(.auth-form--wholesaler) .auth-card {
        max-width: 760px;
    }
    .auth-form--wholesaler {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem 1.15rem;
        align-items: start;
    }
    .auth-form--wholesaler .auth-field--full {
        grid-column: 1 / -1;
    }
    .auth-form--wholesaler .auth-submit {
        width: 100%;
        margin: 0.25rem 0 0;
    }
    .auth-form--wholesaler .auth-textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
    }
    .auth-form--wholesaler .auth-hint {
        margin: 0;
        font-size: 12px;
        color: var(--c-text-muted, #8a8580);
    }
    .auth-form--wholesaler .auth-error {
        margin: 0;
    }
    @media (max-width: 700px) {
        .auth-page:has(.auth-form--wholesaler) .auth-card {
            max-width: 440px;
        }
        .auth-form--wholesaler {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/libs/js/cleave/cleave.min.js') }}"></script>
<script>
    (function () {
        var phoneInput = document.getElementById('business_phone');
        if (!phoneInput || typeof Cleave === 'undefined') return;

        new Cleave(phoneInput, {
            delimiters: ['(', ') ', '-'],
            blocks: [0, 3, 3, 4],
            numericOnly: true
        });

        var form = phoneInput.closest('form');
        if (!form) return;

        form.addEventListener('submit', function (event) {
            var value = (phoneInput.value || '').trim();
            var usPhone = /^\(\d{3}\) \d{3}-\d{4}$/;
            if (!usPhone.test(value)) {
                event.preventDefault();
                phoneInput.classList.add('is-invalid');
                phoneInput.focus();
                var hint = phoneInput.parentElement.querySelector('.auth-hint');
                if (hint) {
                    hint.textContent = 'Enter a valid US phone: (555) 123-4567';
                    hint.style.color = '#ef4444';
                }
            }
        });
    })();
</script>
@endpush
