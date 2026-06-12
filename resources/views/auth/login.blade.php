@extends('layouts.web.auth')
@section('title', __('Sign in'))

@section('content')
    <p class="auth-eyebrow">{{ __('Member access') }}</p>
    <h1 class="auth-title">{{ __('Welcome back') }}</h1>
    <p class="auth-sub">{{ __('Sign in to your REAP433 account') }}</p>

    @if (session('status'))
        <div class="auth-alert auth-alert-success" role="alert">{{ session('status') }}</div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input
                id="email"
                class="auth-input @error('email') is-invalid @enderror"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
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
                    autocomplete="current-password"
                    placeholder="********"
                />
                <button type="button" class="auth-toggle-password" data-toggle-password="#password">Show</button>
            </div>
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <label class="auth-remember">
            <input type="checkbox" name="remember" id="remember_me" />
            <span>{{ __('Remember me') }}</span>
        </label>

        <button type="submit" class="btn btn-gold auth-submit">{{ __('Sign in') }}</button>
    </form>

    <p class="auth-switch">
        {{ __('New here?') }}
        <a href="{{ route('register') }}">{{ __('Create an account') }}</a>
    </p>
@endsection
