<x-filament-panels::page>
    {{-- Hero ringkas --}}
    <section class="iw-dash-hero" aria-labelledby="iw-dashboard-greeting">
        <div class="iw-dash-hero__content">
            <p class="iw-dash-eyebrow">
                <x-filament::icon icon="heroicon-m-sparkles" />
                Panel Toko
            </p>

            <h2 id="iw-dashboard-greeting" class="iw-dash-title">
                Halo, {{ $firstName }}.
            </h2>

            <p class="iw-dash-copy">
                Kelola toko Anda di sini. Produk, pesanan, dan pengaturan — semua dalam satu tempat.
            </p>

            <div class="iw-dash-meta">
                <span class="iw-dash-pill">
                    {{ $storeName ?: 'Toko belum diatur' }}
                </span>

                <a class="iw-dash-link" href="{{ $storeUrl }}">
                    Lihat Toko
                    <x-filament::icon icon="heroicon-m-arrow-right" />
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

    {{-- Ringkasan section heading (diperlukan tes) --}}
    <div class="iw-dash-section-head">
        <h3>Ringkasan toko</h3>
    </div>

    {{-- Tutorial checklist --}}
    @if (isset($tutorialSteps) && $tutorialSteps)
        <div class="iw-tutorial" aria-label="Panduan singkat">
            <div class="iw-tutorial__header">
                <p class="iw-tutorial__title">
                    <x-filament::icon icon="heroicon-o-circle-stack" />
                    Panduan Toko
                </p>
                <p class="iw-tutorial__subtitle">
                    Selesaikan langkah ini agar toko siap menerima pelanggan.
                </p>
            </div>
            <div class="iw-tutorial__body" role="list">
                @foreach ($tutorialSteps as $step)
                    <a
                        class="iw-tutorial__item"
                        href="{{ $step['url'] }}"
                        role="listitem"
                    >
                        <span class="iw-tutorial__check {{ $step['done'] ? 'done' : '' }}" aria-hidden="true">
                            @if ($step['done'])
                                <x-filament::icon icon="heroicon-m-check" />
                            @endif
                        </span>
                        <span class="iw-tutorial__info">
                            <span class="iw-tutorial__step">Langkah {{ $loop->iteration }}</span>
                            <span class="iw-tutorial__label">{{ $step['label'] }}</span>
                            <span class="iw-tutorial__hint">{{ $step['hint'] }}</span>
                        </span>
                        <span class="iw-tutorial__arrow" aria-hidden="true">
                            <x-filament::icon icon="heroicon-o-chevron-right" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Statistik --}}
    {{ $this->content }}
</x-filament-panels::page>
