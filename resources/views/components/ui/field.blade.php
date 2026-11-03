@props([
    'label',
    'name',
    'hint' => null,
    'required' => false,
    'inputId' => null,
])

@php
    $error = $errors->first($name);
    $inputId = $inputId ?: $name;
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    <label for="{{ $inputId }}" class="block text-sm font-medium text-fg-secondary">
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-hidden="true">*</span>
            <span class="sr-only">(wajib)</span>
        @endif
    </label>

    {{ $slot }}

    @if ($hint && !$error)
        <p id="{{ $inputId }}-hint" class="text-xs leading-relaxed text-fg-muted">{{ $hint }}</p>
    @endif

    @if ($error)
        <p id="{{ $inputId }}-error" class="flex items-start gap-1.5 text-xs font-medium text-danger" role="alert">
            <x-ui.icon name="alert-circle" class="mt-0.5 h-4 w-4" />
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
