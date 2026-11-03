@props([
    'variant' => 'surface',
    'size' => 'md',
])

@php
    $sizes = [
        'xs' => 'text-sm',
        'sm' => 'text-base',
        'md' => 'text-lg',
        'lg' => 'text-xl',
        'xl' => 'text-2xl sm:text-3xl',
    ];

    $ink = $variant === 'inverse' ? 'text-white' : 'text-black dark:text-white';
@endphp

<span {{ $attributes->class(['iw-wordmark', $sizes[$size] ?? $sizes['md']]) }}><span class="{{ $ink }}">instan</span><span class="text-brand">web</span></span>
