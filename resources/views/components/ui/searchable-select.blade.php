@props([
    'label',
    'name',
    'endpoint',
    'parent' => null,
    'parentParam' => 'parent_id',
    'hint' => null,
    'required' => false,
    'placeholder' => 'Cari...',
    'selected' => null,
    'selectedLabel' => null,
    'emptyText' => 'Tidak ada hasil.',
    'parentPlaceholder' => 'Pilih wilayah induk terlebih dahulu.',
    'disabled' => false,
])

@php
    $inputId = $name;
    $error = $errors->first($name);
    $selected = filled($selected) && filled($selectedLabel) ? (string) $selected : '';
    $selectedLabel = $selected !== '' ? $selectedLabel : '';
@endphp

<x-ui.field :label="$label" :name="$name" :hint="$hint" :required="$required" :input-id="$inputId . '-input'">
    <div class="relative" data-searchable-select data-endpoint="{{ $endpoint }}"
        @if ($parent) data-parent-field="{{ $parent }}" @endif data-parent-param="{{ $parentParam }}"
        data-parent-placeholder="{{ $parentPlaceholder }}">
        <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $selected }}" @disabled($disabled)>

        <div class="relative">
            <input type="text" id="{{ $inputId }}-input" data-combobox-input value="{{ $selectedLabel }}"
                placeholder="{{ $placeholder }}" autocomplete="off" role="combobox" aria-autocomplete="list"
                aria-expanded="false" aria-controls="{{ $inputId }}-listbox"
                @if ($error) aria-invalid="true" aria-describedby="{{ $inputId }}-input-error" @endif
                @disabled($disabled)
                @class([
                    'h-12 w-full rounded-field border bg-surface pl-3 pr-16 text-sm text-fg placeholder:text-fg-muted focus:border-info disabled:cursor-not-allowed disabled:opacity-60',
                    'border-danger' => $error,
                    'border-line-strong' => !$error,
                ])>

            <button type="button" data-combobox-clear
                @class([
                    'absolute inset-y-0 right-9 my-auto flex h-7 w-7 items-center justify-center rounded-full text-fg-muted transition hover:bg-surface-soft hover:text-fg disabled:cursor-not-allowed disabled:opacity-60',
                    'hidden' => $selected === '',
                ])
                aria-label="Hapus pilihan {{ $label }}" @disabled($disabled)>
                <x-ui.icon name="close" class="h-3.5 w-3.5" />
            </button>

            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-fg-muted">
                <x-ui.icon name="chevron-down" class="h-4 w-4" />
            </span>
        </div>

        <div data-combobox-panel
            class="absolute left-0 right-0 z-50 mt-1 hidden overflow-hidden rounded-card border border-line bg-surface shadow-lg">
            <ul id="{{ $inputId }}-listbox" role="listbox" aria-label="{{ $label }}"
                class="max-h-60 overflow-y-auto p-1.5" data-combobox-list></ul>
            <p data-combobox-empty class="hidden px-3 py-2.5 text-sm text-fg-muted">{{ $emptyText }}</p>
            <p data-combobox-state class="hidden px-3 py-2.5 text-sm text-fg-muted">Memuat...</p>
        </div>
    </div>
</x-ui.field>
