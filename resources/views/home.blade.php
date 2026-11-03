@extends('layouts.app')

@section('title', 'Beranda | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $store = store();
        $whatsappNumber = $store && !empty($store->whatsapp) ? preg_replace('/[^0-9]/', '', $store->whatsapp) : null;
    @endphp

    <h1 class="sr-only">Belanja produk UMKM Indonesia di {{ $store->store_name ?? 'instanweb' }}</h1>

    {{-- Hero carousel --}}
    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8" aria-label="Sorotan toko">
        <div data-hero-carousel class="relative overflow-hidden rounded-panel border border-line bg-inverse shadow-sm">
            <div class="relative h-[420px] sm:h-[440px] lg:h-[480px]" aria-roledescription="carousel"
                aria-label="Sorotan produk dan layanan">

                @php
                    $slides = [
                        [
                            'image' => 'images/gambar1.jpeg',
                            'eyebrow' => 'UMKM lokal',
                            'title' => 'UMKM Lokal, Pasar Nasional',
                            'description' => 'Temukan produk pilihan dari usaha kecil di berbagai daerah Indonesia.',
                            'alt' => 'Produk UMKM lokal Indonesia',
                        ],
                        [
                            'image' => 'images/gambar2.jpeg',
                            'eyebrow' => 'Dukung usaha kecil',
                            'title' => 'Dukung Produk Lokal',
                            'description' => 'Belanja produk unik dan dukung pertumbuhan UMKM Indonesia.',
                            'alt' => 'Aneka produk buatan UMKM',
                        ],
                        [
                            'image' => 'images/gambar3.jpeg',
                            'eyebrow' => 'Belanja online',
                            'title' => 'Belanja dari Mana Saja',
                            'description' => 'Jelajahi produk daerah dan pesan dengan mudah secara online.',
                            'alt' => 'Pengemasan produk untuk pengiriman',
                        ],
                    ];
                @endphp

                @foreach ($slides as $slide)
                    <div data-slide
                        class="absolute inset-0 transition-opacity duration-500 {{ $loop->first ? 'opacity-100' : 'pointer-events-none opacity-0' }}"
                        aria-hidden="{{ $loop->first ? 'false' : 'true' }}" @if (!$loop->first) inert @endif>
                        <img src="{{ asset($slide['image']) }}" alt="{{ $slide['alt'] }}"
                            class="absolute inset-0 h-full w-full object-cover"
                            @if ($loop->first) fetchpriority="high" @endif>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/60 to-black/20"
                            aria-hidden="true">
                        </div>

                        <div class="relative flex h-full items-end p-6 pb-16 sm:items-center sm:p-10 sm:pb-10 lg:p-14">
                            <div class="max-w-xl">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                                    <x-ui.icon name="sparkles" class="h-3.5 w-3.5" />
                                    {{ $slide['eyebrow'] }}
                                </span>

                                <p
                                    class="mt-4 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                                    {{ $slide['title'] }}
                                </p>
                                <p class="mt-3 max-w-md text-sm leading-relaxed text-white/85 sm:text-base">
                                    {{ $slide['description'] }}
                                </p>

                                <div class="mt-6 flex flex-wrap items-center gap-3">
                                    <x-ui.button :href="route('products')" size="lg">
                                        Lihat Produk
                                        <x-ui.icon name="arrow-right" class="h-4 w-4" />
                                    </x-ui.button>

                                    @if ($whatsappNumber)
                                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-field border border-white/35 bg-white/10 px-5 text-base font-semibold text-white backdrop-blur transition hover:bg-white/20">
                                            <x-ui.icon name="chat" class="h-4 w-4" /> Hubungi Toko
                                        </a>
                                    @else
                                        <x-ui.button :href="route('kontak')" variant="outline" size="lg"
                                            class="border-white/35 bg-white/10 text-white hover:bg-white/20">
                                            Hubungi Toko
                                        </x-ui.button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Kontrol carousel --}}
                <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-4 p-4 sm:p-6">
                    <div class="flex items-center gap-2" role="group" aria-label="Pilih sorotan">
                        @foreach ($slides as $slide)
                            <button type="button" data-slide-dot
                                class="h-2.5 rounded-full border border-white/60 transition-all duration-200 {{ $loop->first ? 'w-7 bg-white' : 'w-2.5 bg-white/40 hover:bg-white/70' }}"
                                aria-label="Tampilkan sorotan {{ $loop->iteration }}"
                                aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" data-slide-prev
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-black/30 text-white backdrop-blur transition hover:bg-black/50">
                            <x-ui.icon name="chevron-left" class="h-5 w-5" />
                            <span class="sr-only">Sorotan sebelumnya</span>
                        </button>
                        <button type="button" data-slide-next
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-black/30 text-white backdrop-blur transition hover:bg-black/50">
                            <x-ui.icon name="chevron-right" class="h-5 w-5" />
                            <span class="sr-only">Sorotan berikutnya</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Nilai layanan --}}
    <section class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Kenapa belanja di sini">
        <ul class="grid gap-3 sm:grid-cols-3">
            <li class="rounded-card border border-line bg-surface p-5 shadow-sm">
                <span
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-soft text-brand-text"
                    aria-hidden="true">
                    <x-ui.icon name="sparkles" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold text-fg">Produk UMKM pilihan</p>
                <p class="mt-1 text-xs leading-relaxed text-fg-muted">Dikurasi dari pelaku usaha lokal di berbagai daerah.
                </p>
            </li>
            <li class="rounded-card border border-line bg-surface p-5 shadow-sm">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-soft text-info"
                    aria-hidden="true">
                    <x-ui.icon name="shopping-bag" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold text-fg">Checkout mudah</p>
                <p class="mt-1 text-xs leading-relaxed text-fg-muted">Masuk, isi alamat pengiriman, lalu konfirmasi
                    pesanan.</p>
            </li>
            <li class="rounded-card border border-line bg-surface p-5 shadow-sm">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-soft text-success"
                    aria-hidden="true">
                    <x-ui.icon name="chat" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold text-fg">Tanya langsung</p>
                <p class="mt-1 text-xs leading-relaxed text-fg-muted">
                    @if ($whatsappNumber)
                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer"
                            class="font-medium text-info underline underline-offset-2">Hubungi penjual via WhatsApp</a>
                    @else
                        <a href="{{ route('kontak') }}" class="font-medium text-info underline underline-offset-2">Lihat
                            kanal kontak toko</a>
                    @endif
                    untuk pertanyaan sebelum membeli.
                </p>
            </li>
        </ul>
    </section>

    {{-- Produk terbaru --}}
    <section class="mx-auto mt-12 max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="produk-pilihan">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Katalog</p>
                <h2 id="produk-pilihan" class="mt-1 text-2xl font-bold tracking-tight text-fg sm:text-3xl">Produk Pilihan
                    UMKM
                </h2>
                <p class="mt-1 text-sm text-fg-muted">Produk terbaru dari toko kami.</p>
            </div>

            <a href="{{ route('products') }}"
                class="inline-flex min-h-11 items-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                Lihat semua produk
                <x-ui.icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            @empty
                <x-ui.empty-state class="col-span-full" title="Belum ada produk tersedia"
                    description="Produk akan tampil di sini setelah penjual menambahkannya." icon="package" />
            @endforelse
        </div>
    </section>

    {{-- Blok toko --}}
    @if ($store)
        <section class="mx-auto mt-12 max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="tentang-toko">
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-panel border border-line bg-surface p-6 shadow-sm lg:col-span-2 lg:p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Tentang toko</p>
                    <h2 id="tentang-toko" class="mt-1 text-2xl font-bold tracking-tight text-fg">
                        {{ $store->store_name ?? 'Toko kami' }}
                    </h2>
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-fg-secondary">
                        {{ $store->description ?? 'Toko UMKM yang menjual produk pilihan dengan layanan langsung dari penjual.' }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <x-ui.button :href="route('kontak')" variant="outline">
                            <x-ui.icon name="chat" class="h-4 w-4" /> Hubungi Toko
                        </x-ui.button>
                        <x-ui.button :href="route('selamatdatang')" variant="ghost">
                            Kenali instanweb
                        </x-ui.button>
                    </div>
                </div>

                <div class="rounded-panel border border-line bg-surface p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-fg">Informasi</h3>
                    <dl class="mt-4 space-y-4 text-sm">
                        @if ($store->address)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Alamat</dt>
                                <dd class="mt-1 text-fg-secondary">{{ $store->address }}</dd>
                            </div>
                        @endif
                        @if ($whatsappNumber)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">WhatsApp</dt>
                                <dd class="mt-1">
                                    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium text-info underline underline-offset-2">{{ $store->whatsapp }}</a>
                                </dd>
                            </div>
                        @endif
                        @if ($store->instagram)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Instagram</dt>
                                <dd class="mt-1">
                                    <a href="https://instagram.com/{{ ltrim($store->instagram, '@') }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium text-info underline underline-offset-2">{{ $store->instagram }}</a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </section>
    @endif
    @guest
        {{-- CTA penutup --}}
        <section class="mx-auto mt-12 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-panel border border-line bg-inverse p-6 text-fg-inverse shadow-sm sm:p-10">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-xl">
                        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Siap mulai berbelanja?</h2>
                        <p class="mt-2 text-sm leading-relaxed text-white/70">
                            Masuk ke akun Anda untuk menyimpan riwayat pesanan dan melanjutkan checkout. Belum punya akun?
                            Daftar terlebih dahulu.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="flex flex-wrap gap-3">
                            <x-ui.button :href="route('register.form')" size="lg">Daftar Akun</x-ui.button>
                            <x-ui.button :href="route('login.form')" variant="outline" size="lg"
                                class="border-white/30 bg-white/10 text-white hover:bg-white/20">Masuk</x-ui.button>
                        </div>
                    </div>
                </div>
        </section>
    @endguest
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('[data-hero-carousel]');

            if (!carousel) {
                return;
            }

            const slides = Array.from(carousel.querySelectorAll('[data-slide]'));
            const dots = Array.from(carousel.querySelectorAll('[data-slide-dot]'));
            const prev = carousel.querySelector('[data-slide-prev]');
            const next = carousel.querySelector('[data-slide-next]');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (slides.length < 2) {
                return;
            }

            let index = 0;
            let timer = null;
            let paused = false;
            let touchStartX = null;

            function show(target) {
                index = (target + slides.length) % slides.length;

                slides.forEach(function(slide, i) {
                    const active = i === index;

                    slide.classList.toggle('opacity-100', active);
                    slide.classList.toggle('opacity-0', !active);
                    slide.classList.toggle('pointer-events-none', !active);
                    slide.setAttribute('aria-hidden', active ? 'false' : 'true');

                    if (active) {
                        slide.removeAttribute('inert');
                    } else {
                        slide.setAttribute('inert', '');
                    }
                });

                dots.forEach(function(dot, i) {
                    const active = i === index;

                    dot.classList.toggle('w-7', active);
                    dot.classList.toggle('bg-white', active);
                    dot.classList.toggle('w-2.5', !active);
                    dot.classList.toggle('bg-white/40', !active);
                    dot.setAttribute('aria-current', active ? 'true' : 'false');
                });
            }

            function stop() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            function start() {
                stop();

                if (reduceMotion || paused) {
                    return;
                }

                timer = setInterval(function() {
                    show(index + 1);
                }, 4500);
            }

            prev?.addEventListener('click', function() {
                show(index - 1);
                start();
            });

            next?.addEventListener('click', function() {
                show(index + 1);
                start();
            });

            dots.forEach(function(dot, i) {
                dot.addEventListener('click', function() {
                    show(i);
                    start();
                });
            });

            carousel.addEventListener('mouseenter', function() {
                paused = true;
                stop();
            });

            carousel.addEventListener('mouseleave', function() {
                paused = false;
                start();
            });

            carousel.addEventListener('focusin', function() {
                paused = true;
                stop();
            });

            carousel.addEventListener('focusout', function(event) {
                if (!carousel.contains(event.relatedTarget)) {
                    paused = false;
                    start();
                }
            });

            carousel.addEventListener('touchstart', function(event) {
                touchStartX = event.changedTouches[0].clientX;
            }, {
                passive: true
            });

            carousel.addEventListener('touchend', function(event) {
                if (touchStartX === null) {
                    return;
                }

                const distance = event.changedTouches[0].clientX - touchStartX;

                if (Math.abs(distance) > 40) {
                    show(distance > 0 ? index - 1 : index + 1);
                    start();
                }

                touchStartX = null;
            }, {
                passive: true
            });

            show(0);
            start();
        });
    </script>
@endpush
