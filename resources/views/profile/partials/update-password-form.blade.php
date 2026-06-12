<section class="profile-panel" id="profile-password" aria-labelledby="profile-password-title">
    <header class="profile-panel-header">
        <h2 class="profile-panel-title" id="profile-password-title">{{ __('Update Password') }}</h2>
        <p class="profile-panel-sub">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="form-group">
            <label class="form-label" for="update_password_current_password">{{ __('Current Password') }}</label>
            <div class="profile-password-wrap">
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="form-input @if ($errors->updatePassword->has('current_password')) error @endif"
                    autocomplete="current-password"
                />
                <button type="button" class="profile-toggle-password" data-toggle-password="#update_password_current_password">Show</button>
            </div>
            @if ($errors->updatePassword->has('current_password'))
                <p class="form-error">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="update_password_password">{{ __('New Password') }}</label>
            <div class="profile-password-wrap">
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="form-input @if ($errors->updatePassword->has('password')) error @endif"
                    autocomplete="new-password"
                />
                <button type="button" class="profile-toggle-password" data-toggle-password="#update_password_password">Show</button>
            </div>
            @if ($errors->updatePassword->has('password'))
                <p class="form-error">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <div class="profile-password-wrap">
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="form-input @if ($errors->updatePassword->has('password_confirmation')) error @endif"
                    autocomplete="new-password"
                />
                <button type="button" class="profile-toggle-password" data-toggle-password="#update_password_password_confirmation">Show</button>
            </div>
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="form-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="profile-form-actions">
            <button type="submit" class="btn btn-gold">{{ __('Update password') }}</button>
            @if (session('status') === 'password-updated')
                <p class="profile-flash profile-flash--success" data-profile-flash>{{ __('Password updated.') }}</p>
            @endif
        </div>
    </form>
</section>
