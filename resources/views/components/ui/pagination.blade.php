@props([
    'paginator',
])

@if ($paginator->hasPages())
    <nav class="rs-pagination" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="rs-page-link is-disabled" aria-disabled="true">
                <x-ui.icon name="arrow-left" :size="15" />
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="rs-page-link" rel="prev">
                <x-ui.icon name="arrow-left" :size="15" />
                <span>Sebelumnya</span>
            </a>
        @endif

        <ol class="rs-page-list">
            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <li>
                        <span class="rs-page-number is-active" aria-current="page">{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $url }}" class="rs-page-number">{{ $page }}</a>
                    </li>
                @endif
            @endforeach
        </ol>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="rs-page-link" rel="next">
                <span>Berikutnya</span>
                <x-ui.icon name="arrow-right" :size="15" />
            </a>
        @else
            <span class="rs-page-link is-disabled" aria-disabled="true">
                <span>Berikutnya</span>
                <x-ui.icon name="arrow-right" :size="15" />
            </span>
        @endif
    </nav>
@endif
