@props([
    'variant' => 'surface',
    'size' => 'md',
    'tagline' => 'Pasar Produk UMKM Indonesia',
    'showTagline' => true,
    'taglineClass' => 'hidden sm:block',
    'logo' => null,
])

@php
    $isInverse = $variant === 'inverse';

    $markSizes = [
        'sm' => 'h-8 w-8',
        'md' => 'h-10 w-10',
        'lg' => 'h-12 w-12',
        'xl' => 'h-14 w-14',
    ];

    $wordSizes = [
        'sm' => 'sm',
        'md' => 'md',
        'lg' => 'lg',
        'xl' => 'xl',
    ];

    $taglineSizes = [
        'sm' => 'text-[10px]',
        'md' => 'text-[11px]',
        'lg' => 'text-xs',
        'xl' => 'text-xs',
    ];

    $taglineColor = $isInverse ? 'text-white/60' : 'text-fg-muted';
@endphp

<span {{ $attributes->class(['inline-flex min-w-0 items-center gap-2.5']) }}>
    <img src="{{ $logo ?: asset('images/instanweb-logo.png') }}" alt="" width="560" height="564"
        class="iw-brand-mark {{ $markSizes[$size] ?? $markSizes['md'] }}" decoding="async">

    <span class="flex min-w-0 flex-col leading-none">
        <x-brand-wordmark :variant="$variant" :size="$wordSizes[$size] ?? $wordSizes['md']" />

        @if ($showTagline && filled($tagline))
            <span class="mt-1.5 truncate {{ $taglineSizes[$size] ?? $taglineSizes['md'] }} {{ $taglineColor }}">
                {{ $tagline }}
            </span>
        @endif
    </span>
</span>
