@extends('layouts.profile')
@section('title', __('Add Address'))

@section('profile_heading', __('Add Address'))
@section('profile_subheading', __('Save a new delivery address to your account.'))

@section('profile_content')
    @include('profile.partials.address-form', [
        'defaultChecked' => $user->addresses()->where('is_default', true)->doesntExist(),
    ])
@endsection
