@extends('layouts.web.auth')
@section('title', __('Create account'))

@section('content')
    <p class="auth-eyebrow">{{ __('Join REAP433') }}</p>
    <h1 class="auth-title">{{ __('Create your account') }}</h1>
    <p class="auth-sub">{{ __('Choose how you want to join') }}</p>

    <div class="auth-role-choices">
        <a href="{{ route('register.user') }}" class="auth-role-choice">
            <strong>{{ __('Become a User') }}</strong>
            <span>{{ __('Shop the collection and track your orders') }}</span>
        </a>
        <a href="{{ route('register.wholesaler') }}" class="auth-role-choice">
            <strong>{{ __('Become a Whole Seller') }}</strong>
            <span>{{ __('Apply with your business details. Account requires admin approval.') }}</span>
        </a>
    </div>

    <p class="auth-switch">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
    </p>
@endsection

@push('scripts')
<style>
    .auth-role-choices {
        display: grid;
        gap: 0.85rem;
        margin: 1.5rem 0 1rem;
    }
    .auth-role-choice {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.03);
        color: inherit;
        text-decoration: none;
        transition: border-color 0.15s ease, background 0.15s ease;
    }
    .auth-role-choice:hover {
        border-color: var(--c-gold, #c9a227);
        background: rgba(201, 162, 39, 0.08);
    }
    .auth-role-choice strong {
        color: var(--c-cream, #f5f0e6);
        font-size: 1.05rem;
    }
    .auth-role-choice span {
        color: var(--c-text-secondary, #a8a29e);
        font-size: 0.875rem;
        line-height: 1.45;
    }
</style>
@endpush
