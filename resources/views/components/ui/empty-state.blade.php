@props([
    'title',
    'description' => null,
    'icon' => 'package',
])

<div
    {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-panel border border-dashed border-line-strong bg-surface px-6 py-12 text-center']) }}>
    <span class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-surface-soft text-fg-muted"
        aria-hidden="true">
        <x-ui.icon :name="$icon" class="h-7 w-7" />
    </span>

    <h2 class="text-lg font-semibold text-fg">{{ $title }}</h2>

    @if ($description)
        <p class="mt-1 max-w-md text-sm leading-relaxed text-fg-muted">{{ $description }}</p>
    @endif

    @if (! $slot->isEmpty())
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
