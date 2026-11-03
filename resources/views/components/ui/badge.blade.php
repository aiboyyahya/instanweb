@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-neutral-soft text-neutral-ink border-line',
        'success' => 'bg-success-soft text-success border-success/25',
        'warning' => 'bg-warning-soft text-warning border-warning/25',
        'danger' => 'bg-danger-soft text-danger border-danger/25',
        'info' => 'bg-info-soft text-info border-info/25',
        'shipping' => 'bg-shipping-soft text-shipping border-shipping/25',
        'accent' => 'bg-surface-soft text-brand-text border-line',
    ];
@endphp

<span
    {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold leading-none ' .
            ($variants[$variant] ?? $variants['neutral']),
    ]) }}>
    {{ $slot }}
</span>
