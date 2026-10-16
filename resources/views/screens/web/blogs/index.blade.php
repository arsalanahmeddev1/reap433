@extends('layouts.web.master')
@section('title', __('All Articles'))

@section('meta_description', __('Stay current on voting rights, education reform, and civic leadership across Texas and beyond.'))

@section('content')
<main id="main">
    <section class="journal-page section-pad civic-hub" aria-labelledby="journal-title">
        <div class="container">
            <div class="section-header journal-header" data-scroll-reveal>
                <span class="section-eyebrow">{{ __('The REAP433 Civic Hub') }}</span>
                <h1 class="section-title" id="journal-title">{{ __('All Articles') }}</h1>
                <p class="section-sub">{{ __('Stay current on voting rights, education reform, and civic leadership across Texas and beyond.') }}</p>
            </div>

            <div class="journal-layout">
                <aside class="journal-sidebar" aria-label="{{ __('Filter articles') }}">
                    <p class="journal-sidebar-title">{{ __('Categories') }}</p>
                    <nav class="journal-filters" role="tablist" aria-label="{{ __('Blog categories') }}">
                        <button
                            type="button"
                            class="journal-filter hub-cat {{ $activeCategory === null || $activeCategory === 'all' ? 'active' : '' }}"
                            role="tab"
                            aria-selected="{{ $activeCategory === null || $activeCategory === 'all' ? 'true' : 'false' }}"
                            data-cat="all"
                        >{{ __('All Articles') }}</button>
                        @foreach ($categories as $category)
                            <button
                                type="button"
                                class="journal-filter hub-cat {{ $activeCategory === $category->slug ? 'active' : '' }}"
                                role="tab"
                                aria-selected="{{ $activeCategory === $category->slug ? 'true' : 'false' }}"
                                data-cat="{{ $category->slug }}"
                            >{{ $category->name }}</button>
                        @endforeach
                    </nav>
                </aside>

                <div class="journal-main">
                    <div id="journal-results" data-posts-url="{{ route('blog.posts') }}">
                        @include('screens.web.blogs.partials.posts-results', ['blogs' => $blogs])
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
(function () {
    var root = document.getElementById('journal-results');
    if (!root) return;

    var postsUrl = root.getAttribute('data-posts-url');
    var filters = document.querySelectorAll('.journal-filter');

    function setActive(cat) {
        filters.forEach(function (btn) {
            var on = btn.getAttribute('data-cat') === cat;
            btn.classList.toggle('active', on);
            btn.setAttribute('aria-selected', on ? 'true' : 'false');
        });
    }

    function prettyUrl(cat, page) {
        var pretty = new URL('{{ route('blog.index') }}', window.location.origin);
        if (cat && cat !== 'all') {
            pretty.searchParams.set('category', cat);
        }
        if (page && String(page) !== '1') {
            pretty.searchParams.set('page', page);
        }
        return pretty.pathname + (pretty.search || '');
    }

    function loadPosts(cat, page) {
        var url = new URL(postsUrl, window.location.origin);
        cat = cat || 'all';

        if (cat !== 'all') {
            url.searchParams.set('category', cat);
        }

        if (page && String(page) !== '1') {
            url.searchParams.set('page', page);
        }

        root.classList.add('is-loading');
        setActive(cat);

        fetch(url.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) {
                if (!res.ok) throw new Error('Request failed');
                return res.json();
            })
            .then(function (data) {
                root.innerHTML = data.html || '';
                var active = data.activeCategory || cat;
                setActive(active);
                window.history.replaceState({}, '', prettyUrl(active, data.page || page || 1));
                root.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function () {
                window.location.href = prettyUrl(cat, page || 1);
            })
            .finally(function () { root.classList.remove('is-loading'); });
    }

    filters.forEach(function (btn) {
        btn.addEventListener('click', function () {
            loadPosts(btn.getAttribute('data-cat') || 'all', 1);
        });
    });

    root.addEventListener('click', function (e) {
        var link = e.target.closest('.storefront-pagination a');
        if (!link || !link.getAttribute('href')) return;
        e.preventDefault();

        var active = document.querySelector('.journal-filter.active');
        var cat = active ? active.getAttribute('data-cat') : 'all';
        var page = new URL(link.href, window.location.origin).searchParams.get('page') || 1;
        loadPosts(cat, page);
    });
})();
</script>
@endpush
