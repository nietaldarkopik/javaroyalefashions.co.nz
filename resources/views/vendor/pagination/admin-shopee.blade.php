@if ($paginator->hasPages())
<nav class="shopee-pagination" role="navigation">

    @if ($paginator->onFirstPage())
        <span class="page-btn disabled">&laquo;</span>
    @else
        <a class="page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a class="page-btn" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a class="page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
    @else
        <span class="page-btn disabled">&raquo;</span>
    @endif

</nav>
<p class="shopee-pagination-meta">
    Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
</p>
@endif
