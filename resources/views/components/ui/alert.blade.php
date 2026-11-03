@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $styles = [
        'success' => [
            'wrapper' => 'border-success/30 bg-success-soft text-success',
            'icon' => 'check-circle',
            'role' => 'status',
        ],
        'warning' => [
            'wrapper' => 'border-warning/30 bg-warning-soft text-warning',
            'icon' => 'alert-triangle',
            'role' => 'status',
        ],
        'danger' => [
            'wrapper' => 'border-danger/30 bg-danger-soft text-danger',
            'icon' => 'alert-circle',
            'role' => 'alert',
        ],
        'error' => [
            'wrapper' => 'border-danger/30 bg-danger-soft text-danger',
            'icon' => 'alert-circle',
            'role' => 'alert',
        ],
        'info' => [
            'wrapper' => 'border-info/30 bg-info-soft text-info',
            'icon' => 'info',
            'role' => 'status',
        ],
    ];

    $style = $styles[$variant] ?? $styles['info'];
@endphp

<div data-alert role="{{ $style['role'] }}"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-card border px-4 py-3 text-sm ' . $style['wrapper']]) }}>
    <x-ui.icon :name="$style['icon']" class="mt-0.5 h-5 w-5" />

    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div class="{{ $title ? 'mt-1' : '' }} leading-relaxed">{{ $slot }}</div>
    </div>

    @if ($dismissible)
        <button type="button" data-alert-dismiss
            class="-mr-2 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full opacity-70 transition hover:opacity-100 focus-visible:opacity-100"
            aria-label="Tutup pesan">
            <x-ui.icon name="close" class="h-4 w-4" />
        </button>
    @endif
</div>
