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

                        <x-ui.field label="Alamat Lengkap" name="address"
                            hint="Opsional bila titik peta sudah cukup. Tambahkan patokan alamat bila diperlukan.">
                            <textarea id="address" name="address" rows="3"
                                autocomplete="street-address"
                                placeholder="Contoh: Jl. Merdeka No. 10, RT 02/RW 05, Kel. Sukamaju"
                                @class([
                                    'w-full rounded-field border bg-surface px-3 py-3 text-sm text-fg placeholder:text-fg-muted focus:border-info',
                                    $inputNormalClass,
                                ])>{{ old('address', Auth::user()->address ?? '') }}</textarea>
                        </x-ui.field>

                        <div class="sm:col-span-2">
                            <x-ui.map-picker label="Titik Pengiriman (Peta)" required
                                hint="Tandai lokasi pengiriman Anda pada peta. Ongkos kirim dihitung otomatis dari jarak lokasi toko ke titik ini."
                                :lat="old('latitude')" :lng="old('longitude')"
                                :origin-lat="$originLatitude ?? null" :origin-lng="$originLongitude ?? null"
                                :origin-label="$originStoreName ?? 'Lokasi toko'" />

                            @if (blank($originLatitude) || blank($originLongitude))
                                <x-ui.alert variant="warning" class="mt-3">
                                    Penjual belum mengatur titik lokasi toko. Ongkos kirim akan dikonfirmasi manual oleh
                                    penjual.
                                </x-ui.alert>
                            @endif

                            <p data-geo-hint class="mt-2 text-xs leading-relaxed text-fg-muted" aria-live="polite">
                                Klik peta untuk mengisi provinsi, kota, dan kecamatan secara otomatis.
                            </p>
                        </div>

                        <div class="grid gap-5 sm:col-span-2 sm:grid-cols-2 lg:grid-cols-3">
                            <x-ui.searchable-select label="Provinsi" name="province_id"
                                endpoint="{{ route('regions.provinces') }}" placeholder="Cari provinsi..."
                                :selected="old('province_id')" :selected-label="$selectedRegions['province'] ?? null"
                                empty-text="Provinsi tidak ditemukan." required />

                            <x-ui.searchable-select label="Kota/Kabupaten" name="city_id"
                                endpoint="{{ route('regions.regencies') }}" parent="province_id"
                                parent-param="province_id" placeholder="Cari kota atau kabupaten..."
                                :selected="old('city_id')" :selected-label="$selectedRegions['city'] ?? null"
                                empty-text="Kota/kabupaten tidak ditemukan."
                                parent-placeholder="Pilih provinsi terlebih dahulu." required />

                            <x-ui.searchable-select label="Kecamatan" name="district_id"
                                endpoint="{{ route('regions.districts') }}" parent="city_id" parent-param="city_id"
                                placeholder="Cari kecamatan..." :selected="old('district_id')"
                                :selected-label="$selectedRegions['district'] ?? null"
                                empty-text="Kecamatan tidak ditemukan."
                                parent-placeholder="Pilih kota/kabupaten terlebih dahulu." required />
                        </div>

                        <x-ui.field label="Kurir Pengiriman" name="courier" required
                            hint="Ongkos kirim otomatis menyesuaikan jarak dan tarif kurir yang dipilih.">
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

                        <x-ui.field label="Kode Pos" name="postal_code"
                            hint="Opsional. Dihitung otomatis dari titik pengiriman bila memungkinkan.">
                            <input type="text" id="postal_code" name="postal_code" inputmode="numeric"
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
                </section>

                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="metode-pembayaran">
                    <div class="flex items-center gap-2">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-soft text-sm font-bold text-fg">2</span>
                        <h2 id="metode-pembayaran" class="text-lg font-semibold text-fg">Metode Pembayaran</h2>
                    </div>

                    <div class="mt-5">
                        <x-ui.field label="Pilih metode pembayaran" name="payment_method" required
                            hint="Metode yang Anda pilih akan tercatat pada pesanan dan diteruskan ke penjual.">
                            <select id="payment_method" name="payment_method" required
                                @class([$inputClass, $inputNormalClass])>
                                <option value="transfer_bank" @selected(old('payment_method', 'transfer_bank') === 'transfer_bank')>
                                    Transfer Bank
                                </option>
                                <option value="cod" @selected(old('payment_method') === 'cod')>
                                    Bayar di Tempat (COD)
                                </option>
                                <option value="qris" @selected(old('payment_method') === 'qris')>
                                    QRIS / E-Wallet
                                </option>
                                <option value="manual" @selected(old('payment_method') === 'manual')>
                                    Konfirmasi Manual dengan Penjual
                                </option>
                            </select>
                        </x-ui.field>
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
                            <dt class="text-fg-muted">
                                Ongkos kirim
                                <span data-shipping-distance class="block text-xs font-normal text-fg-muted"></span>
                            </dt>
                            <dd class="text-right font-medium text-fg" data-shipping-cost>Pilih titik &amp; kurir</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                            <dt class="font-medium text-fg">Total pembayaran</dt>
                            <dd class="text-xl font-bold text-fg" data-order-total>
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </dd>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('checkout-form');
            if (!form) {
                return;
            }

            var picker = form.querySelector('[data-map-picker]');
            var shippingEl = document.querySelector('[data-shipping-cost]');
            var distanceEl = document.querySelector('[data-shipping-distance]');
            var totalEl = document.querySelector('[data-order-total]');
            var courierEl = document.getElementById('courier');
            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');
            var addressInput = document.getElementById('address');
            var postalCodeInput = document.getElementById('postal_code');
            var geocodeEndpoint = @json(route('regions.geocode'));
            var subtotal = {{ (float) $subtotal }};
            var endpoint = @json(route('checkout.shipping-estimate'));
            var tokenInput = form.querySelector('input[name="_token"]');
            var requestId = 0;
            var geocodeRequestId = 0;
            var isGeocoding = false;

            function rupiah(value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value));
            }

            function setSummary(distance, cost, courierLabel) {
                if (cost === null) {
                    shippingEl.textContent = 'Pilih titik & kurir';
                    distanceEl.textContent = '';
                    totalEl.textContent = rupiah(subtotal);
                    return;
                }

                shippingEl.textContent = rupiah(cost);

                var parts = [];
                if (courierLabel) {
                    parts.push(courierLabel);
                }
                if (distance !== null) {
                    parts.push('± ' + Number(distance).toFixed(2) + ' km dari toko');
                }

                distanceEl.textContent = parts.join(' · ');
                totalEl.textContent = rupiah(subtotal + cost);
            }

            function selectedCourier() {
                return courierEl ? courierEl.value : '';
            }

            function requestEstimate(lat, lng) {
                if (lat === null || lng === null || Number.isNaN(lat) || Number.isNaN(lng)) {
                    setSummary(null, null);
                    return;
                }

                var courier = selectedCourier();

                if (!courier) {
                    requestId++;
                    shippingEl.textContent = 'Pilih kurir';
                    distanceEl.textContent = 'Pilih kurir untuk menghitung ongkir';
                    totalEl.textContent = rupiah(subtotal);
                    return;
                }

                var currentId = ++requestId;

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': tokenInput ? tokenInput.value : '',
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng, courier: courier }),
                })
                    .then(function (response) {
                        return response.ok ? response.json() : null;
                    })
                    .then(function (data) {
                        if (!data || currentId !== requestId) {
                            return;
                        }

                        if (!data.origin_configured) {
                            shippingEl.textContent = 'Dikonfirmasi penjual';
                            distanceEl.textContent = 'Toko belum mengatur titik lokasi';
                            totalEl.textContent = rupiah(subtotal);
                            return;
                        }

                        setSummary(data.distance_km, data.shipping_cost, data.courier_label);
                    })
                    .catch(function () {
                        if (currentId !== requestId) {
                            return;
                        }

                        shippingEl.textContent = 'Dikonfirmasi penjual';
                        distanceEl.textContent = '';
                        totalEl.textContent = rupiah(subtotal);
                    });
            }

            /**
             * Reverse geocode titik peta → isi provinsi/kota/kecamatan secara otomatis.
             */
            function geocodePoint(lat, lng) {
                if (isGeocoding || lat === null || lng === null) {
                    return;
                }

                var currentId = ++geocodeRequestId;
                isGeocoding = true;

                fetch(geocodeEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': tokenInput ? tokenInput.value : '',
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng }),
                })
                    .then(function (response) {
                        return response.ok ? response.json() : null;
                    })
                    .then(function (data) {
                        if (!data || currentId !== geocodeRequestId) {
                            return;
                        }

                        if (data.province_id) {
                            setSelectableValue('province_id', data.province_name, data.province_id);
                        }
                        if (data.city_id) {
                            setSelectableValue('city_id', data.city_name, data.city_id);
                        }
                        if (data.district_id) {
                            setSelectableValue('district_id', data.district_name, data.district_id);
                        }

                        if (data.matched) {
                            var hint = document.querySelector('[data-geo-hint]');
                            if (hint) {
                                hint.textContent = 'Wilayah terdeteksi otomatis dari titik peta.';
                            }
                        }
                    })
                    .catch(function () {
                        // Biarkan pengguna mengisi wilayah secara manual.
                    })
                    .finally(function () {
                        isGeocoding = false;
                    });
            }

            /**
             * Set nilai input tersembunyi & label combobox pada searchable-select.
             */
            function setSelectableValue(name, label, value) {
                var hidden = document.getElementById(name);
                var input = document.getElementById(name + '-input');
                if (!hidden || !input) {
                    return;
                }
                hidden.value = value;
                input.value = label || '';
                hidden.dispatchEvent(new Event('change', { bubbles: true }));

                // Trigger handler parent reset agar turunannya bersih.
                document.dispatchEvent(new CustomEvent('iw:searchable-change', {
                    bubbles: true,
                    detail: { name: name, value: value },
                }));
            }

            if (picker) {
                picker.addEventListener('map-picker:change', function (event) {
                    var detail = event.detail || {};
                    var lat = detail.latitude !== undefined ? detail.latitude : null;
                    var lng = detail.longitude !== undefined ? detail.longitude : null;

                    requestEstimate(lat, lng);
                    geocodePoint(lat, lng);
                });
            }

            if (courierEl) {
                courierEl.addEventListener('change', function () {
                    var lat = latInput ? parseFloat(latInput.value) : NaN;
                    var lng = lngInput ? parseFloat(lngInput.value) : NaN;

                    requestEstimate(
                        Number.isFinite(lat) ? lat : null,
                        Number.isFinite(lng) ? lng : null
                    );
                });
            }

            if (latInput && lngInput && latInput.value !== '' && lngInput.value !== '') {
                requestEstimate(parseFloat(latInput.value), parseFloat(lngInput.value));
            }
        });
    </script>
@endpush
