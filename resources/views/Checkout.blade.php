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

            @php
                $items = isset($directCheckout) && !empty($directCheckout) ? $directCheckout : $cart;
                $hasZeroStock = false;
            @endphp

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
                                value="{{ old('phone_number', Auth::user()->phone_number ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600"
                            placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, provinsi">{{ old('address', Auth::user()->address ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="courier" class="block text-sm font-medium text-gray-700 mb-2">Kurir Pengiriman</label>
                            <select id="courier" name="courier" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600">
                                <option value="">Pilih Kurir</option>
                                <option value="JNE">JNE</option>
                                <option value="J&T">J&T Express</option>
                                <option value="Sicepat">SiCepat</option>
                                <option value="AnterAja">AnterAja</option>
                                <option value="COD / Kurir Toko">COD / Kurir Toko</option>
                            </select>
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                            <input type="text" name="postal_code" id="postal_code" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-orange-600 focus:border-orange-600"
                                placeholder="Masukkan kode pos">
                        </div>
                    </div>

                    <!-- Input tersembunyi untuk ongkir default 0 (atau flat-rate manual jika diperlukan) -->
                    <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Pesanan</label>
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
                        @foreach ($items as $id => $item)
                            @php
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                                
                                // Cek stok produk secara langsung
                                $productModel = \App\Models\Product::find($id);
                                $currentStock = $productModel ? $productModel->stock : ($item['stock'] ?? 0);
                                if ($currentStock <= 0) {
                                    $hasZeroStock = true;
                                }
                            @endphp
                            <li class="flex gap-4 items-center border-b border-gray-100 pb-4">
                                <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/100' }}"
                                    alt="{{ $item['name'] }}" class="w-24 h-20 object-cover rounded-xl" />
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">{{ $item['name'] }}</p>
                                    <p class="text-sm text-gray-600">Jumlah: {{ $item['quantity'] }}</p>
                                    
                                    @if ($currentStock <= 0)
                                        <span class="inline-block bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded mt-1">Stok Habis</span>
                                    @else
                                        <p class="text-xs text-gray-500">Stok tersedia: {{ $currentStock }}</p>
                                    @endif
                                </div>
                                <div class="ml-auto font-semibold text-gray-900">
                                    Rp{{ number_format($subtotal, 0, ',', '.') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @if ($hasZeroStock)
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                            ⚠️ Beberapa produk di keranjang Anda habis atau stoknya tidak mencukupi. Harap hapus produk tersebut untuk melanjutkan.
                        </div>
                    @endif

                    <div class="mt-8 border-t pt-6 text-gray-900">
                        <h3 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h3>
                        <div class="flex justify-between mb-2">
                            <span>Subtotal</span>
                            <span id="display-subtotal">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-6">
                            <span>Ongkos Kirim</span>
                            <span id="display-shipping">Rp0</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-4">
                            <span>Total Pembayaran</span>
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
                    class="mt-6 w-full py-3 rounded-xl font-semibold transition {{ $hasZeroStock ? 'bg-gray-400 text-gray-200 cursor-not-allowed' : 'bg-black text-white hover:bg-gray-800' }}"
                    {{ $hasZeroStock ? 'disabled' : '' }}>
                    {{ $hasZeroStock ? 'STOK KOSONG - TIDAK DAPAT CHECKOUT' : 'KONFIRMASI PESANAN' }}
                </button>
            </div>
        </div>
    </section>

    <!-- Script Midtrans Snap (Opsional jika masih ingin menggunakan Midtrans pembayaran) -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

@endsection