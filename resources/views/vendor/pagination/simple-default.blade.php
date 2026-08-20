@if ($paginator->hasPages())
    <nav class="pagination">
        {{-- Página anterior --}}
        @if ($paginator->onFirstPage())
            <span>‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
        @endif

        {{-- Páginas numeradas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span>…</span>
            @else
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="active-page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Página siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
        @else
            <span>›</span>
        @endif
    </nav>
@endif