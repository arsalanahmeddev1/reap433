@extends('layouts.profile')
@section('title', __('Profile Information'))

@section('profile_heading', __('Profile Information'))
@section('profile_subheading', __("Update your account's profile information and email address."))

@section('profile_content')
    @include('profile.partials.update-profile-information-form')
@endsection
