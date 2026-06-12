@extends('layouts.profile')
@section('title', __('Edit Address'))

@section('profile_heading', __('Edit Address'))
@section('profile_subheading', __('Update your saved delivery address.'))

@section('profile_content')
    @include('profile.partials.address-form', ['userAddress' => $userAddress])
@endsection
