@extends('layouts.app')

@section('title', $product->product_name . ' | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $stock = (int) ($product->stock ?? 0);
        $price = $product->purchase_price ?? 0;
        $avg = (float) ($avgRating ?? 0);
        $count = (int) ($ratingCount ?? 0);
        $mainImage = $product->image ? asset('storage/' . $product->image) : asset('images/gambar3.jpeg');
        $gallery = $product->images ?? collect();
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 pb-24 sm:px-6 lg:px-8 lg:py-10 lg:pb-10">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Produk', 'url' => route('products')],
            ['label' => $product->product_name],
        ]" />

        <div class="mt-6 grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)] lg:gap-10">
            {{-- Galeri & ulasan --}}
            <div class="space-y-8">
                <section aria-label="Galeri produk" class="rounded-panel border border-line bg-surface p-4 shadow-sm sm:p-6">
                    <div class="overflow-hidden rounded-xl border border-line bg-surface-soft">
                        <img id="main-product-image" src="{{ $mainImage }}" alt="{{ $product->product_name }}"
                            class="aspect-square w-full object-contain p-4 sm:p-8">
                    </div>

                    @if ($gallery->count())
                        <div class="mt-4 flex flex-wrap gap-3" role="group" aria-label="Pilih gambar produk">
                            <button type="button" data-gallery-thumb data-src="{{ $mainImage }}"
                                class="h-16 w-16 overflow-hidden rounded-lg border-2 border-cta bg-surface-soft p-1 transition"
                                aria-label="Tampilkan gambar utama" aria-pressed="true">
                                <img src="{{ $mainImage }}" alt="" class="h-full w-full rounded object-cover">
                            </button>

                            @foreach ($gallery as $img)
                                @php $thumb = asset('storage/' . $img->image_file); @endphp
                                <button type="button" data-gallery-thumb data-src="{{ $thumb }}"
                                    class="h-16 w-16 overflow-hidden rounded-lg border-2 border-line bg-surface-soft p-1 transition hover:border-line-strong"
                                    aria-label="Tampilkan gambar {{ $loop->iteration }}"
                                    aria-pressed="false">
                                    <img src="{{ $thumb }}" alt="" class="h-full w-full rounded object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Ulasan --}}
                <section aria-labelledby="ulasan-produk"
                    class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 id="ulasan-produk" class="text-lg font-semibold text-fg">Ulasan Produk</h2>
                            <p class="mt-1 text-sm text-fg-muted">
                                {{ $count > 0 ? 'Rata-rata ' . number_format($avg, 1, ',', '.') . ' dari 5' : 'Belum ada penilaian' }}
                            </p>
                        </div>

                        <x-ui.rating-stars :rating="$avg" :count="$count" size="md" show-value />
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($ratings as $rating)
                            <article class="rounded-card border border-line bg-surface-muted p-4">
                                <div class="flex gap-3">
                                    @if ($rating->customer && $rating->customer->profile_photo)
                                        <img src="{{ asset('storage/' . $rating->customer->profile_photo) }}" alt=""
                                            class="h-10 w-10 shrink-0 rounded-full object-cover">
                                    @else
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-surface-soft text-sm font-bold text-fg-secondary"
                                            aria-hidden="true">
                                            {{ strtoupper(substr($rating->customer->name ?? 'P', 0, 1)) }}
                                        </span>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="text-sm font-semibold text-fg">
                                                {{ $rating->customer->name ?? 'Pengguna' }}
                                            </p>
                                            <x-ui.rating-stars :rating="$rating->rating" />
                                        </div>

                                        @if ($rating->comment)
                                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-fg-secondary">
                                                {{ $rating->comment }}</p>
                                        @else
                                            <p class="mt-2 text-sm italic text-fg-muted">Tanpa komentar.</p>
                                        @endif

                                        @if ($rating->image)
                                            <img src="{{ asset('storage/' . $rating->image) }}"
                                                alt="Foto ulasan dari {{ $rating->customer->name ?? 'pengguna' }}"
                                                class="mt-3 h-24 w-24 rounded-lg border border-line object-cover"
                                                loading="lazy">
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <x-ui.empty-state title="Belum ada ulasan" icon="chat"
                                description="Ulasan akan muncul setelah pembeli menyelesaikan pesanan dan mengirim penilaian." />
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Panel pembelian --}}
            <div id="panel-beli" class="scroll-mt-24 lg:sticky lg:top-24 lg:h-fit">
                <div class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6">
                    @if ($product->category)
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">
                            {{ $product->category->category_name }}</p>
                    @endif

                    <h1 class="mt-1 text-2xl font-bold leading-tight tracking-tight text-fg sm:text-3xl">
                        {{ $product->product_name }}
                    </h1>

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <x-ui.rating-stars :rating="$avg" :count="$count" size="md" show-value />
                        <span class="text-xs text-fg-muted">{{ $count }} ulasan</span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-fg">
                        Rp {{ number_format($price, 0, ',', '.') }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if ($stock > 0)
                            <x-ui.badge :variant="$stock <= 5 ? 'warning' : 'success'">
                                Stok tersedia: {{ $stock }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="danger">Stok habis</x-ui.badge>
                        @endif
                    </div>

                    @if ($product->description)
                        <div class="mt-5 border-t border-line pt-5">
                            <h2 class="text-sm font-semibold text-fg">Deskripsi</h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-fg-secondary">
                                {{ $product->description }}
                            </p>
                        </div>
                    @endif

                    {{-- Jumlah --}}
                    <div class="mt-6">
                        <label for="product-quantity" class="block text-sm font-medium text-fg-secondary">Jumlah</label>

                        <div class="mt-2 flex items-center gap-3">
                            <button type="button" id="decrement-btn" @disabled($stock <= 0)
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line-strong bg-surface text-fg transition hover:bg-surface-soft disabled:pointer-events-none disabled:opacity-40"
                                aria-label="Kurangi jumlah">
                                <x-ui.icon name="minus" class="h-4 w-4" />
                            </button>

                            <input type="number" id="product-quantity" value="1" min="1"
                                max="{{ max($stock, 1) }}" readonly
                                class="h-11 w-16 rounded-field border border-line-strong bg-surface text-center text-base font-semibold text-fg"
                                aria-live="polite">

                            <button type="button" id="increment-btn" @disabled($stock <= 0)
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line-strong bg-surface text-fg transition hover:bg-surface-soft disabled:pointer-events-none disabled:opacity-40"
                                aria-label="Tambah jumlah">
                                <x-ui.icon name="plus" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Aksi pembelian --}}
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <form id="buy-now-form" method="GET" action="{{ route('checkout.page') }}">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="buy-now-qty" value="1">
                            <input type="hidden" name="direct" value="1">
                            <button type="submit" @disabled($stock <= 0)
                                class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-field bg-cta px-4 text-sm font-semibold text-on-cta shadow-sm transition hover:bg-cta-hover disabled:pointer-events-none disabled:opacity-50">
                                <x-ui.icon name="shopping-bag" class="h-4 w-4" />
                                {{ $stock > 0 ? 'Beli Sekarang' : 'Stok habis' }}
                            </button>
                        </form>

                        <form id="add-to-cart-form" method="POST" action="{{ route('addToCart') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="addcart-qty" value="1">
                            <button type="submit" @disabled($stock <= 0)
                                class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft disabled:pointer-events-none disabled:opacity-50">
                                <x-ui.icon name="package" class="h-4 w-4" />
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>

                    @guest
                        <p class="mt-3 text-xs leading-relaxed text-fg-muted">
                            Anda perlu <a href="{{ route('login.form') }}"
                                class="font-semibold text-info underline underline-offset-2">masuk</a> untuk menambahkan
                            produk ke keranjang atau melakukan checkout.
                        </p>
                    @endguest

                    <div class="mt-6 space-y-3 border-t border-line pt-5 text-sm text-fg-secondary">
                        <div class="flex items-start gap-3">
                            <x-ui.icon name="shield" class="mt-0.5 h-5 w-5 text-success" />
                            <div>
                                <p class="font-semibold text-fg">Pengembalian barang</p>
                                <p class="text-fg-muted">Gratis pengembalian dalam 30 hari.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-ui.icon name="truck" class="mt-0.5 h-5 w-5 text-info" />
                            <div>
                                <p class="font-semibold text-fg">Pengiriman</p>
                                <p class="text-fg-muted">Kurir dipilih saat checkout.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bar aksi mobile --}}
    <div
        class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-surface/95 p-3 backdrop-blur lg:hidden">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-xs text-fg-muted">{{ $product->product_name }}</p>
                <p class="text-lg font-bold text-fg">Rp {{ number_format($price, 0, ',', '.') }}</p>
            </div>
            <a href="#panel-beli"
                class="inline-flex min-h-11 items-center justify-center rounded-field bg-cta px-5 text-sm font-semibold text-on-cta">
                {{ $stock > 0 ? 'Beli Sekarang' : 'Stok habis' }}
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inc = document.getElementById('increment-btn');
            const dec = document.getElementById('decrement-btn');
            const qtyInput = document.getElementById('product-quantity');
            const buyQty = document.getElementById('buy-now-qty');
            const cartQty = document.getElementById('addcart-qty');

            if (qtyInput) {
                const max = parseInt(qtyInput.getAttribute('max'), 10) || 1;

                function sync() {
                    if (buyQty) buyQty.value = qtyInput.value;
                    if (cartQty) cartQty.value = qtyInput.value;
                }

                inc?.addEventListener('click', function () {
                    const value = parseInt(qtyInput.value, 10) || 1;
                    if (value < max) {
                        qtyInput.value = value + 1;
                    }
                    sync();
                });

                dec?.addEventListener('click', function () {
                    const value = parseInt(qtyInput.value, 10) || 1;
                    if (value > 1) {
                        qtyInput.value = value - 1;
                    }
                    sync();
                });

                sync();
            }

            // Galeri: tombol thumbnail mengganti gambar utama.
            const mainImage = document.getElementById('main-product-image');
            const thumbs = document.querySelectorAll('[data-gallery-thumb]');

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    if (!mainImage) {
                        return;
                    }

                    mainImage.src = thumb.dataset.src;

                    thumbs.forEach(function (item) {
                        item.setAttribute('aria-pressed', item === thumb ? 'true' : 'false');
                        item.classList.toggle('border-cta', item === thumb);
                        item.classList.toggle('border-line', item !== thumb);
                    });
                });
            });
        });
    </script>
@endpush
