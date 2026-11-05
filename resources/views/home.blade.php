@extends('layouts.app')

@section('title', 'Beranda | ' . config('app.name', 'instanweb'))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.page-header eyebrow="Direktori toko" title="Temukan Toko UMKM"
            description="Jelajahi website toko milik penjual UMKM. Pilih satu toko untuk melihat produk dan bertransaksi langsung dengan pemiliknya.">
            {{-- <x-ui.button :href="route('products')" variant="outline">
                <x-ui.icon name="package" class="h-4 w-4" /> Semua Produk
            </x-ui.button> --}}

            @auth
                @if (auth()->user()->role === 'user')
                    <x-ui.button :href="route('register.store.form')">
                        <x-ui.icon name="shield" class="h-4 w-4" /> Buka Toko
                    </x-ui.button>
                @endif
            @else
                <x-ui.button :href="route('register.store.form')">
                    <x-ui.icon name="shield" class="h-4 w-4" /> Buka Toko
                </x-ui.button>
            @endauth
        </x-ui.page-header>

        <form method="GET" action="{{ route('home') }}" role="search"
            class="mt-6 flex flex-col gap-3 rounded-panel border border-line bg-surface p-4 shadow-sm sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-fg-muted">
                    <x-ui.icon name="search" class="h-4 w-4" />
                </span>
                <label for="store-search" class="sr-only">Cari toko</label>
                <input type="search" id="store-search" name="q" value="{{ $search }}"
                    placeholder="Cari nama toko, alamat, atau deskripsi..."
                    class="h-12 w-full rounded-field border border-line-strong bg-surface pl-11 pr-4 text-sm text-fg placeholder:text-fg-muted"
                    autocomplete="off">
            </div>

            <div class="flex gap-2">
                <x-ui.button type="submit">
                    <x-ui.icon name="search" class="h-4 w-4" /> Cari Toko
                </x-ui.button>

                @if ($search !== '')
                    <x-ui.button :href="route('home')" variant="outline">Reset</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-8">
            @if ($stores->isEmpty())
                @if ($search !== '')
                    <x-ui.empty-state title="Toko tidak ditemukan" icon="search"
                        description="Coba kata kunci lain atau reset pencarian untuk melihat seluruh direktori toko.">
                        <x-ui.button :href="route('home')" variant="outline">Reset pencarian</x-ui.button>
                    </x-ui.empty-state>
                @else
                    <x-ui.empty-state title="Belum ada toko" icon="shield"
                        description="Toko UMKM yang bergabung akan tampil di direktori ini.">
                        <x-ui.button :href="route('register.store.form')">Buka Toko Pertama</x-ui.button>
                    </x-ui.empty-state>
                @endif
            @else
                <p class="text-sm text-fg-muted" aria-live="polite">
                    @if ($search !== '')
                        Hasil pencarian <span class="font-semibold text-fg">“{{ $search }}”</span> —
                    @endif
                    Menampilkan <span class="font-semibold text-fg">{{ $stores->count() }}</span> toko UMKM.
                </p>

                <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($stores as $store)
                        @php
                            $banner = $store->banner_1
                                ? asset('storage/' . $store->banner_1)
                                : asset('images/gambar1.jpeg');
                            $storeUrl = route('store.show', $store);
                        @endphp

                        <a href="{{ $storeUrl }}"
                            class="iw-lift group flex flex-col overflow-hidden rounded-panel border border-line bg-surface shadow-sm transition hover:border-cta/40"
                            aria-label="Kunjungi toko {{ $store->store_name }}">
                            <div class="relative h-36 overflow-hidden bg-inverse sm:h-40">
                                <img src="{{ $banner }}" alt="Banner {{ $store->store_name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                    loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                                    aria-hidden="true"></div>

                                <div class="absolute inset-x-4 bottom-4 flex items-end gap-3">
                                    @if ($store->logo)
                                        <img src="{{ asset('storage/' . $store->logo) }}"
                                            alt="Logo {{ $store->store_name }}"
                                            class="h-14 w-14 shrink-0 rounded-2xl border-2 border-white/80 bg-surface object-cover shadow-lg">
                                    @else
                                        <span
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-white/80 bg-white/15 text-xl font-extrabold text-white shadow-lg backdrop-blur"
                                            aria-hidden="true">
                                            {{ strtoupper(substr($store->store_name, 0, 1)) }}
                                        </span>
                                    @endif

                                    <div class="min-w-0 pb-0.5">
                                        <p class="truncate text-lg font-bold leading-tight text-white">
                                            {{ $store->store_name }}
                                        </p>
                                        <p class="mt-1 flex items-center gap-1.5 text-xs text-white/80">
                                            <x-ui.icon name="package" class="h-3.5 w-3.5" />
                                            {{ $store->products_count }} produk aktif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <p class="line-clamp-2 text-sm leading-relaxed text-fg-secondary">
                                    {{ $store->description ?? 'Toko UMKM yang menjual produk pilihan dengan layanan langsung dari penjual.' }}
                                </p>

                                @if ($store->address)
                                    <p class="mt-3 flex items-start gap-2 text-xs text-fg-muted">
                                        <x-ui.icon name="home" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
                                        <span class="line-clamp-2">{{ $store->address }}</span>
                                    </p>
                                @endif

                                <span
                                    class="mt-auto inline-flex items-center gap-2 pt-4 text-sm font-semibold text-cta">
                                    Kunjungi Toko
                                    <x-ui.icon name="arrow-right"
                                        class="h-4 w-4 transition group-hover:translate-x-0.5" />
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @guest
            <section class="mt-12 overflow-hidden rounded-panel border border-line bg-inverse p-6 text-fg-inverse shadow-sm sm:p-10">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-xl">
                        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Punya usaha? Buka toko online Anda.</h2>
                        <p class="mt-2 text-sm leading-relaxed text-white/70">
                            Daftarkan toko, unggah logo dan banner, lalu kelola produk serta pesanan dari satu panel.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <x-ui.button :href="route('register.store.form')" size="lg">Daftar Toko</x-ui.button>
                        <x-ui.button :href="route('login.form')" variant="outline" size="lg"
                            class="border-white/30 bg-white/10 text-white hover:bg-white/20">Masuk</x-ui.button>
                    </div>
                </div>
            </section>
        @endguest
    </div>
@endsection
