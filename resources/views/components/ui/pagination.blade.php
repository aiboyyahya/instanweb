@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $start + 4);
        $start = max(1, $end - 4);
    @endphp

    <nav aria-label="Navigasi halaman"
        {{ $attributes->merge(['class' => 'flex flex-col items-center justify-between gap-3 border-t border-line pt-5 sm:flex-row']) }}>
        <p class="text-sm text-fg-muted">
            Menampilkan
            <span class="font-medium text-fg">{{ $paginator->firstItem() }}</span>
            &ndash;
            <span class="font-medium text-fg">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-fg">{{ $paginator->total() }}</span>
            hasil
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex h-10 min-w-10 cursor-not-allowed items-center justify-center rounded-field border border-line px-3 text-sm text-fg-muted opacity-60"
                    aria-disabled="true">
                    <x-ui.icon name="chevron-left" class="h-4 w-4" />
                    <span class="sr-only">Halaman sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-field border border-line bg-surface px-3 text-sm text-fg transition hover:bg-surface-soft">
                    <x-ui.icon name="chevron-left" class="h-4 w-4" />
                    <span class="sr-only">Halaman sebelumnya</span>
                </a>
            @endif

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-field border border-line bg-surface px-3 text-sm text-fg transition hover:bg-surface-soft">1</a>
                @if ($start > 2)
                    <span class="px-1 text-sm text-fg-muted" aria-hidden="true">&hellip;</span>
                @endif
            @endif

            @foreach ($paginator->getUrlRange($start, $end) as $page => $url)
                @if ($page === $current)
                    <span aria-current="page"
                        class="inline-flex h-10 min-w-10 items-center justify-center rounded-field border border-cta bg-cta px-3 text-sm font-semibold text-on-cta">{{ $page }}</span>
                @else
                    <a href="{{ $url }}"
                        class="inline-flex h-10 min-w-10 items-center justify-center rounded-field border border-line bg-surface px-3 text-sm text-fg transition hover:bg-surface-soft">{{ $page }}</a>
                @endif
            @endforeach

            @if ($end < $last)
                @if ($end < $last - 1)
                    <span class="px-1 text-sm text-fg-muted" aria-hidden="true">&hellip;</span>
                @endif
                <a href="{{ $paginator->url($last) }}"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-field border border-line bg-surface px-3 text-sm text-fg transition hover:bg-surface-soft">{{ $last }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-field border border-line bg-surface px-3 text-sm text-fg transition hover:bg-surface-soft">
                    <x-ui.icon name="chevron-right" class="h-4 w-4" />
                    <span class="sr-only">Halaman berikutnya</span>
                </a>
            @else
                <span
                    class="inline-flex h-10 min-w-10 cursor-not-allowed items-center justify-center rounded-field border border-line px-3 text-sm text-fg-muted opacity-60"
                    aria-disabled="true">
                    <x-ui.icon name="chevron-right" class="h-4 w-4" />
                    <span class="sr-only">Halaman berikutnya</span>
                </span>
            @endif
        </div>
    </nav>
@endif
