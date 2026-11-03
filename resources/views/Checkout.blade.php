@extends('layouts.app')

@section('title', 'Checkout | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $items = isset($directCheckout) && ! empty($directCheckout) ? $directCheckout : $cart;
        $hasZeroStock = false;
        $subtotal = 0;

        foreach ($items as $id => $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $productModel = \App\Models\Product::find($id);
            $currentStock = $productModel ? $productModel->stock : ($item['stock'] ?? 0);

            if ($currentStock <= 0 || $currentStock < $item['quantity']) {
                $hasZeroStock = true;
            }
        }

        $inputClass =
            'h-12 w-full rounded-field border bg-surface px-3 text-sm text-fg placeholder:text-fg-muted focus:border-info';
        $inputNormalClass = 'border-line-strong';    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.page-header eyebrow="Langkah 2 dari 3" title="Checkout"
            description="Lengkapi data pengiriman. Pastikan nomor telepon dan alamat sudah benar.">
            <x-ui.button :href="route('cart')" variant="outline">
                <x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali ke Keranjang
            </x-ui.button>
        </x-ui.page-header>

        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] lg:items-start">
            <form id="checkout-form" action="{{ route('checkout') }}" method="POST" class="space-y-5">
                @csrf

                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="info-pengiriman">
                    <div class="flex items-center gap-2">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-soft text-sm font-bold text-fg">1</span>
                        <h2 id="info-pengiriman" class="text-lg font-semibold text-fg">Informasi Pengiriman</h2>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <x-ui.field label="Nama Penerima" name="recipient_name" required>
                            <input type="text" id="recipient_name" name="recipient_name" required
                                autocomplete="name" value="{{ old('recipient_name', Auth::user()->name ?? '') }}"
                                placeholder="Nama lengkap penerima"
                                @class([$inputClass, $inputNormalClass])>
                        </x-ui.field>

                        <x-ui.field label="Nomor Telepon" name="phone_number" required
                            hint="Gunakan format 08xxxx atau 62xxxx untuk konfirmasi via WhatsApp.">
                            <input type="tel" id="phone_number" name="phone_number" required inputmode="numeric"
                                autocomplete="tel" value="{{ old('phone_number', Auth::user()->phone_number ?? '') }}"
                                placeholder="Contoh: 08123456789"
                                @class([$inputClass, $inputNormalClass])>
                        </x-ui.field>

                        <div class="sm:col-span-2">
                            <x-ui.field label="Alamat Lengkap" name="address" required
                                hint="Sertakan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, dan provinsi.">
                                <textarea id="address" name="address" rows="3" required autocomplete="street-address"
                                    placeholder="Contoh: Jl. Merdeka No. 10, RT 02/RW 05, Kel. Sukamaju, Kec. Cilodong, Depok, Jawa Barat"
                                    @class([
                                        'w-full rounded-field border bg-surface px-3 py-3 text-sm text-fg placeholder:text-fg-muted focus:border-info',
                                        $inputNormalClass,
                                    ])>{{ old('address', Auth::user()->address ?? '') }}</textarea>
                            </x-ui.field>
                        </div>

                        <x-ui.field label="Kurir Pengiriman" name="courier" required>
                            <select id="courier" name="courier" required @class([$inputClass, $inputNormalClass])>
                                <option value="">Pilih kurir</option>
                                <option value="JNE" @selected(old('courier') === 'JNE')>JNE</option>
                                <option value="J&T" @selected(old('courier') === 'J&T')>J&T Express</option>
                                <option value="Sicepat" @selected(old('courier') === 'Sicepat')>SiCepat</option>
                                <option value="AnterAja" @selected(old('courier') === 'AnterAja')>AnterAja</option>
                                <option value="COD / Kurir Toko" @selected(old('courier') === 'COD / Kurir Toko')>COD / Kurir
                                    Toko</option>
                            </select>
                        </x-ui.field>

                        <x-ui.field label="Kode Pos" name="postal_code" required>
                            <input type="text" id="postal_code" name="postal_code" required inputmode="numeric"
                                autocomplete="postal-code" value="{{ old('postal_code') }}" placeholder="Contoh: 16415"
                                @class([$inputClass, $inputNormalClass])>
                        </x-ui.field>

                        <div class="sm:col-span-2">
                            <x-ui.field label="Catatan Pesanan" name="notes"
                                hint="Opsional. Misalnya permintaan khusus atau patokan alamat.">
                                <textarea id="notes" name="notes" rows="3" placeholder="Tambahkan catatan untuk penjual..."
                                    @class([
                                        'w-full rounded-field border border-line-strong bg-surface px-3 py-3 text-sm text-fg placeholder:text-fg-muted focus:border-info',
                                    ])>{{ old('notes') }}</textarea>
                            </x-ui.field>
                        </div>
                    </div>

                    {{-- Ongkir manual: nilai tetap 0, dihitung bersama penjual. --}}
                    <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">
                </section>

                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="metode-pembayaran">
                    <div class="flex items-center gap-2">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-soft text-sm font-bold text-fg">2</span>
                        <h2 id="metode-pembayaran" class="text-lg font-semibold text-fg">Metode Pembayaran</h2>
                    </div>

                    <div class="mt-4 rounded-card border border-info/30 bg-info-soft p-4">
                        <div class="flex items-start gap-3">
                            <x-ui.icon name="info" class="mt-0.5 h-5 w-5 text-info" />
                            <div class="text-sm leading-relaxed text-info">
                                <p class="font-semibold">Konfirmasi pembayaran bersama penjual</p>
                                <p class="mt-1">
                                    Setelah pesanan dibuat, penjual akan menghubungi Anda melalui WhatsApp untuk
                                    rincian pembayaran dan pengiriman.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                @if ($hasZeroStock)
                    <x-ui.alert variant="danger" title="Stok tidak mencukupi">
                        Beberapa produk di keranjang tidak tersedia atau stoknya kurang. Hapus produk tersebut dari
                        keranjang sebelum melanjutkan.
                    </x-ui.alert>
                @endif

                <p class="text-xs leading-relaxed text-fg-muted">
                    Dengan menekan tombol konfirmasi, pesanan akan dibuat dengan status menunggu. Tidak ada pembayaran
                    yang diproses otomatis di halaman ini.
                </p>
            </form>

            {{-- Ringkasan pesanan --}}
            <aside class="space-y-5 lg:sticky lg:top-24" aria-labelledby="ringkasan-pesanan">
                <div class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6">
                    <h2 id="ringkasan-pesanan" class="text-lg font-semibold text-fg">Detail Produk</h2>

                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($items as $id => $item)
                            @php
                                $productModel = \App\Models\Product::find($id);
                                $currentStock = $productModel ? $productModel->stock : ($item['stock'] ?? 0);
                                $image = ! empty($item['image'])
                                    ? asset('storage/' . $item['image'])
                                    : asset('images/gambar3.jpeg');
                            @endphp

                            <li class="flex gap-3 py-3 first:pt-0">
                                <img src="{{ $image }}" alt=""
                                    class="h-16 w-16 shrink-0 rounded-lg border border-line object-cover">

                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-fg">{{ $item['name'] }}</p>
                                    <p class="mt-1 text-xs text-fg-muted">
                                        {{ $item['quantity'] }} × Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </p>

                                    @if ($currentStock <= 0)
                                        <x-ui.badge variant="danger" class="mt-1.5">Stok habis</x-ui.badge>
                                    @elseif ($currentStock < $item['quantity'])
                                        <x-ui.badge variant="warning" class="mt-1.5">Stok tersisa {{ $currentStock }}
                                        </x-ui.badge>
                                    @endif
                                </div>

                                <p class="text-sm font-semibold text-fg">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>

                    <dl class="mt-4 space-y-2 border-t border-line pt-4 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-fg-muted">Subtotal</dt>
                            <dd class="font-medium text-fg">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-fg-muted">Ongkos kirim</dt>
                            <dd class="font-medium text-fg">Dikonfirmasi penjual</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                            <dt class="font-medium text-fg">Total pembayaran</dt>
                            <dd class="text-xl font-bold text-fg">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <button type="submit" form="checkout-form" @disabled($hasZeroStock)
                        class="mt-5 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-field bg-cta px-4 text-sm font-semibold text-on-cta shadow-sm transition hover:bg-cta-hover disabled:pointer-events-none disabled:opacity-50">
                        <x-ui.icon name="check" class="h-4 w-4" />
                        {{ $hasZeroStock ? 'Stok tidak mencukupi' : 'Konfirmasi Pesanan' }}
                    </button>

                    <x-ui.button :href="route('cart')" variant="ghost" block class="mt-2">
                        Ubah Keranjang
                    </x-ui.button>
                </div>

                <x-ui.alert variant="info" title="Data Anda aman">
                    Alamat dan nomor telepon hanya digunakan untuk memproses pesanan ini.
                </x-ui.alert>
            </aside>
        </div>
    </div>
@endsection
