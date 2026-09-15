@extends('layouts.web.master')
@section('title', $page->seo_title ?: $page->title)

@section('meta_description', (string) ($page->seo_description ?: ''))

@section('content')
<main id="main">
    <section class="site-page-section section-pad" aria-labelledby="site-page-title">
        <div class="site-page-container">
            @if ($page->imageUrl())
                <div class="site-page-hero">
                    <img
                        src="{{ $page->imageUrl() }}"
                        alt="{{ $page->title }}"
                        class="site-page-image"
                        loading="eager"
                    />
                </div>
            @endif

            <h1 class="site-page-title" id="site-page-title">{{ $page->title }}</h1>

            @if ($page->description)
                <div class="site-page-body">
                    {!! $page->description !!}
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
