<section class="profile-panel profile-panel--danger" id="profile-delete" aria-labelledby="profile-delete-title">
    <header class="profile-panel-header">
        <h2 class="profile-panel-title" id="profile-delete-title">{{ __('Delete Account') }}</h2>
        <p class="profile-panel-sub">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="btn btn-outline profile-delete-trigger" data-open-delete-modal>
        {{ __('Delete account') }}
    </button>
</section>

<div class="profile-modal" id="profile-delete-modal" @if ($errors->userDeletion->isNotEmpty()) data-open="true" @endif hidden>
    <div class="profile-modal-backdrop" data-close-delete-modal></div>
    <div class="profile-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="profile-delete-modal-title">
        <button type="button" class="profile-modal-close" data-close-delete-modal aria-label="{{ __('Close') }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <h2 class="profile-modal-title" id="profile-delete-modal-title">{{ __('Are you sure you want to delete your account?') }}</h2>
        <p class="profile-modal-sub">
            {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>

        <form method="post" action="{{ route('profile.destroy') }}" class="profile-form">
            @csrf
            @method('delete')

            <div class="form-group">
                <label class="form-label" for="delete_password">{{ __('Password') }}</label>
                <div class="profile-password-wrap">
                    <input
                        id="delete_password"
                        name="password"
                        type="password"
                        class="form-input @if ($errors->userDeletion->has('password')) error @endif"
                        placeholder="{{ __('Your password') }}"
                        autocomplete="current-password"
                    />
                    <button type="button" class="profile-toggle-password" data-toggle-password="#delete_password">Show</button>
                </div>
                @if ($errors->userDeletion->has('password'))
                    <p class="form-error">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="profile-modal-actions">
                <button type="button" class="btn btn-outline-sm" data-close-delete-modal>{{ __('Cancel') }}</button>
                <button type="submit" class="btn profile-delete-submit">{{ __('Delete account') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('profile-delete-modal');
    if (!modal) return;

    const open = () => {
        modal.hidden = false;
        document.body.classList.add('profile-modal-open');
        const input = modal.querySelector('#delete_password');
        if (input) input.focus();
    };
    const close = () => {
        modal.hidden = true;
        document.body.classList.remove('profile-modal-open');
    };

    document.querySelectorAll('[data-open-delete-modal]').forEach((btn) => {
        btn.addEventListener('click', open);
    });
    document.querySelectorAll('[data-close-delete-modal]').forEach((el) => {
        el.addEventListener('click', close);
    });

    if (modal.dataset.open === 'true') {
        open();
    }

    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.querySelector(btn.getAttribute('data-toggle-password'));
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Hide' : 'Show';
        });
    });

    document.querySelectorAll('[data-profile-flash]').forEach((el) => {
        setTimeout(() => {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, 2500);
    });
})();
</script>
@endpush
