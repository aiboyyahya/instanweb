@extends('layouts.app')

@section('title', 'Keranjang Belanja | ' . (store()->store_name ?? 'instanweb'))

@php
    $activeStore = store_context();
    $productsUrl = $activeStore ? route('store.products', $activeStore) : route('products');
    $homeUrl = $activeStore ? route('store.show', $activeStore) : route('home');
@endphp

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.page-header eyebrow="Langkah 1 dari 3" title="Keranjang Belanja"
            description="Tinjau produk pilihan Anda sebelum melanjutkan ke checkout.">
            <x-ui.button :href="$productsUrl" variant="outline">
                <x-ui.icon name="arrow-left" class="h-4 w-4" /> Lanjut Belanja
            </x-ui.button>
        </x-ui.page-header>

        @if (count($cart) > 0)
            @php
                $total = 0;
                $totalQuantity = 0;

                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                    $totalQuantity += $item['quantity'];
                }
            @endphp

            <div class="mt-8 grid gap-6 lg:grid-cols-3 lg:items-start">
                <div class="space-y-4 lg:col-span-2">
                    @foreach ($cart as $id => $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $image = !empty($item['image'])
                                ? asset('storage/' . $item['image'])
                                : asset('images/gambar3.jpeg');
                        @endphp

                        <article class="rounded-panel border border-line bg-surface p-4 shadow-sm sm:p-5">
                            <div class="flex gap-4">
                                <a href="{{ route('product.show', $id) }}"
                                    class="h-24 w-24 shrink-0 overflow-hidden rounded-card border border-line bg-surface-soft sm:h-28 sm:w-28">
                                    <img src="{{ $image }}" alt="{{ $item['name'] }}"
                                        class="h-full w-full object-cover">
                                </a>

                                <div class="flex min-w-0 flex-1 flex-col gap-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h2 class="text-sm font-semibold leading-snug text-fg sm:text-base">
                                                <a href="{{ route('product.show', $id) }}"
                                                    class="transition hover:text-cta">{{ $item['name'] }}</a>
                                            </h2>
                                            <p class="mt-1 text-sm text-fg-muted">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }} / item
                                            </p>
                                        </div>

                                        <form action="{{ route('removeCart', $id) }}" method="POST"
                                            data-confirm="Hapus {{ $item['name'] }} dari keranjang?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-fg-muted transition hover:bg-danger-soft hover:text-danger"
                                                aria-label="Hapus {{ $item['name'] }} dari keranjang">
                                                <x-ui.icon name="trash" class="h-5 w-5" />
                                            </button>
                                        </form>
                                    </div>

                                    <div class="mt-auto flex flex-wrap items-end justify-between gap-3">
                                        {{-- Stepper jumlah --}}
                                        <form action="{{ route('updateCart', $id) }}" method="POST"
                                            id="quantity-form-{{ $id }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                    onclick="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                                    @disabled($item['quantity'] <= 1)
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line-strong bg-surface text-fg transition hover:bg-surface-soft disabled:pointer-events-none disabled:opacity-40"
                                                    aria-label="Kurangi jumlah {{ $item['name'] }}">
                                                    <x-ui.icon name="minus" class="h-4 w-4" />
                                                </button>

                                                <label for="quantity-{{ $id }}" class="sr-only">Jumlah
                                                    {{ $item['name'] }}</label>
                                                <input type="number" readonly name="quantity"
                                                    id="quantity-{{ $id }}" value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    class="h-10 w-14 rounded-field border border-line-strong bg-surface text-center text-sm font-semibold text-fg">

                                                <button type="button"
                                                    onclick="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line-strong bg-surface text-fg transition hover:bg-surface-soft"
                                                    aria-label="Tambah jumlah {{ $item['name'] }}">
                                                    <x-ui.icon name="plus" class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </form>

                                        <div class="text-right">
                                            <p class="text-xs text-fg-muted">Subtotal</p>
                                            <p class="text-base font-bold text-fg sm:text-lg">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <div
                        class="flex flex-wrap items-center justify-between gap-3 rounded-card border border-line bg-surface p-4 text-sm">
                        <p class="text-fg-muted">
                            Keranjang disimpan selama sesi browser Anda. Total
                            <span class="font-semibold text-fg">{{ $totalQuantity }}</span> item.
                        </p>
                        <a href="{{ $productsUrl }}"
                            class="font-semibold text-info underline underline-offset-2">Tambah produk lain</a>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <aside class="lg:sticky lg:top-24" aria-labelledby="ringkasan-belanja">
                    <div class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6">
                        <h2 id="ringkasan-belanja" class="text-lg font-semibold text-fg">Ringkasan Belanja</h2>

                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-fg-muted">Jumlah produk</dt>
                                <dd class="font-medium text-fg">{{ count($cart) }} jenis</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-fg-muted">Total kuantitas</dt>
                                <dd class="font-medium text-fg">{{ $totalQuantity }} item</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                                <dt class="font-medium text-fg">Total pembayaran</dt>
                                <dd class="text-xl font-bold text-fg">Rp {{ number_format($total, 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        <p class="mt-3 text-xs leading-relaxed text-fg-muted">
                            Ongkos kirim akan diinformasikan penjual saat konfirmasi pesanan.
                        </p>

                        <x-ui.button :href="route('checkout.page')" size="lg" block class="mt-5">
                            Checkout Sekarang
                            <x-ui.icon name="arrow-right" class="h-4 w-4" />
                        </x-ui.button>

                        <x-ui.button :href="$homeUrl" variant="outline" block class="mt-3">
                            Lanjut Belanja
                        </x-ui.button>
                    </div>
                </aside>
            </div>
        @else
            <div class="mt-8">
                <x-ui.empty-state title="Keranjang masih kosong"
                    description="Belum ada produk di keranjang belanja Anda. Yuk pilih produk favoritmu."
                    icon="shopping-bag">
                    <x-ui.button :href="$productsUrl" size="lg">Mulai Belanja</x-ui.button>
                    <x-ui.button :href="$homeUrl" variant="outline">Kembali ke Beranda</x-ui.button>
                </x-ui.empty-state>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) {
                return;
            }

            const form = document.getElementById('quantity-form-' + productId);
            const quantityInput = document.getElementById('quantity-' + productId);

            if (!form || !quantityInput) {
                return;
            }

            quantityInput.value = newQuantity;
            form.submit();
        }
    </script>
@endpush
