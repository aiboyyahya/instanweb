@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<section class="py-16 bg-white min-h-screen">
    <div class="max-w-6xl mx-auto border border-gray-200 rounded-3xl bg-white shadow-md p-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran Pesanan</h1>
            <p class="text-gray-600 mt-2">Order #{{ $transaction->order_code }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">Detail Produk</h2>
                <div class="space-y-4">
                    @foreach($transaction->items as $item)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/80' }}"
                             alt="{{ $item->product->product_name }}"
                             class="w-20 h-20 object-cover rounded-lg shadow-sm">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $item->product->product_name }}</h3>
                            <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }}</p>
                            <p class="text-sm text-gray-600">Harga: Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-lg text-orange-600">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="bg-gray-50 rounded-xl p-6 space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pengiriman</h3>
                    <div>
                        <p class="text-sm text-gray-600">Alamat</p>
                        <p class="font-medium text-gray-900">{{ $transaction->address }}</p>
                    </div>
                    @if($transaction->province && $transaction->city && $transaction->subdistrict)
                    <div>
                        <p class="text-sm text-gray-600">Wilayah</p>
                        <p class="font-medium text-gray-900">
                            {{ $transaction->subdistrict }}, {{ $transaction->city }}, {{ $transaction->province }}
                        </p>
                    </div>
                    @endif
                    @if($transaction->courier)
                    <div>
                        <p class="text-sm text-gray-600">Kurir</p>
                        <p class="font-medium text-gray-900 uppercase">{{ $transaction->courier }}</p>
                    </div>
                    @endif
                    @if($transaction->courier_service)
                    <div>
                        <p class="text-sm text-gray-600">Layanan</p>
                        <p class="font-medium text-gray-900">{{ $transaction->courier_service }}</p>
                    </div>
                    @endif
                    @if($transaction->shipping_cost)
                    <div>
                        <p class="text-sm text-gray-600">Biaya Ongkir</p>
                        <p class="font-medium text-gray-900">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($transaction->tracking_number)
                    <div>
                        <p class="text-sm text-gray-600">Nomor Resi</p>
                        <p class="font-medium text-gray-900">{{ $transaction->tracking_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 border-b border-gray-200 pb-2">Ringkasan Pembayaran</h2>

                <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->total - ($transaction->shipping_cost ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between">
                        <span class="font-bold text-lg">Total Pembayaran</span>
                        <span class="font-extrabold text-2xl text-orange-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pembayaran</h3>
                    <div class="flex items-center gap-3">
                        @php
                            $statusColor = match($transaction->payment_status) {
                                'paid' => 'bg-green-500',
                                'pending' => 'bg-yellow-500',
                                'failed' => 'bg-red-500',
                                default => 'bg-gray-400'
                            };
                        @endphp
                        <div class="w-4 h-4 rounded-full {{ $statusColor }}"></div>
                        <span class="text-gray-700 capitalize">{{ $transaction->payment_status }}</span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
                    <div id="payment-loading" class="space-y-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mx-auto"></div>
                        <h3 class="text-lg font-semibold text-gray-900">Memproses Pembayaran...</h3>
                        <p class="text-gray-600">Mohon tunggu sebentar, sedang mempersiapkan pembayaran Midtrans.</p>
                    </div>

                    <div id="payment-ready" class="hidden space-y-4">
                        <div class="text-green-600">
                            <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Pembayaran Siap</h3>
                        <p class="text-gray-600">Modal pembayaran akan muncul dalam beberapa detik.</p>
                        <button id="manual-pay-btn"
                            class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition font-medium hidden">
                            Buka Pembayaran Manual
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingDiv = document.getElementById('payment-loading');
    const readyDiv = document.getElementById('payment-ready');
    const manualBtn = document.getElementById('manual-pay-btn');

    setTimeout(() => {
        loadingDiv.classList.add('hidden');
        readyDiv.classList.remove('hidden');
    }, 2000);

    setTimeout(() => {
        triggerPayment();
    }, 3000);

    manualBtn.addEventListener('click', triggerPayment);

    function triggerPayment() {
        fetch("{{ route('transaction.getSnapToken', $transaction->id) }}", {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (!data.snap_token) throw new Error("Snap token tidak ditemukan di server.");
            snap.pay(data.snap_token, {
                onSuccess: result => window.location.href = "{{ route('checkout.success', $transaction->id) }}",
                onPending: result => window.location.href = "{{ route('checkout.success', $transaction->id) }}",
                onError: result => {
                    alert("Pembayaran gagal. Silakan coba lagi.");
                    window.location.href = "{{ route('checkout.page') }}";
                },
                onClose: () => console.log("Modal pembayaran ditutup.")
            });
        })
        .catch(err => {
            alert("Tidak bisa memulai pembayaran. Coba refresh halaman");
            console.error(err);
        });
    }
});
</script>
@endsection
