@extends('layouts.profile')
@section('title', __('My Addresses'))

@section('profile_heading', __('My Addresses'))
@section('profile_subheading', __('Save shipping addresses for faster checkout.'))

@section('profile_content')
    <section class="profile-panel" aria-labelledby="profile-addresses-title">
        <header class="profile-panel-header profile-panel-header--row">
            <div>
                <h2 class="profile-panel-title" id="profile-addresses-title">{{ __('Saved addresses') }}</h2>
                <p class="profile-panel-sub">{{ __('Manage where your orders are delivered.') }}</p>
            </div>
            <a href="{{ route('profile.addresses.create') }}" class="btn btn-gold-sm">{{ __('Add address') }}</a>
        </header>

        @if (session('status') === 'address-created')
            <p class="profile-flash profile-flash--success" data-profile-flash>{{ __('Address added.') }}</p>
        @elseif (session('status') === 'address-updated')
            <p class="profile-flash profile-flash--success" data-profile-flash>{{ __('Address updated.') }}</p>
        @elseif (session('status') === 'address-deleted')
            <p class="profile-flash profile-flash--success" data-profile-flash>{{ __('Address removed.') }}</p>
        @elseif (session('status') === 'address-default-set')
            <p class="profile-flash profile-flash--success" data-profile-flash>{{ __('Default address updated.') }}</p>
        @endif

        @if ($addresses->isNotEmpty())
            <div class="profile-address-list">
                @foreach ($addresses as $address)
                    <article class="profile-address-card @if ($address->is_default) is-default @endif">
                        <div class="profile-address-card-top">
                            <div>
                                @if ($address->label)
                                    <p class="profile-address-label">{{ $address->label }}</p>
                                @endif
                                <h3 class="profile-address-name">{{ $address->full_name }}</h3>
                            </div>
                            @if ($address->is_default)
                                <span class="profile-address-badge">{{ __('Default') }}</span>
                            @endif
                        </div>
                        <p class="profile-address-lines">
                            {{ $address->street_address }}@if ($address->street_address_2), {{ $address->street_address_2 }}@endif<br>
                            {{ $address->city }}@if ($address->state), {{ $address->state }}@endif {{ $address->zipcode }}<br>
                            {{ $address->country }}
                        </p>
                        @if ($address->phone)
                            <p class="profile-address-phone">{{ $address->phone }}</p>
                        @endif
                        <div class="profile-address-actions">
                            <a href="{{ route('profile.addresses.edit', $address) }}" class="profile-address-action">{{ __('Edit') }}</a>
                            @unless ($address->is_default)
                                <form method="post" action="{{ route('profile.addresses.default', $address) }}">
                                    @csrf
                                    @method('patch')
                                    <button type="submit" class="profile-address-action">{{ __('Set default') }}</button>
                                </form>
                            @endunless
                            <form method="post" action="{{ route('profile.addresses.destroy', $address) }}" onsubmit="return confirm(@json(__('Remove this address?')))">
                                @csrf
                                @method('delete')
                                <button type="submit" class="profile-address-action profile-address-action--danger">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="profile-address-empty">{{ __('No saved addresses yet.') }}</p>
            <a href="{{ route('profile.addresses.create') }}" class="btn btn-gold">{{ __('Add your first address') }}</a>
        @endif
    </section>
@endsection
