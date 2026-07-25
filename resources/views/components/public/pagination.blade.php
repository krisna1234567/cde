@if ($paginator->hasPages())
    <nav class="media-pagination" aria-label="Media pagination">
        @if ($paginator->onFirstPage())
            <span class="pagination-control disabled" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
        @else
            <a class="pagination-control" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-ellipsis">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page === $paginator->currentPage())
                        <span class="pagination-page active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pagination-page" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pagination-control" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
        @else
            <span class="pagination-control disabled" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
        @endif
    </nav>
@endif
