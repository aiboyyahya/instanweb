<x-filament-panels::page>
    <section class="iw-dash-hero" aria-labelledby="iw-dashboard-greeting">
        <div class="iw-dash-hero__content">
            <p class="iw-dash-eyebrow">
                <x-filament::icon icon="heroicon-m-sparkles" />
                Panel Toko instanweb
            </p>

            <h2 id="iw-dashboard-greeting" class="iw-dash-title">
                Halo, {{ $firstName }}.
            </h2>

            <p class="iw-dash-copy">
                Kelola katalog, pesanan, dan konten toko dalam satu tempat. Semua data di bawah
                berasal langsung dari aktivitas toko Anda.
            </p>

            <div class="iw-dash-meta">
                <span class="iw-dash-pill">
                    {{ $storeName ?: 'Nama toko belum diatur' }}
                </span>

                <a
                    class="iw-dash-link"
                    href="{{ $storeUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Lihat toko
                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" />
                </a>
            </div>
        </div>

        <div class="iw-dash-quick" aria-label="Aksi cepat">
            <p class="iw-dash-quick__label">Aksi cepat</p>

            @foreach ($quickActions as $action)
                <a class="iw-quick-action" href="{{ $action['url'] }}">
                    <span class="iw-quick-action__icon" aria-hidden="true">
                        <x-filament::icon :icon="$action['icon']" />
                    </span>
                    <span>
                        <span class="iw-quick-action__title">{{ $action['label'] }}</span>
                        <span class="iw-quick-action__desc">{{ $action['description'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <div class="iw-dash-section-head">
        <h3 id="iw-dashboard-summary">Ringkasan toko</h3>
        <p>Statistik dan pesanan terbaru dihitung langsung dari aktivitas toko Anda.</p>
    </div>

    {{ $this->content }}
</x-filament-panels::page>
