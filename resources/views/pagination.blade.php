@if ($paginator->hasPages())
<nav>
    <ul class="pagination justify-content-center">

        {{-- PREV --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">‹</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}">‹</a>
            </li>
        @endif

        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();

            // show current page first
            $start = max(1, $current);
            $end = min($current + 5, $last);
        @endphp

        {{-- PAGE NUMBERS --}}
        @for ($i = $start; $i <= $end; $i++)
            <li class="page-item {{ $i == $current ? 'active' : '' }}">
                @if ($i == $current)
                    <span class="page-link">{{ $i }}</span>
                @else
                    <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                @endif
            </li>
        @endfor

        {{-- NEXT --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}">›</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">›</span>
            </li>
        @endif

    </ul>
</nav>
@endif
