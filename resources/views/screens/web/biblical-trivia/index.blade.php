@extends('layouts.web.master')

@section('title', 'Biblical Trivia Card Game')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/web/css/biblical-trivia.css') }}">
@endpush

@section('content')
<main id="main" class="biblical-trivia-page">
    <section class="biblical-trivia-section" aria-labelledby="biblical-trivia-heading">
        <div class="container biblical-trivia-container">
            <div id="biblical-trivia-app" class="biblical-trivia-app" role="application" aria-label="Biblical Trivia Card Game">
                <div id="biblical-trivia-root" class="biblical-trivia-root" aria-live="polite"></div>
                <noscript>
                    <p class="biblical-trivia-noscript">JavaScript is required to play the Biblical Trivia Card Game.</p>
                </noscript>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('assets/web/js/biblical-trivia-decks.js') }}"></script>
    <script src="{{ asset('assets/web/js/biblical-trivia.js') }}"></script>
@endpush
