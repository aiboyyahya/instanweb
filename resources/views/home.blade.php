@extends('layouts.app')

@section('title', 'Beranda InstanWeb')

@section('content')
    <section class="mx-4 bg-slate-950 pt-8 pb-14 md:mx-6">
        <div class="relative w-full overflow-hidden rounded-3xl shadow-xl">
            <div id="slider" class="slider flex transition-transform duration-700 ease-in-out">
                <div class="slide relative w-full flex-shrink-0">
                    <img src="{{ asset('images/gambar1.jpeg') }}"
                        alt="Produk UMKM lokal Indonesia"
                        class="h-64 w-full rounded-2xl object-cover md:h-80 lg:h-96">

                    <div class="absolute inset-0 flex flex-col items-center justify-center rounded-2xl bg-black/50 px-4 text-center text-white">
                        <h2 class="mb-3 text-3xl font-extrabold drop-shadow-lg md:text-5xl">
                            UMKM Lokal, Pasar Nasional
                        </h2>
                        <p class="text-base font-semibold opacity-90 md:text-xl">
                            Temukan produk pilihan dari usaha kecil di berbagai daerah Indonesia.
                        </p>
                    </div>
                </div>

                <div class="slide relative w-full flex-shrink-0">
                    <img src="{{ asset('images/gambar2.jpeg') }}"
                        alt="Aneka produk buatan UMKM"
                        class="h-64 w-full rounded-2xl object-cover md:h-80 lg:h-96">

                    <div class="absolute inset-0 flex flex-col items-center justify-center rounded-2xl bg-black/50 px-4 text-center text-white">
                        <h2 class="mb-3 text-3xl font-extrabold drop-shadow-lg md:text-5xl">
                            Dukung Produk Lokal
                        </h2>
                        <p class="text-base font-semibold opacity-90 md:text-xl">
                            Belanja produk unik dan dukung pertumbuhan UMKM Indonesia.
                        </p>
                    </div>
                </div>

                <div class="slide relative w-full flex-shrink-0">
                    <img src="{{ asset('images/gambar3.jpeg') }}"
                        alt="Pengemasan produk untuk pengiriman"
                        class="h-64 w-full rounded-2xl object-cover md:h-80 lg:h-96">

                    <div class="absolute inset-0 flex flex-col items-center justify-center rounded-2xl bg-black/50 px-4 text-center text-white">
                        <h2 class="mb-3 text-3xl font-extrabold drop-shadow-lg md:text-5xl">
                            Belanja dari Mana Saja
                        </h2>
                        <p class="text-base font-semibold opacity-90 md:text-xl">
                            Jelajahi produk daerah dan pesan dengan mudah secara online.
                        </p>
                    </div>
                </div>
            </div>

            <button id="btn-prev" type="button" aria-label="Slide sebelumnya"
                class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/80 p-3 opacity-0 shadow-md transition hover:bg-white pointer-events-none">
                <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button id="btn-next" type="button" aria-label="Slide berikutnya"
                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/80 p-3 opacity-0 shadow-md transition hover:bg-white pointer-events-none">
                <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
                <button type="button" class="indicator h-3 w-3 rounded-full bg-white" aria-label="Tampilkan slide 1"></button>
                <button type="button" class="indicator h-3 w-3 rounded-full bg-white/50" aria-label="Tampilkan slide 2"></button>
                <button type="button" class="indicator h-3 w-3 rounded-full bg-white/50" aria-label="Tampilkan slide 3"></button>
            </div>
        </div>
    </section>

    <section class="mx-4 border-y border-slate-800 bg-slate-950 py-12 md:mx-6 md:py-16">
        <div class="mx-auto max-w-full px-4 md:px-8 lg:px-16">
            <h2 class="mb-10 text-center text-3xl font-extrabold tracking-tight md:mb-14 md:text-4xl">
                Produk Pilihan UMKM
            </h2>

            <div class="grid grid-cols-2 gap-5 sm:gap-6 md:grid-cols-3 lg:grid-cols-4 lg:gap-8">
                @forelse ($products as $product)
                    <div class="group flex flex-col overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <a href="{{ route('product.show', $product->id) }}" class="relative block overflow-hidden">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/gambar3.jpeg') }}"
                                alt="{{ $product->product_name }}"
                                class="h-44 w-full object-cover transition duration-500 group-hover:scale-105 md:h-56">
                        </a>

                        <div class="flex flex-1 flex-col p-4 text-left md:p-5">
                            <h3 class="mb-2 line-clamp-2 text-base font-bold text-slate-100 md:text-lg">
                                {{ $product->product_name }}
                            </h3>

                            @if (($product->ratingCount ?? 0) > 0)
                                <div class="mb-2 flex items-center gap-1">
                                    @php
                                        $avg = (int) round($product->avgRating ?? 0);
                                    @endphp

                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $avg ? 'text-yellow-400' : 'text-gray-300' }}"
                                            fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148c.429 0 .81-.275.95-.69l1.286-3.947z" />
                                        </svg>
                                    @endfor

                                    <span class="text-xs text-slate-400">({{ $product->ratingCount }})</span>
                                </div>
                            @endif

                            @if (!empty($product->description))
                                <p class="mb-3 line-clamp-2 text-sm text-slate-400">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <p class="mb-5 mt-auto pt-2 text-lg font-extrabold tracking-tight text-slate-100 md:text-xl">
                                Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                            </p>

                            <form action="{{ route('addToCart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">

                                <button type="submit"
                                    class="w-full rounded-xl bg-gray-900 py-2.5 text-sm font-semibold text-white transition hover:bg-black hover:shadow-md active:scale-95 md:text-base">
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full py-10 text-center text-slate-400">
                        Belum ada produk tersedia.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.getElementById('slider');
            const slides = document.querySelectorAll('.slide');
            const indicators = document.querySelectorAll('.indicator');
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');

            if (!slider || slides.length === 0) {
                return;
            }

            let currentSlide = 0;
            let userInteracted = false;
            let hideButtonsTimeout;

            function showSlide(index) {
                currentSlide = (index + slides.length) % slides.length;
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;

                indicators.forEach(function (indicator, i) {
                    indicator.classList.toggle('bg-white', i === currentSlide);
                    indicator.classList.toggle('bg-white/50', i !== currentSlide);
                });
            }

            function showButtons() {
                userInteracted = true;

                [btnPrev, btnNext].forEach(function (button) {
                    if (button) {
                        button.classList.remove('opacity-0', 'pointer-events-none');
                    }
                });

                clearTimeout(hideButtonsTimeout);

                hideButtonsTimeout = setTimeout(function () {
                    userInteracted = false;

                    [btnPrev, btnNext].forEach(function (button) {
                        if (button) {
                            button.classList.add('opacity-0', 'pointer-events-none');
                        }
                    });
                }, 2500);
            }

            if (btnPrev) {
                btnPrev.addEventListener('click', function () {
                    showButtons();
                    showSlide(currentSlide - 1);
                });
            }

            if (btnNext) {
                btnNext.addEventListener('click', function () {
                    showButtons();
                    showSlide(currentSlide + 1);
                });
            }

            indicators.forEach(function (indicator, index) {
                indicator.addEventListener('click', function () {
                    showButtons();
                    showSlide(index);
                });
            });

            slider.addEventListener('mouseenter', showButtons);
            showSlide(0);

            if (slides.length > 1) {
                setInterval(function () {
                    if (!userInteracted) {
                        showSlide(currentSlide + 1);
                    }
                }, 4000);
            }
        });
    </script>
@endsection
