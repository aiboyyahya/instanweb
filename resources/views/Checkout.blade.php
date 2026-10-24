@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

    <section class="py-16 bg-white min-h-screen">
        <div
            class="max-w-6xl mx-auto border border-gray-200 rounded-3xl bg-white shadow-md p-10 grid grid-cols-1 md:grid-cols-2 gap-12">

            <div class="md:col-span-2 text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
                <p class="text-gray-600 mt-2">Pastikan data dan pesanan Anda sudah benar sebelum melakukan pembayaran.</p>
            </div>

            <form id="checkout-form" action="{{ route('checkout') }}" method="POST" class="space-y-8">
                @csrf
                <div class="border border-gray-200 rounded-3xl p-8 shadow-sm bg-white">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Informasi Pengiriman</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-2">Nama
                                Penerima</label>
                            <input type="text" name="recipient_name" id="recipient_name" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600"
                                value="{{ old('recipient_name', Auth::user()->name ?? '') }}">
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                                Telepon</label>
                            <input type="tel" name="phone_number" id="phone_number" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600"
                                value="{{ old('phone_number', Auth::user()->phone ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">{{ old('address', Auth::user()->address ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="province_select"
                                class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                            <select id="province_select"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                                <option value="">Pilih provinsi</option>
                            </select>
                            <p id="province_status" class="text-xs text-gray-500 mt-1 hidden">Memuat daftar provinsi...
                            </p>
                            <p id="province_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label for="city_select" class="block text-sm font-medium text-gray-700 mb-2">Kota /
                                Kabupaten</label>
                            <select id="city_select"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                                <option value="">Pilih kota/kabupaten</option>
                            </select>
                            <p id="city_status" class="text-xs text-gray-500 mt-1 hidden">Memuat daftar kota...</p>
                            <p id="city_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="mt-6 mb-6">
                        <label for="district_select" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                        <select id="district_select"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                            <option value="">Pilih kecamatan</option>
                        </select>
                        <p id="district_status" class="text-xs text-gray-500 mt-1 hidden">Memuat daftar kecamatan...</p>
                        <p id="district_error" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 mb-6">
                        <div>
                            <label for="courier" class="block text-sm font-medium text-gray-700 mb-2">Kurir</label>
                            <select id="courier" name="courier"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                                <option value="">Pilih kurir</option>
                            </select>
                            <p id="courier_status" class="text-xs text-gray-500 mt-1 hidden">Memuat layanan kurir...</p>
                            <p id="courier_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label for="courier_service"
                                class="block text-sm font-medium text-gray-700 mb-2">Layanan</label>
                            <select id="courier_service" name="courier_service"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                                <option value="">Pilih layanan</option>
                            </select>
                            <p id="service_status" class="text-xs text-gray-500 mt-1 hidden">Memuat layanan...</p>
                            <p id="service_error" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                            <input type="text" name="postal_code" id="postal_code" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600"
                                placeholder="Masukkan kode pos">
                        </div>
                    </div>

                    <input type="hidden" id="province_id" name="province_id">
                    <input type="hidden" id="province_name" name="province">
                    <input type="hidden" id="city_id" name="city_id">
                    <input type="hidden" id="city_name" name="city">
                    <input type="hidden" id="district_id" name="district_id">
                    <input type="hidden" id="district_name" name="district">
                    <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600"
                            placeholder="Tambahkan catatan untuk penjual jika ada..."></textarea>
                    </div>


                </div>
            </form>

            <div class="flex flex-col max-w-lg h-fit space-y-8">
                <div class="border border-gray-200 rounded-3xl p-8 shadow-sm bg-white">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Detail Produk</h2>
                    <ul class="space-y-6">
                        @php $total = 0; @endphp
                        @php
                            $items = isset($directCheckout) && !empty($directCheckout) ? $directCheckout : $cart;
                        @endphp
                        @foreach ($items as $id => $item)
                            @php
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            @endphp
                            <li class="flex gap-4 items-center border-b border-gray-100 pb-4">
                                <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/100' }}"
                                    alt="{{ $item['name'] }}" class="w-24 h-20 object-cover rounded-xl" />
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">{{ $item['name'] }}</p>
                                    <p class="text-sm text-gray-600">Kategori: Helm</p>
                                    <p class="text-sm text-gray-600">Jumlah: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="ml-auto font-semibold text-gray-900">
                                    Rp{{ number_format($subtotal, 0, ',', '.') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-8 border-t pt-6 text-gray-900">
                        <h3 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h3>
                        <div class="flex justify-between mb-2">
                            <span>Subtotal</span>
                            <span id="display-subtotal">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-6">
                            <span>Ongkir</span>
                            <span id="display-shipping">Rp0</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-4">
                            <span>Total</span>
                            <span id="display-total">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-3xl p-8 shadow-sm bg-white">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Metode Pembayaran</h2>
                    <div class="border border-gray-300 rounded-xl p-5 bg-gray-50 hover:bg-gray-100 transition">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="payment_method" value="midtrans" checked
                                class="form-radio text-orange-600 focus:ring-orange-500" />
                            <span class="ml-3 font-medium text-gray-900">
                                Midtrans (QRIS / Virtual Account / E-Wallet)
                            </span>
                        </label>
                    </div>
                </div>
                <button type="submit" form="checkout-form"
                    class="mt-6 w-full bg-black text-white font-semibold py-3 rounded-xl hover:bg-gray-800 transition">
                    KONFIRMASI PESANAN
                </button>
            </div>
        </div>
    </section>


    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const provinceSelect = document.getElementById("province_select");
            const citySelect = document.getElementById("city_select");
            const districtSelect = document.getElementById("district_select");
            const courierSelect = document.getElementById("courier");
            const serviceSelect = document.getElementById("courier_service");
            const costInput = document.getElementById("shipping_cost");
            const displayShipping = document.getElementById("display-shipping");
            const displayTotal = document.getElementById("display-total");

            const originCityId = {{ $originCityId }};
            const totalWeight = {!! $totalWeight !!};

            async function loadProvinces() {
                const statusEl = document.getElementById("province_status");
                const errorEl = document.getElementById("province_error");
                statusEl.classList.remove("hidden");
                errorEl.classList.add("hidden");

                try {
                    const res = await fetch(`/rajaongkir/provinces`);
                    const data = await res.json();

                    if (!res.ok || !data.rajaongkir?.results) throw new Error("Invalid data");

                    provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                    data.rajaongkir.results.forEach(p => {
                        const option = document.createElement("option");
                        option.value = p.province_id || p.id;
                        option.textContent = p.province || p.name;
                        provinceSelect.appendChild(option);
                    });
                    statusEl.classList.add("hidden");
                } catch (err) {
                    console.error("Province fetch failed:", err);
                    provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
                    statusEl.classList.add("hidden");
                    errorEl.textContent = "Gagal memuat provinsi. Silakan coba lagi.";
                    errorEl.classList.remove("hidden");
                }
            }

            async function loadCities(provinceId) {
                if (!provinceId) return;
                const statusEl = document.getElementById("city_status");
                const errorEl = document.getElementById("city_error");
                statusEl.classList.remove("hidden");
                errorEl.classList.add("hidden");

                try {
                    const res = await fetch(`/rajaongkir/cities?province_id=${provinceId}`);
                    const data = await res.json();

                    if (!res.ok || !data.rajaongkir?.results) throw new Error("Invalid data");

                    citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                    data.rajaongkir.results.forEach(c => {
                        const option = document.createElement("option");
                        option.value = c.city_id;
                        option.textContent = c.city_name;
                        citySelect.appendChild(option);
                    });

                    districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    statusEl.classList.add("hidden");
                } catch (err) {
                    console.error("City fetch failed:", err);
                    citySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
                    statusEl.classList.add("hidden");
                    errorEl.textContent = "Gagal memuat kota. Silakan coba lagi.";
                    errorEl.classList.remove("hidden");
                }
            }

            async function loadDistricts(cityId) {
                if (!cityId) return;
                const statusEl = document.getElementById("district_status");
                const errorEl = document.getElementById("district_error");
                statusEl.classList.remove("hidden");
                errorEl.classList.add("hidden");

                try {
                    const res = await fetch(`/rajaongkir/districts?city_id=${cityId}`);
                    const data = await res.json();

                    if (!res.ok || !data.rajaongkir?.results) throw new Error("Invalid data");

                    districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    data.rajaongkir.results.forEach(d => {
                        const option = document.createElement("option");
                        option.value = d.subdistrict_id || d.district_id;
                        option.textContent = d.subdistrict_name || d.district_name;
                        districtSelect.appendChild(option);
                    });

                    statusEl.classList.add("hidden");
                } catch (err) {
                    console.error("District fetch failed:", err);
                    districtSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
                    statusEl.classList.add("hidden");
                    errorEl.textContent = "Gagal memuat kecamatan. Silakan coba lagi.";
                    errorEl.classList.remove("hidden");
                }
            }

            function loadCouriers() {
                courierSelect.innerHTML = '<option value="">Pilih Kurir</option>';
                const couriers = [{
                        code: "jne",
                        name: "JNE"
                    },
                    {
                        code: "tiki",
                        name: "TIKI"
                    },
                    {
                        code: "pos",
                        name: "POS Indonesia"
                    },
                    {
                        code: "jnt",
                        name: "J&T Express"
                    }
                ];
                couriers.forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.code;
                    opt.textContent = c.name;
                    courierSelect.appendChild(opt);
                });
            }

            async function loadCourierServices() {
                const courier = courierSelect.value;
                const destination = districtSelect.value || citySelect.value;

                if (!courier || !destination) {
                    serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                    costInput.value = 0;
                    updateTotal();
                    return;
                }

                const statusEl = document.getElementById("service_status");
                const errorEl = document.getElementById("service_error");
                statusEl.classList.remove("hidden");
                errorEl.classList.add("hidden");

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch(`/rajaongkir/cost`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            origin: originCityId,
                            destination,
                            weight: totalWeight,
                            courier
                        }),
                    });

                    if (!res.ok) throw new Error("Gagal memuat layanan");
                    const data = await res.json();

                    serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';

                    if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                        data.data.forEach(service => {
                            const option = document.createElement("option");
                            option.value = service.service;
                            option.textContent =
                                `${service.name} - ${service.description} (Rp ${service.cost.toLocaleString()})`;
                            option.dataset.cost = service.cost;
                            serviceSelect.appendChild(option);
                        });
                    } else {
                        serviceSelect.innerHTML = '<option value="">Tidak ada layanan tersedia</option>';
                    }

                    statusEl.classList.add("hidden");
                } catch (err) {
                    console.error("Load courier services failed:", err);
                    statusEl.classList.add("hidden");
                    errorEl.textContent = "Gagal memuat layanan pengiriman. Silakan coba lagi.";
                    errorEl.classList.remove("hidden");
                    serviceSelect.innerHTML = '<option value="">Gagal memuat layanan</option>';
                }
            }

            function updateTotal() {
                const subtotal = parseInt(document.getElementById("display-subtotal").textContent.replace(/[^\d]/g,
                    '')) || 0;
                const shipping = parseInt(costInput.value) || 0;
                const total = subtotal + shipping;
                displayShipping.textContent = `Rp${shipping.toLocaleString()}`;
                displayTotal.textContent = `Rp${total.toLocaleString()}`;
            }

            provinceSelect.addEventListener("change", (e) => {
                const selectedOption = e.target.selectedOptions[0];
                if (selectedOption) {
                    document.getElementById("province_id").value = selectedOption.value;
                    document.getElementById("province_name").value = selectedOption.textContent;
                }
                loadCities(e.target.value);
            });
            citySelect.addEventListener("change", (e) => {
                const selectedOption = e.target.selectedOptions[0];
                if (selectedOption) {
                    document.getElementById("city_id").value = selectedOption.value;
                    document.getElementById("city_name").value = selectedOption.textContent;
                }
                loadDistricts(e.target.value);
            });
            districtSelect.addEventListener("change", (e) => {
                const selectedOption = e.target.selectedOptions[0];
                if (selectedOption) {
                    document.getElementById("district_id").value = selectedOption.value;
                    document.getElementById("district_name").value = selectedOption.textContent;
                }
                loadCourierServices();
            });
            courierSelect.addEventListener("change", loadCourierServices);
            serviceSelect.addEventListener("change", (e) => {
                const cost = e.target.selectedOptions[0]?.dataset.cost || 0;
                costInput.value = cost;
                updateTotal();
            });


            loadProvinces();
            loadCouriers();
        });
    </script>



@endsection
