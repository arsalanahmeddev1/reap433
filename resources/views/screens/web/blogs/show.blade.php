@extends('layouts.web.master')
@section('title', $blog->title)

@section('content')
<main id="main">
    <section class="civic-hub section-pad blog-detail-section" aria-labelledby="blog-detail-title">
        <div class="container blog-detail-container">
            <a href="{{ route('home') }}#hub" class="blog-detail-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15,18 9,12 15,6"/></svg>
                {{ __('Back to Civic Hub') }}
            </a>

            <article class="blog-detail" data-scroll-reveal>
                @if ($blog->featuredImageUrl())
                    <div class="blog-detail-hero">
                        <img
                            src="{{ $blog->featuredImageUrl() }}"
                            alt="{{ $blog->title }}"
                            class="blog-detail-image"
                            loading="eager"
                        />
                        @if ($blog->category)
                            <span class="blog-category-badge">{{ $blog->category->name }}</span>
                        @endif
                    </div>
                @endif

                <div class="blog-detail-content">
                    <div class="blog-meta">
                        @if ($blog->author)
                            <span class="blog-author">
                                <span class="author-avatar author-avatar--initial" aria-hidden="true">{{ strtoupper(substr($blog->author->name, 0, 1)) }}</span>
                                {{ $blog->author->name }}
                            </span>
                        @endif
                        @if ($blog->published_at)
                            <time class="blog-date" datetime="{{ $blog->published_at->toDateString() }}">{{ $blog->published_at->format('M j, Y') }}</time>
                        @endif
                        @if ($blog->category && ! $blog->featuredImageUrl())
                            <span class="blog-category-badge blog-category-badge--inline">{{ $blog->category->name }}</span>
                        @endif
                    </div>

                    <h1 class="blog-detail-title" id="blog-detail-title">{{ $blog->title }}</h1>

                    @if ($blog->body)
                        <div class="blog-detail-body">
                            {!! $blog->body !!}
                        </div>
                    @endif
                </div>
            </article>
        </div>
    </section>
</main>
@endsection
