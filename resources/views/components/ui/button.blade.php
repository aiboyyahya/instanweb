@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'block' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-field font-semibold leading-none transition-colors duration-150 disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'primary' => 'bg-cta text-on-cta shadow-sm hover:bg-cta-hover',
        'secondary' => 'bg-inverse text-fg-inverse shadow-sm hover:opacity-90',
        'outline' => 'border border-line-strong bg-surface text-fg hover:bg-surface-soft',
        'ghost' => 'text-fg-secondary hover:bg-surface-soft hover:text-fg',
        'danger' => 'bg-danger-strong text-white shadow-sm hover:opacity-90',
        'subtle' => 'bg-surface-soft text-fg hover:bg-line',
    ];

    $sizes = [
        'sm' => 'min-h-9 px-3 text-xs',
        'md' => 'min-h-11 px-4 text-sm',
        'lg' => 'min-h-12 px-6 text-base',
        'icon' => 'h-11 w-11 p-0',
    ];

    $classes = trim(
        $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']) . ($block ? ' w-full' : ''),
    );
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
