@if ($blogs->count())
    <div class="journal-grid blog-grid" role="list" aria-label="{{ __('Blog articles') }}">
        @foreach ($blogs as $blog)
            @include('screens.web.home.partials.blog-card', [
                'blog' => $blog,
                'isFeatured' => false,
                'useScrollReveal' => false,
            ])
        @endforeach
    </div>

    @if ($blogs->hasPages())
        <div class="journal-pagination printful-products-pagination">
            {{ $blogs->links('vendor.pagination.storefront') }}
        </div>
    @endif
@else
    <div class="journal-empty" role="status">
        <p class="journal-empty-title">{{ __('No article available') }}</p>
        <p class="journal-empty-text">{{ __('There are no published articles in this category yet. Try another filter or check back soon.') }}</p>
    </div>
@endif
