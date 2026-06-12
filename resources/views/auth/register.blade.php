@extends('layouts.web.auth')
@section('title', __('Create account'))

@section('content')
    <p class="auth-eyebrow">{{ __('Join REAP433') }}</p>
    <h1 class="auth-title">{{ __('Create your account') }}</h1>
    <p class="auth-sub">{{ __('Shop the collection and track your orders') }}</p>

    <form class="auth-form" method="POST" action="{{ route('register') }}" autocomplete="off">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="name">{{ __('Full name') }}</label>
            <input
                id="name"
                class="auth-input @error('name') is-invalid @enderror"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Your name"
            />
            @error('name')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input
                id="email"
                class="auth-input @error('email') is-invalid @enderror"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
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
                    autocomplete="new-password"
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
                    placeholder="********"
                />
                <button type="button" class="auth-toggle-password" data-toggle-password="#password_confirmation">Show</button>
            </div>
        </div>

        <button type="submit" class="btn btn-gold auth-submit">{{ __('Create account') }}</button>
    </form>

    <p class="auth-switch">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
    </p>
@endsection
