@php
    $isFeatured = $isFeatured ?? false;
    $delay = $delay ?? 0;
    $categorySlug = $blog->category?->slug ?? '';
    $imageUrl = $blog->featuredImageUrl() ?? 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80&auto=format&fit=crop';
    $excerpt = \Illuminate\Support\Str::limit(strip_tags((string) $blog->body), $isFeatured ? 200 : 140);
    $blogUrl = route('blog.show', $blog->slug);
@endphp

<article
    class="blog-card {{ $isFeatured ? 'blog-featured' : 'blog-standard' }}"
    role="listitem"
    data-cat="{{ $categorySlug }}"
    data-scroll-reveal
    data-delay="{{ $delay }}"
>
    <div class="blog-image-wrap">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $blog->title }}"
            class="blog-image"
            loading="lazy"
        />
        @if ($blog->category)
            <span class="blog-category-badge">{{ $blog->category->name }}</span>
        @endif
    </div>
    <div class="blog-content">
        <h3 class="blog-title">
            <a href="{{ $blogUrl }}" class="blog-title-link">{{ $blog->title }}</a>
        </h3>
        @if ($excerpt !== '')
            <p class="blog-excerpt">{{ $excerpt }}</p>
        @endif
        <a href="{{ $blogUrl }}" class="blog-read-more">
            {{ $isFeatured ? __('Read Full Article') : __('Read More') }}
            <svg width="{{ $isFeatured ? 16 : 14 }}" height="{{ $isFeatured ? 16 : 14 }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
    </div>
</article>
