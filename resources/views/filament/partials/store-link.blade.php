{{-- Akses cepat ke storefront agar admin dapat meninjau tampilan toko. --}}
<a
    class="iw-topbar-link"
    href="{{ route('home') }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Lihat toko di tab baru"
>
    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" />
    <span>Lihat Toko</span>
</a>
