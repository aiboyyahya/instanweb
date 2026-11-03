@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-fg-muted">
        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                @if (! empty($item['url']) && ! $loop->last)
                    <a href="{{ $item['url'] }}" class="rounded px-0.5 transition hover:text-fg">{{ $item['label'] }}</a>
                @else
                    <span @if ($loop->last) aria-current="page" class="font-medium text-fg" @endif>
                        {{ $item['label'] }}
                    </span>
                @endif

                @if (! $loop->last)
                    <x-ui.icon name="chevron-right" class="h-3.5 w-3.5 text-fg-muted" />
                @endif
            </li>
        @endforeach
    </ol>
</nav>
