@php
    $editing = $userAddress ?? null;
@endphp

<section class="profile-panel" aria-labelledby="profile-address-form-title">
    <header class="profile-panel-header">
        <h2 class="profile-panel-title" id="profile-address-form-title">
            {{ $editing ? __('Edit address') : __('Add new address') }}
        </h2>
        <p class="profile-panel-sub">
            {{ $editing ? __('Update your delivery details below.') : __('Enter your delivery details below.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ $editing ? route('profile.addresses.update', $editing) : route('profile.addresses.store') }}"
        class="profile-form profile-address-form"
    >
        @csrf
        @if ($editing)
            @method('patch')
        @endif

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="address_label">{{ __('Label') }}</label>
                <input
                    id="address_label"
                    name="label"
                    type="text"
                    class="form-input @if ($errors->address->has('label')) error @endif"
                    value="{{ old('label', $editing?->label) }}"
                    placeholder="{{ __('Home, Office, etc.') }}"
                />
                @if ($errors->address->has('label'))
                    <p class="form-error">{{ $errors->address->first('label') }}</p>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label" for="address_full_name">{{ __('Full name') }}</label>
                <input
                    id="address_full_name"
                    name="full_name"
                    type="text"
                    class="form-input @if ($errors->address->has('full_name')) error @endif"
                    value="{{ old('full_name', $editing?->full_name ?? $user->name) }}"
                    required
                />
                @if ($errors->address->has('full_name'))
                    <p class="form-error">{{ $errors->address->first('full_name') }}</p>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="address_phone">{{ __('Phone') }}</label>
            <input
                id="address_phone"
                name="phone"
                type="text"
                class="form-input @if ($errors->address->has('phone')) error @endif"
                value="{{ old('phone', $editing?->phone) }}"
                autocomplete="tel"
            />
            @if ($errors->address->has('phone'))
                <p class="form-error">{{ $errors->address->first('phone') }}</p>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="address_street">{{ __('Street address') }}</label>
            <input
                id="address_street"
                name="street_address"
                type="text"
                class="form-input @if ($errors->address->has('street_address')) error @endif"
                value="{{ old('street_address', $editing?->street_address) }}"
                required
                autocomplete="street-address"
            />
            @if ($errors->address->has('street_address'))
                <p class="form-error">{{ $errors->address->first('street_address') }}</p>
            @endif
        </div>

        <div class="form-group">
            <label class="form-label" for="address_street_2">{{ __('Apartment, suite, etc.') }}</label>
            <input
                id="address_street_2"
                name="street_address_2"
                type="text"
                class="form-input @if ($errors->address->has('street_address_2')) error @endif"
                value="{{ old('street_address_2', $editing?->street_address_2) }}"
                autocomplete="address-line2"
            />
            @if ($errors->address->has('street_address_2'))
                <p class="form-error">{{ $errors->address->first('street_address_2') }}</p>
            @endif
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="address_city">{{ __('City') }}</label>
                <input
                    id="address_city"
                    name="city"
                    type="text"
                    class="form-input @if ($errors->address->has('city')) error @endif"
                    value="{{ old('city', $editing?->city) }}"
                    required
                    autocomplete="address-level2"
                />
                @if ($errors->address->has('city'))
                    <p class="form-error">{{ $errors->address->first('city') }}</p>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label" for="address_state">{{ __('State') }}</label>
                <input
                    id="address_state"
                    name="state"
                    type="text"
                    class="form-input @if ($errors->address->has('state')) error @endif"
                    value="{{ old('state', $editing?->state) }}"
                    autocomplete="address-level1"
                />
                @if ($errors->address->has('state'))
                    <p class="form-error">{{ $errors->address->first('state') }}</p>
                @endif
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="address_zipcode">{{ __('Zip code') }}</label>
                <input
                    id="address_zipcode"
                    name="zipcode"
                    type="text"
                    class="form-input @if ($errors->address->has('zipcode')) error @endif"
                    value="{{ old('zipcode', $editing?->zipcode) }}"
                    required
                    autocomplete="postal-code"
                />
                @if ($errors->address->has('zipcode'))
                    <p class="form-error">{{ $errors->address->first('zipcode') }}</p>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label" for="address_country">{{ __('Country') }}</label>
                <input
                    id="address_country"
                    name="country"
                    type="text"
                    class="form-input @if ($errors->address->has('country')) error @endif"
                    value="{{ old('country', $editing?->country ?? 'United States') }}"
                    required
                    autocomplete="country-name"
                />
                @if ($errors->address->has('country'))
                    <p class="form-error">{{ $errors->address->first('country') }}</p>
                @endif
            </div>
        </div>

        <label class="profile-remember">
            <input
                type="checkbox"
                name="is_default"
                value="1"
                @checked(old('is_default', $editing?->is_default ?? ($defaultChecked ?? false)))
            />
            <span>{{ __('Set as default address') }}</span>
        </label>

        <div class="profile-form-actions">
            <button type="submit" class="btn btn-gold">
                {{ $editing ? __('Update address') : __('Save address') }}
            </button>
            <a href="{{ route('profile.addresses.index') }}" class="btn btn-outline-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
</section>
