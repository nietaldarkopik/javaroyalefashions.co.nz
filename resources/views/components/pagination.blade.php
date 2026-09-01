@if ($paginator->hasPages())
<div style="display:flex; justify-content:center; gap:8px; margin-top:32px; margin-bottom:32px; flex-wrap:wrap;">
    @if ($paginator->onFirstPage())
    <span class="chip" style="opacity:0.4;"><i class="fa-solid fa-chevron-left"></i> Prev</span>
    @else
    <a class="chip" href="{{ $paginator->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i> Prev</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
        <span class="chip" style="opacity:0.4;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                <span class="chip active">{{ $page }}</span>
                @else
                <a class="chip" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
    <a class="chip" href="{{ $paginator->nextPageUrl() }}">Next <i class="fa-solid fa-chevron-right"></i></a>
    @else
    <span class="chip" style="opacity:0.4;">Next <i class="fa-solid fa-chevron-right"></i></span>
    @endif
</div>
@endif
