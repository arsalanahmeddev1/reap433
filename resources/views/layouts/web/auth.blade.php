<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.web.partials.head')
</head>
<body class="auth-page">
    <div class="auth-bg" aria-hidden="true">
        <div class="auth-bg-mesh"></div>
        <div class="auth-bg-noise"></div>
    </div>
    <a href="{{ route('home') }}" class="auth-back-home">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15,18 9,12 15,6"/></svg>
        Back to home
    </a>
    <main class="auth-shell">
        <div class="auth-card">
            <a href="{{ route('home') }}" class="auth-logo" aria-label="REAP433 Home">
                <img src="{{ asset('assets/web/images/logo.png') }}" alt="REAP433" />
            </a>
            @yield('content')
        </div>
    </main>
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = document.querySelector(btn.getAttribute('data-toggle-password'));
                if (!input) return;
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                btn.textContent = isHidden ? 'Hide' : 'Show';
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
