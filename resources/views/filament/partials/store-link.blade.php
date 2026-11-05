{{-- Akses cepat ke storefront agar admin dapat meninjau tampilan toko. --}}
@php
    $activeStore = auth()->user()?->store ?: store();
    $storefrontUrl = $activeStore ? route('store.show', $activeStore) : route('home');
@endphp
<a
    class="iw-topbar-link"
    href="{{ $storefrontUrl }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Lihat toko di tab baru"
>
    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" />
    <span>Lihat Toko</span>
</a>
