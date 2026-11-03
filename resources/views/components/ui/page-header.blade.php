@props([
    'title',
    'description' => null,
    'eyebrow' => null,
    'align' => 'left',
])

<div
    {{ $attributes->merge([
        'class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between ' . ($align === 'center' ? 'text-center sm:text-left' : ''),
    ]) }}>
    <div class="max-w-2xl">
        @if ($eyebrow)
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">{{ $eyebrow }}</p>
        @endif

        <h1 class="text-3xl font-bold leading-tight tracking-tight text-fg sm:text-4xl">{{ $title }}</h1>

        @if ($description)
            <p class="mt-2 text-sm leading-relaxed text-fg-muted sm:text-base">{{ $description }}</p>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-3">{{ $slot }}</div>
    @endif
</div>
