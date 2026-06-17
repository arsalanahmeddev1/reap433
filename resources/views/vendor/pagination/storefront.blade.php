@if ($paginator->hasPages())
    <nav class="storefront-pagination" role="navigation" aria-label="Pagination">
        <ul class="storefront-pagination__list">
            @if ($paginator->onFirstPage())
                <li class="storefront-pagination__item storefront-pagination__item--disabled">
                    <span aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="storefront-pagination__item">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&lsaquo;</a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="storefront-pagination__item storefront-pagination__item--ellipsis">
                        <span>{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="storefront-pagination__item storefront-pagination__item--active" aria-current="page">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li class="storefront-pagination__item">
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="storefront-pagination__item">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&rsaquo;</a>
                </li>
            @else
                <li class="storefront-pagination__item storefront-pagination__item--disabled">
                    <span aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
