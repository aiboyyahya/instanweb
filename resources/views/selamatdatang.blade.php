@extends('layouts.app')

@section('title', 'InstanWeb — Toko Online Instan untuk UMKM')

@push('head')
    <meta name="description"
        content="InstanWeb membantu UMKM membuat toko online sendiri dengan katalog rapi, checkout mudah, dan panel toko dalam satu tempat.">
    <noscript>
        <style>
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
            }
        </style>
    </noscript>
@endpush

@section('content')
    @php
        $isAuthed = auth()->check();
        $isAdmin = $isAuthed && auth()->user()->role === 'admin';
        $hasStore = $isAuthed && auth()->user()->store;

        $advantages = [
            [
                'icon' => 'sparkles',
                'tone' => 'bg-brand-soft text-brand-text',
                'title' => 'Siap dipakai',
                'description' => 'Struktur toko sudah tersedia. Tinggal lengkapi identitas dan produk.',
            ],
            [
                'icon' => 'package',
                'tone' => 'bg-info-soft text-info',
                'title' => 'Katalog rapi',
                'description' => 'Produk, kategori, dan pencarian tersusun agar pembeli cepat menemukan.',
            ],
            [
                'icon' => 'shopping-bag',
                'tone' => 'bg-success-soft text-success',
                'title' => 'Checkout jelas',
                'description' => 'Alamat, ringkasan pesanan, dan konfirmasi dalam alur yang sederhana.',
            ],
            [
                'icon' => 'shield',
                'tone' => 'bg-brand-soft text-brand-text',
                'title' => 'Panel terpusat',
                'description' => 'Kelola produk, pesanan, dan artikel dari satu dashboard.',
            ],
            [
                'icon' => 'phone',
                'tone' => 'bg-info-soft text-info',
                'title' => 'Mobile-first',
                'description' => 'Tampilan tetap rapi dan nyaman dipakai dari ponsel.',
            ],
            [
                'icon' => 'chat',
                'tone' => 'bg-success-soft text-success',
                'title' => 'Kontak langsung',
                'description' => 'Pembeli dapat menghubungi toko lewat kanal yang tersedia.',
            ],
        ];

        $testimonials = [
            [
                'quote' =>
                    'Toko saya punya satu tautan yang rapi untuk dibagikan. Pembeli bisa melihat katalog tanpa harus bertanya satu per satu.',
                'role' => 'Pemilik toko kuliner',
                'context' => 'Katalog dan pesanan',
                'position' => 'fade-right',
            ],
            [
                'quote' =>
                    'Alur belanjanya jelas di ponsel. Dari keranjang sampai ringkasan pesanan, semuanya mudah dicek.',
                'role' => 'Pembeli',
                'context' => 'Pengalaman checkout',
                'position' => 'fade-up',
            ],
            [
                'quote' => 'Produk, pesanan, dan artikel ada di panel yang sama. Pekerjaan harian jadi lebih teratur.',
                'role' => 'Pemilik toko fashion',
                'context' => 'Panel toko',
                'position' => 'fade-left',
            ],
        ];
    @endphp

    <div class="iw-landing-page">
        {{-- Hero --}}
        <section class="iw-landing-hero relative overflow-hidden" aria-labelledby="hero-title">
            <div class="iw-landing-grid pointer-events-none absolute inset-0" aria-hidden="true"></div>

            <div
                class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 pb-16 pt-10 sm:px-6 sm:pb-20 sm:pt-14 lg:grid-cols-[1.04fr_0.96fr] lg:gap-16 lg:px-8 lg:pb-24 lg:pt-20">
                <div data-aos="fade-up">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-brand/20 bg-brand-soft px-3.5 py-1.5 text-xs font-semibold text-brand-text">
                        <x-ui.icon name="sparkles" class="h-3.5 w-3.5" />
                        Website instan untuk UMKM
                    </span>

                    <h1 id="hero-title"
                        class="mt-5 text-4xl font-extrabold leading-[1.05] tracking-[-0.045em] text-fg sm:text-5xl lg:text-[3.65rem]">
                        Toko online Anda,
                        <span class="mt-1 block text-brand-text">siap dalam hitungan menit.</span>
                    </h1>

                    <p class="mt-5 max-w-xl text-base leading-relaxed text-fg-secondary sm:text-lg">
                        InstanWeb membantu UMKM punya etalase digital sendiri: katalog rapi, checkout mudah, dan panel toko
                        dalam satu tempat.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        @guest
                            <x-ui.button :href="route('home')" size="lg" class="iw-btn-feedback shadow-md">
                                <x-ui.icon name="shopping-bag" class="h-5 w-5" /> Mulai Belanja
                            </x-ui.button>

                            <x-ui.button href="#tentang" variant="outline" size="lg" class="iw-btn-feedback">
                                Kenali Instanweb<x-ui.icon name="chevron-right"/>
                            </x-ui.button>
                        @else
                            @if ($isAdmin)
                                <x-ui.button :href="url('/admin')" size="lg" class="iw-btn-feedback shadow-md">
                                    <x-ui.icon name="shield" class="h-5 w-5" /> Buka Panel Admin
                                </x-ui.button>

                                <x-ui.button :href="route('home')" variant="outline" size="lg" class="iw-btn-feedback">
                                    <x-ui.icon name="home" class="h-5 w-5" /> Lihat Beranda
                                </x-ui.button>
                            @else
                                <x-ui.button :href="route('home')" size="lg" class="iw-btn-feedback shadow-md">
                                    <x-ui.icon name="home" class="h-5 w-5" /> Lanjut ke Beranda
                                </x-ui.button>

                                <x-ui.button :href="$hasStore ? route('profil') : route('register.store.form')" variant="outline" size="lg" class="iw-btn-feedback">
                                    <x-ui.icon name="shield" class="h-5 w-5" />
                                    {{ $hasStore ? 'Kelola Toko' : 'Buka Toko' }}
                                </x-ui.button>
                            @endif
                        @endguest
                    </div>

                    <ul class="mt-7 flex flex-wrap gap-x-5 gap-y-2 text-xs font-medium text-fg-muted">
                        <li class="flex items-center gap-1.5">
                            <x-ui.icon name="check" class="h-4 w-4 text-success" /> Tanpa coding
                        </li>
                        <li class="flex items-center gap-1.5">
                            <x-ui.icon name="check" class="h-4 w-4 text-success" /> Mobile-first
                        </li>
                        <li class="flex items-center gap-1.5">
                            <x-ui.icon name="check" class="h-4 w-4 text-success" /> Panel toko terpusat
                        </li>
                    </ul>

                    <a href="#tentang"
                        class="group mt-8 inline-flex items-center gap-2 text-sm font-semibold text-fg-secondary transition hover:text-brand-text">
                        Kenali InstanWeb
                        <x-ui.icon name="chevron-down"
                            class="h-4 w-4 transition-transform duration-200 group-hover:translate-y-0.5" />
                    </a>
                </div>

                {{-- Preview toko --}}
                <div class="relative mx-auto w-full max-w-[34rem] lg:max-w-none" data-aos="fade-left" data-aos-delay="120"
                    aria-hidden="true">
                    <div class="absolute -inset-6 rounded-[2.5rem] bg-brand/10 blur-3xl"></div>

                    <div class="relative overflow-hidden rounded-panel border border-line bg-inverse p-4 shadow-lg sm:p-5">
                        <div class="flex items-center gap-2 border-b border-white/10 pb-4">
                            <span class="h-2.5 w-2.5 rounded-full bg-danger"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-warning"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-success"></span>
                            <span
                                class="ml-2 flex min-w-0 flex-1 items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-[10px] font-medium text-white/50">
                                <x-ui.icon name="shield" class="h-3 w-3 shrink-0" />
                                <span class="truncate">instanweb.app/toko-anda</span>
                            </span>
                            <span
                                class="hidden rounded-full border border-success/25 bg-success/15 px-2 py-1 text-[10px] font-semibold text-success sm:inline-flex">
                                Aktif
                            </span>
                        </div>

                        <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-inverse shadow-sm">
                                    <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="24"
                                        height="24" class="h-6 w-6" decoding="async">
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">Toko Anda</p>
                                    <p class="text-[10px] text-white/50">Etalase UMKM</p>
                                </div>
                                <span
                                    class="ml-auto rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[10px] font-semibold text-white/80">
                                    Katalog
                                </span>
                            </div>

                            <div class="mt-4 overflow-hidden rounded-xl bg-gradient-to-br from-brand to-brand-hover p-4">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-white/60">
                                    Koleksi pilihan
                                </p>
                                <p class="mt-1 max-w-[16rem] text-lg font-bold leading-snug text-white">
                                    Produk UMKM, langsung dari tokonya.
                                </p>
                                <span
                                    class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-[11px] font-semibold text-brand-text">
                                    Jelajahi katalog
                                    <x-ui.icon name="arrow-right" class="h-3 w-3" />
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <div class="rounded-xl border border-white/10 bg-white/[0.04] p-2">
                                    <div
                                        class="flex aspect-[4/3] items-center justify-center rounded-lg bg-gradient-to-br from-brand-soft via-surface to-brand-soft text-brand-text">
                                        <x-ui.icon name="package" class="h-7 w-7" />
                                    </div>
                                    <p class="mt-2 truncate text-[11px] font-semibold text-white">Produk baru</p>
                                    <p class="text-[10px] text-white/50">Siap dipajang</p>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/[0.04] p-2">
                                    <div
                                        class="flex aspect-[4/3] items-center justify-center rounded-lg bg-gradient-to-br from-info-soft via-surface to-brand-soft text-info">
                                        <x-ui.icon name="shopping-bag" class="h-7 w-7" />
                                    </div>
                                    <p class="mt-2 truncate text-[11px] font-semibold text-white">Katalog toko</p>
                                    <p class="text-[10px] text-white/50">Rapi dan mudah dicari</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="iw-float absolute -bottom-6 left-2 w-52 rounded-xl border border-line bg-surface p-3.5 shadow-lg sm:-left-6">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-success-soft text-success">
                                <x-ui.icon name="check-circle" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-fg">Pesanan diproses</p>
                                <p class="text-[10px] text-fg-muted">Status terlihat di panel</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="iw-float iw-float-delayed absolute -right-3 -top-4 hidden items-center gap-2 rounded-full border border-line bg-surface px-3 py-2 text-[11px] font-semibold text-fg-secondary shadow-md sm:flex">
                        <x-ui.icon name="phone" class="h-3.5 w-3.5 text-brand-text" />
                        Tampilan mobile-first
                    </div>
                </div>
            </div>
        </section>

        {{-- Tentang --}}
        <section id="tentang" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
            aria-labelledby="tentang-title">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:gap-16">
                <div data-aos="fade-right">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-text">Tentang InstanWeb</p>

                    <h2 id="tentang-title"
                        class="mt-3 max-w-xl text-3xl font-extrabold leading-tight tracking-[-0.035em] text-fg sm:text-4xl">
                        Etalase digital yang dibuat untuk UMKM.
                    </h2>

                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-fg-secondary sm:text-base">
                        InstanWeb menyatukan etalase toko, katalog produk, keranjang, dan pengelolaan pesanan dalam satu
                        pengalaman yang ringan. Dibuat untuk pelaku usaha yang ingin langsung jualan tanpa repot teknis.
                    </p>

                    <x-ui.button :href="$hasStore ? route('profil') : route('register.store.form')" variant="outline" class="iw-btn-feedback mt-6">
                        <x-ui.icon name="arrow-right" class="h-4 w-4" />
                        {{ $hasStore ? 'Kelola Toko Saya' : 'Mulai Buka Toko' }}
                    </x-ui.button>
                </div>

                <div class="grid gap-3 sm:grid-cols-2" data-aos="fade-left">
                    <div class="rounded-panel border border-line bg-surface p-6 shadow-sm sm:col-span-2">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <x-brand-wordmark size="lg" />
                            <x-ui.badge variant="info">Satu tempat</x-ui.badge>
                        </div>

                        <p class="mt-5 text-lg font-semibold text-fg">Dari katalog sampai pesanan.</p>
                        <p class="mt-1 max-w-xl text-sm leading-relaxed text-fg-muted">
                            Pembeli melihat produk dan checkout dengan jelas. Pemilik toko mengelola semuanya dari panel
                            yang
                            sama.
                        </p>
                    </div>

                    <div class="iw-lift rounded-card border border-line bg-surface p-5 shadow-sm">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-soft text-brand-text">
                            <x-ui.icon name="external" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-semibold text-fg">Satu tautan</p>
                        <p class="mt-1 text-xs leading-relaxed text-fg-muted">Alamat toko yang siap dibagikan ke pelanggan.
                        </p>
                    </div>

                    <div class="iw-lift rounded-card border border-line bg-surface p-5 shadow-sm">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-info-soft text-info">
                            <x-ui.icon name="phone" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-semibold text-fg">Mobile-first</p>
                        <p class="mt-1 text-xs leading-relaxed text-fg-muted">Nyaman dipakai dari ponsel, tablet, dan
                            desktop.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Keunggulan --}}
        <section id="keunggulan" class="mx-auto max-w-7xl scroll-mt-24 px-4 pb-16 sm:px-6 sm:pb-20 lg:px-8"
            aria-labelledby="keunggulan-title">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-text">Keunggulan</p>
                <h2 id="keunggulan-title"
                    class="mt-3 text-3xl font-extrabold leading-tight tracking-[-0.035em] text-fg sm:text-4xl">
                    Semua yang dibutuhkan untuk mulai jualan.
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-fg-secondary sm:text-base">
                    Fokus pada produk dan pelanggan. Bagian teknis toko dibuat sederhana.
                </p>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($advantages as $index => $advantage)
                    <article class="iw-lift group rounded-card border border-line bg-surface p-5 shadow-sm"
                        data-aos="fade-up" data-aos-delay="{{ min($index * 70, 280) }}">
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full transition-transform duration-200 group-hover:-translate-y-0.5 group-hover:scale-110 {{ $advantage['tone'] }}">
                            <x-ui.icon :name="$advantage['icon']" class="h-5 w-5" />
                        </span>

                        <h3 class="mt-4 text-base font-bold text-fg">{{ $advantage['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-fg-muted">{{ $advantage['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Testimoni --}}
        <section id="testimoni" class="border-y border-line bg-surface py-16 sm:py-20" aria-labelledby="testimoni-title">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-text">Testimoni</p>
                    <h2 id="testimoni-title"
                        class="mt-3 text-3xl font-extrabold leading-tight tracking-[-0.035em] text-fg sm:text-4xl">
                        Pengalaman yang ingin kami hadirkan.
                    </h2>
                    {{-- <p class="mt-3 text-sm leading-relaxed text-fg-secondary sm:text-base">
                    Sederhana bagi pembeli, teratur bagi pemilik toko.
                </p> --}}
                </div>

                <div class="mt-10 grid gap-4 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <figure
                            class="iw-lift flex h-full flex-col rounded-panel border border-line bg-surface-muted p-6 shadow-sm"
                            data-aos="{{ $testimonial['position'] }}">
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-soft text-brand-text">
                                <x-ui.icon name="chat" class="h-5 w-5" />
                            </span>

                            <blockquote class="mt-4 flex-1 text-sm leading-relaxed text-fg-secondary">
                                “{{ $testimonial['quote'] }}”
                            </blockquote>

                            <figcaption class="mt-6 border-t border-line pt-4">
                                <p class="text-sm font-semibold text-fg">{{ $testimonial['role'] }}</p>
                                <p class="mt-0.5 text-xs text-fg-muted">{{ $testimonial['context'] }}</p>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Pilihan masuk --}}
        <section id="masuk" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
            aria-labelledby="masuk-title">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-text">Masuk</p>
                <h2 id="masuk-title"
                    class="mt-3 text-3xl font-extrabold leading-tight tracking-[-0.035em] text-fg sm:text-4xl">
                    Pilih jalur yang paling sesuai.
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-fg-secondary sm:text-base">
                    Satu pintu login, Sistem mengarahkan Anda ke halaman yang tepat.
                </p>
            </div>

            <div class="mt-10 grid gap-4 lg:grid-cols-2">
                {{-- Pembeli --}}
                <article class="iw-lift rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-8"
                    data-aos="fade-right">
                    <div class="flex items-start justify-between gap-4">
                        <span
                            class="inline-flex h-12 w-12 items-center justify-center rounded-card bg-brand-soft text-brand-text">
                            <x-ui.icon name="shopping-bag" class="h-6 w-6" />
                        </span>
                        <x-ui.badge variant="neutral">Untuk pembeli</x-ui.badge>
                    </div>

                    <h3 class="mt-5 text-xl font-bold text-fg">Belanja produk UMKM</h3>
                    <p class="mt-2 max-w-md text-sm leading-relaxed text-fg-secondary">
                        Temukan produk pilihan, simpan pesanan, dan beri ulasan dari satu akun.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        @guest
                            <x-ui.button :href="route('login.form')" class="iw-btn-feedback">
                                <x-ui.icon name="user" class="h-4 w-4" /> Masuk
                            </x-ui.button>

                            <x-ui.button :href="route('register.form')" variant="outline" class="iw-btn-feedback">
                                Daftar Pembeli
                            </x-ui.button>
                        @else
                            <x-ui.button :href="route('home')" class="iw-btn-feedback">
                                <x-ui.icon name="home" class="h-4 w-4" /> Lanjut ke Beranda
                            </x-ui.button>

                            <x-ui.button :href="route('orders')" variant="outline" class="iw-btn-feedback">
                                <x-ui.icon name="package" class="h-4 w-4" /> Pesanan Saya
                            </x-ui.button>
                        @endguest
                    </div>
                </article>

                {{-- Admin / pemilik toko --}}
                <article
                    class="iw-lift relative overflow-hidden rounded-panel border border-white/10 bg-inverse p-6 shadow-lg sm:p-8"
                    data-aos="fade-left">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-44 w-44 rounded-full bg-brand/20 blur-3xl"
                        aria-hidden="true"></div>

                    <div class="relative flex items-start justify-between gap-4">
                        <span
                            class="inline-flex h-12 w-12 items-center justify-center rounded-card bg-white/10 text-white">
                            <x-ui.icon name="shield" class="h-6 w-6" />
                        </span>
                        <span
                            class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-xs font-semibold text-white/80">
                            Panel Toko
                        </span>
                    </div>

                    <h3 class="relative mt-5 text-xl font-bold text-white">Admin / Pemilik Toko</h3>
                    <p class="relative mt-2 max-w-md text-sm leading-relaxed text-white/70">
                        Kelola katalog, pesanan, dan konten toko dari satu dashboard yang ringkas.
                    </p>

                    <div class="relative mt-6 flex flex-wrap gap-3">
                        @guest
                            <x-ui.button :href="url('/admin')" class="iw-btn-feedback !bg-white !text-inverse hover:!bg-white/90">
                                <x-ui.icon name="shield" class="h-4 w-4" /> Masuk Admin
                            </x-ui.button>

                            <x-ui.button :href="route('register.store.form')" variant="outline"
                                class="iw-btn-feedback !border-white/20 !bg-white/10 !text-white hover:!bg-white/15">
                                Buka Toko
                            </x-ui.button>
                        @elseif ($isAdmin)
                            <x-ui.button :href="url('/admin')" class="iw-btn-feedback !bg-white !text-inverse hover:!bg-white/90">
                                <x-ui.icon name="shield" class="h-4 w-4" /> Buka Panel Admin
                            </x-ui.button>

                            <x-ui.button :href="route('profil')" variant="outline"
                                class="iw-btn-feedback !border-white/20 !bg-white/10 !text-white hover:!bg-white/15">
                                Pengaturan Toko
                            </x-ui.button>
                        @else
                            <x-ui.button :href="$hasStore ? route('profil') : route('register.store.form')" class="iw-btn-feedback !bg-white !text-inverse hover:!bg-white/90">
                                <x-ui.icon name="shield" class="h-4 w-4" />
                                {{ $hasStore ? 'Kelola Toko' : 'Buka Toko' }}
                            </x-ui.button>

                            <x-ui.button :href="route('home')" variant="outline"
                                class="iw-btn-feedback !border-white/20 !bg-white/10 !text-white hover:!bg-white/15">
                                Lihat Beranda
                            </x-ui.button>
                        @endguest
                    </div>
                </article>
            </div>
        </section>

        {{-- Penutup --}}
        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 sm:pb-20 lg:px-8">
            <div class="iw-lift relative overflow-hidden rounded-panel bg-inverse p-8 text-white shadow-lg sm:p-10 lg:p-12"
                data-aos="fade-up">
                <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand/25 blur-3xl"
                    aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-brand-sky/10 blur-3xl"
                    aria-hidden="true"></div>

                <div class="relative grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center lg:gap-10">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-glow">Mulai sekarang</p>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-[-0.035em] text-white sm:text-3xl">
                            Siap punya toko online sendiri?
                        </h2>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/70">
                            Lengkapi identitas toko, unggah produk, dan bagikan satu tautan ke pelanggan.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @guest
                            <x-ui.button :href="route('register.store.form')" class="iw-btn-feedback !bg-white !text-inverse hover:!bg-white/90">
                                <x-ui.icon name="shield" class="h-4 w-4" /> Buka Toko
                            </x-ui.button>

                            <x-ui.button :href="route('home')" variant="outline"
                                class="iw-btn-feedback !border-white/20 !bg-white/10 !text-white hover:!bg-white/15">
                                Mulai Belanja
                            </x-ui.button>
                        @else
                            <x-ui.button :href="$isAdmin ? url('/admin') : route('home')" class="iw-btn-feedback !bg-white !text-inverse hover:!bg-white/90">
                                <x-ui.icon :name="$isAdmin ? 'shield' : 'home'" class="h-4 w-4" />
                                {{ $isAdmin ? 'Buka Panel Admin' : 'Lanjut ke Beranda' }}
                            </x-ui.button>

                            <x-ui.button :href="$hasStore ? route('profil') : route('register.store.form')" variant="outline"
                                class="iw-btn-feedback !border-white/20 !bg-white/10 !text-white hover:!bg-white/15">
                                {{ $hasStore ? 'Kelola Toko' : 'Buka Toko' }}
                            </x-ui.button>
                        @endguest
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
