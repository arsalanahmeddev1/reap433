<aside class="profile-sidebar">
    <div class="profile-user-card">
        <div class="profile-avatar" aria-hidden="true">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <h2 class="profile-user-name">{{ $user->name }}</h2>
        <p class="profile-user-email">{{ $user->email }}</p>
        <span class="profile-user-role">{{ ucfirst($user->role ?? 'user') }}</span>
    </div>
    <nav class="profile-nav" aria-label="{{ __('Profile sections') }}">
        <a href="{{ route('profile.edit') }}" class="profile-nav-link @if (request()->routeIs('profile.edit')) is-active @endif">
            {{ __('Profile information') }}
        </a>
        <a href="{{ route('profile.password.edit') }}" class="profile-nav-link @if (request()->routeIs('profile.password.edit')) is-active @endif">
            {{ __('Password') }}
        </a>
        <a href="{{ route('profile.addresses.index') }}" class="profile-nav-link @if (request()->routeIs('profile.addresses.*')) is-active @endif">
            {{ __('Addresses') }}
        </a>
        <a href="{{ route('profile.orders.index') }}" class="profile-nav-link @if (request()->routeIs('profile.orders.*')) is-active @endif">
            {{ __('Orders') }}
        </a>
    </nav>
    <a href="{{ route('cart.index') }}" class="btn btn-outline-sm profile-cart-link">{{ __('View cart') }}</a>
</aside>
