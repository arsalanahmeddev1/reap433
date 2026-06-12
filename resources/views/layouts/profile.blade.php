@extends('layouts.web.master')

@section('content')
<main id="main">
    <section class="shop-section section-pad profile-page" aria-labelledby="profile-title">
        <div class="container">
            <div class="section-header" data-scroll-reveal>
                <span class="section-eyebrow">{{ __('Account') }}</span>
                <h1 class="section-title" id="profile-title">@yield('profile_heading', __('My Profile'))</h1>
                <p class="section-sub">@yield('profile_subheading', __('Manage your account details and preferences.'))</p>
            </div>

            <div class="profile-layout" data-scroll-reveal>
                @include('profile.partials.sidebar')

                <div class="profile-panels">
                    @yield('profile_content')
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    @include('profile.partials.scripts')
@endpush
