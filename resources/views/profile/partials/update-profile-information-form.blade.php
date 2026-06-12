<section class="profile-panel" id="profile-info" aria-labelledby="profile-info-title">
    <header class="profile-panel-header">
        <h2 class="profile-panel-title" id="profile-info-title">{{ __('Profile Information') }}</h2>
        <p class="profile-panel-sub">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="form-group">
            <label class="form-label" for="name">{{ __('Name') }}</label>
            <input
                id="name"
                name="name"
                type="text"
                class="form-input @error('name') error @enderror"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            />
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-input @error('email') error @enderror"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            />
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="profile-verify-notice">
                    <p>{{ __('Your email address is unverified.') }}</p>
                    <button type="submit" form="send-verification" class="profile-inline-link">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="profile-flash profile-flash--success">{{ __('A new verification link has been sent to your email address.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="profile-form-actions">
            <button type="submit" class="btn btn-gold">{{ __('Save changes') }}</button>
            @if (session('status') === 'profile-updated')
                <p class="profile-flash profile-flash--success" data-profile-flash>{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
