@props([
    'rating' => null,
    'count' => null,
    'size' => 'sm',
    'showValue' => false,
])

@php
    $value = (float) ($rating ?? 0);
    $rounded = (int) round($value);
    $starSize = $size === 'lg' ? 'h-5 w-5' : ($size === 'md' ? 'h-4 w-4' : 'h-3.5 w-3.5');
    $label = $value > 0
        ? 'Rating ' . number_format($value, 1, ',', '.') . ' dari 5' . ($count ? ' berdasarkan ' . $count . ' ulasan' : '')
        : 'Belum ada rating';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-1.5']) }} role="img" aria-label="{{ $label }}">
    <div class="flex items-center gap-0.5" aria-hidden="true">
        @for ($i = 1; $i <= 5; $i++)
            <svg class="{{ $starSize }} {{ $i <= $rounded ? 'text-rating' : 'text-line-strong' }}" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 0 0 .95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 0 0-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 0 0-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 0 0-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 0 0 .95-.69l1.286-3.947Z" />
            </svg>
        @endfor
    </div>

    @if ($showValue && $value > 0)
        <span class="text-xs font-semibold text-fg">{{ number_format($value, 1, ',', '.') }}</span>
    @endif

    @if (! is_null($count) && $count > 0)
        <span class="text-xs text-fg-muted">({{ number_format($count, 0, ',', '.') }})</span>
    @endif
</div>
