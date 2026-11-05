{{-- Akses cepat ke storefront agar admin dapat meninjau tampilan toko. --}}
@php
    $activeStore = auth()->user()?->store ?: store();
    $storefrontUrl = $activeStore ? route('store.show', $activeStore) : route('home');
@endphp
<a
    class="iw-topbar-link"
    href="{{ $storefrontUrl }}"
    data-iw-storefront-link
    aria-label="Lihat toko"
>
    <x-filament::icon icon="heroicon-o-building-storefront" />
    <span>Lihat Toko</span>
</a>
