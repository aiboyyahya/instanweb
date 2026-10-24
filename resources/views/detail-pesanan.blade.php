@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <section class="py-16 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="bg-white border border-gray-200 rounded-3xl shadow-md p-10 grid grid-cols-1 lg:grid-cols-3 gap-10">

                <div class="lg:col-span-2 space-y-8">
                    <h1 class="text-4xl font-bold text-center ml-96 text-gray-900 mb-10">Detail Pesanan</h1>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-semibold text-lg text-gray-900 mb-4">Produk dalam Pesanan</h3>
                        <div class="space-y-4">
                            @foreach ($transaction->items as $item)
                                <div class="flex gap-4 items-center">
                                    <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100' }}"
                                        alt="{{ $item->product->product_name }}"
                                        class="w-20 h-20 object-cover rounded-lg" />
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $item->product->product_name }}</p>
                                        <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between gap-6 text-sm font-medium text-gray-700">
                            <div>
                                <p class="text-gray-900 mb-1 font-semibold">Nomor Pesanan</p>
                                <p>#{{ $transaction->order_code }}</p>
                            </div>
                            <div>
                                <p class="text-gray-900 mb-1 font-semibold">Tanggal Pemesanan</p>
                                <p>{{ $transaction->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-900 mb-1 font-semibold">Tanggal Dikirim</p>
                                <p>
                                    @if ($transaction->status === 'sent')
                                        {{ $transaction->updated_at->translatedFormat('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-900 mb-1 font-semibold">Jumlah Barang</p>
                                <p>{{ $transaction->items->count() }} produk</p>
                            </div>
                            <div>
                                <p class="text-gray-900 mb-1 font-semibold">Status</p>
                                <span class="capitalize">{{ $transaction->status }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-semibold text-lg mb-4 text-gray-900">Informasi Pengiriman</h3>
                        <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-700">
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Nomor Resi</p>
                                <p>{{ $transaction->tracking_number ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Alamat Lengkap</p>
                                <p>{{ $transaction->address ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Provinsi</p>
                                <p>{{ $transaction->province ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Kota / Kabupaten</p>
                                <p>{{ $transaction->city ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Kecamatan</p>
                                <p>{{ $transaction->district ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Kurir</p>
                                <p class="uppercase">{{ $transaction->courier ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Layanan</p>
                                <p>{{ $transaction->courier_service ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Ongkos Kirim</p>
                                <p>Rp {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $steps = [
                            [
                                'key' => 'pending',
                                'label' => 'Pesanan Dibuat',
                                'date' => $transaction->created_at->translatedFormat('d M Y'),
                            ],
                            [
                                'key' => 'packing',
                                'label' => 'Dikemas',
                                'date' => $transaction->created_at->addDay()->translatedFormat('d M Y'),
                            ],
                            [
                                'key' => 'sent',
                                'label' => 'Dikirim',
                                'date' => $transaction->created_at->addDays(2)->translatedFormat('d M Y'),
                            ],
                            [
                                'key' => 'done',
                                'label' => 'Selesai',
                                'date' => $transaction->updated_at->translatedFormat('d M Y'),
                            ],
                        ];
                        $orderKeys = array_column($steps, 'key');
                        $currentStep = array_search($transaction->status, $orderKeys);
                    @endphp

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm w-full">
                        <h3 class="font-semibold text-lg mb-4 text-gray-900">Pelacakan Pesanan</h3>
                        <div class="flex items-center justify-between overflow-x-auto w-full">
                            @foreach ($steps as $index => $step)
                                @php
                                    $isActive = $index <= $currentStep;
                                    $circleColor = $isActive ? 'bg-orange-500 text-white' : 'bg-gray-300 text-gray-500';
                                @endphp
                                <div class="flex flex-col items-center min-w-[90px] relative text-center">
                                    <div
                                        class="rounded-full w-12 h-12 flex items-center justify-center mb-2 {{ $circleColor }}">
                                        {{ $loop->iteration }}
                                    </div>
                                    <span class="font-semibold text-sm text-gray-900">{{ $step['label'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $step['date'] }}</span>
                                    @if ($index < count($steps) - 1)
                                        <div class="absolute top-6 left-full w-16 h-1 bg-gray-300">
                                            @if ($isActive && $index + 1 <= $currentStep)
                                                <div class="h-1 bg-orange-500 w-full"></div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white border border-gray-200 p-6 flex flex-col space-y-6 shadow-sm h-fit mt-20">
                    <h3 class="font-bold text-xl text-gray-900">Ringkasan Pembayaran</h3>

                    <div class="text-gray-700 text-sm space-y-3">
                        <div class="flex justify-between items-center">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-900">Rp
                                {{ number_format($transaction->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Ongkos Kirim</span>
                            <span class="font-semibold text-gray-900">
                                Rp {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t pt-4 flex justify-between font-semibold text-lg text-gray-900">
                        <span>Total</span>
                        <span class="text-orange-600 font-bold">
                            Rp
                            {{ number_format(($transaction->total ?? 0) + ($transaction->shipping_cost ?? 0), 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-4">
                        @if ($transaction->payment_status == 'pending' && $transaction->status != 'done')
                            <a href="{{ route('checkout.payment', $transaction->id) }}"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 rounded-lg text-center transition">
                                Bayar Sekarang
                            </a>
                        @endif

                        <a href="{{ route('orders') }}"
                            class="w-full border border-gray-300 text-gray-700 font-medium py-3 rounded-lg text-center hover:bg-gray-100 transition">
                            Kembali ke Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
