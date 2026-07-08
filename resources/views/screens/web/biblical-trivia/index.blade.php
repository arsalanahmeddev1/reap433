@extends('layouts.web.master')

@section('title', 'Reap433 — Bible Trivia Decks')

@push('meta')
    <meta name="description" content="Reap433 Bible Trivia — Seven decks covering Faith, Baptism, Tithing, Salvation, Holy Spirit, Spiritual Gifts, and Reap What You Sow." />
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/web/css/biblical-trivia.css') }}">
@endpush

@section('content')
<main id="main" class="biblical-trivia-page">
    <div id="reap433-decks" role="application" aria-label="Reap433 Bible Trivia Decks"></div>
    <noscript>
        <p style="text-align:center;padding:2rem;color:#6b5d4f;">JavaScript is required to play the Bible Trivia Card Game.</p>
    </noscript>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('assets/web/js/biblical-trivia-decks.js') }}"></script>
    <script src="{{ asset('assets/web/js/biblical-trivia.js') }}"></script>
@endpush
