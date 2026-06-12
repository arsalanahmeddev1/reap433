@extends('layouts.profile')
@section('title', __('Password'))

@section('profile_heading', __('Update Password'))
@section('profile_subheading', __('Ensure your account is using a long, random password to stay secure.'))

@section('profile_content')
    @include('profile.partials.update-password-form')
@endsection
