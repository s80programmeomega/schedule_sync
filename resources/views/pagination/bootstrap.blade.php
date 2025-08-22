@if ($paginator->hasPages())
<nav>
    <ul class="pagination justify-content-center">
        @if ($paginator->onFirstPage())
        <li class="page-item disabled"><span class="page-link">Previous</span></li>
        @else
        <li class="page-item"><a class="page-link btn-primary-style"
                href="{{ $paginator->previousPageUrl() }}">Previous</a></li>
        @endif

        @foreach ($elements as $element)
        @if (is_string($element))
        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="page-item active"><span class="page-link btn-primary-style">{{ $page }}</span></li>
        @else
        <li class="page-item"><a class="page-link pagination-link" href="{{ $url }}">{{ $page }}</a></li>
        @endif
        @endforeach
        @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <li class="page-item"><a class="page-link btn-primary-style" href="{{ $paginator->nextPageUrl() }}">Next</a>
        </li>
        @else
        <li class="page-item disabled"><span class="page-link">Next</span></li>
        @endif
    </ul>
</nav>

<style>
    .btn-primary-style {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: white !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-primary-style:hover {
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .pagination-link {
        color: var(--primary) !important;
        border-color: var(--gray-light) !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pagination-link:hover {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: white !important;
        transform: translateY(-1px);
    }
</style>
@endif
