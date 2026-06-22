@extends('layouts.web.master')

@section('title', 'Biblical Trivia Card Game')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/web/css/biblical-trivia.css') }}">
@endpush

@section('content')
<main id="main" class="biblical-trivia-page">
    <section class="biblical-trivia-section" aria-labelledby="biblical-trivia-heading">
        <div class="container biblical-trivia-container">
            <div id="reap433-decks" role="application" aria-label="Reap433 Bible Trivia Card Game"></div>
            <noscript>
                <p style="text-align:center;padding:2rem;color:#6b5d4f;">JavaScript is required to play the Bible Trivia Card Game.</p>
            </noscript>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('assets/web/js/biblical-trivia-decks.js') }}"></script>
    <script src="{{ asset('assets/web/js/biblical-trivia.js') }}"></script>
@endpush
