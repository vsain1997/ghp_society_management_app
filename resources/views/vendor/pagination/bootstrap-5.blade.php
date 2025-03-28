@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->appends(request()->except('page'))->previousPageUrl() }}"
                        rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Start Ellipsis and First Page --}}
            @if ($paginator->currentPage() > 3)
                <li class="page-item">
                    <span class="page-link">...</span>
                </li>
            @endif

            {{-- Middle Page Links --}}
            @php
                // Determine the range of pages to display
                $start = max(1, $paginator->currentPage() - 1);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 1);
            @endphp

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $paginator->appends(request()->except('page'))->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- End Ellipsis --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                <li class="page-item">
                    <span class="page-link">...</span>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->appends(request()->except('page'))->nextPageUrl() }}"
                        rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
