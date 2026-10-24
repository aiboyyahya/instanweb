@extends('layouts.app')

@section('title', 'Checkout Berhasil - InstanWeb')

@section('content')
    <section class="py-16 bg-gray-100">
        <div class="max-w-4xl mx-auto px-6 lg:px-16 text-center">
            <div class="bg-white rounded-2xl shadow p-8">
                <div class="text-green-500 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Checkout Berhasil!</h1>
                <p class="text-gray-600 mb-6">Pesanan Anda telah diterima dan sedang diproses.</p>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif



                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                    <h2 class="text-xl font-semibold mb-4">Detail Pesanan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Kode Pesanan</p>
                            <p class="font-semibold">{{ $transaction->order_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="font-semibold text-orange-600">{{ ucfirst($transaction->status) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status Pembayaran</p>
                            @php
                                $paymentStatusClass = match ($transaction->payment_status) {
                                    'paid' => 'text-green-600',
                                    'pending' => 'text-orange-600',
                                    'failed' => 'text-red-600',
                                    default => 'text-gray-600',
                                };
                            @endphp
                            <p class="font-semibold {{ $paymentStatusClass }}">
                                {{ ucfirst($transaction->payment_status) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total</p>
                            <p class="font-semibold">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal</p>
                            <p class="font-semibold">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Alamat Pengiriman</p>
                        <p class="font-semibold">{{ $transaction->address }}</p>
                    </div>
                    @if ($transaction->notes)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Catatan</p>
                            <p class="font-semibold">{{ $transaction->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                    <h3 class="text-lg font-semibold mb-4">Produk yang Dipesan</h3>
                    <div class="space-y-4">
                        @foreach ($transaction->items as $item)
                            <div class="flex items-center gap-4">
                                <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100' }}"
                                    alt="{{ $item->product->product_name }}" class="w-16 h-16 object-cover rounded">
                                <div class="flex-1">
                                    <p class="font-semibold">{{ $item->product->product_name }}</p>
                                    <p class="text-sm text-gray-600">Jumlah: {{ $item->quantity }} | Harga: Rp
                                        {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">Rp
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="mt-3 w-full">
                                @auth
                                    @php
                                        $productId = $item->product->id;
                                    @endphp
                                    @if (isset($userRatings) && $userRatings->has($productId))
                                        @php $r = $userRatings->get($productId); @endphp
                                        <div class="mt-2 text-sm text-gray-700">
                                            <div class="flex items-center gap-2">
                                                <div class="text-yellow-400 flex items-center">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $r->rating)
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.947z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.947z" />
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <div class="text-gray-600 text-sm">Anda memberi rating:
                                                    <strong>{{ $r->rating }}</strong>
                                                </div>
                                            </div>
                                            @if ($r->comment)
                                                <p class="mt-2 text-gray-700">"{{ $r->comment }}"</p>
                                            @endif
                                        </div>
                                    @else
                                        <form action="{{ route('ratings.store') }}" method="POST"
                                            enctype="multipart/form-data" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $productId }}">
                                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                            <div class="flex items-center gap-2">
                                                <label class="text-sm text-gray-700">Beri rating:</label>
                                                <select name="rating" class="rounded border-gray-300 px-2 py-1">
                                                    @for ($s = 1; $s <= 5; $s++)
                                                        <option value="{{ $s }}">{{ $s }}</option>
                                                    @endfor
                                                </select>
                                                <input type="file" name="image" accept="image/*" class="ml-2">
                                            </div>
                                            <div class="mt-2">
                                                <textarea name="comment" rows="2" class="w-full rounded border-gray-200" placeholder="Tulis ulasan singkat..."></textarea>
                                            </div>
                                            <div class="mt-2 text-right">
                                                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded">Kirim
                                                    Ulasan</button>
                                            </div>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}"
                        class="bg-orange-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-orange-700 transition">Lanjut
                        Belanja</a>
                    <a href="{{ route('orders') }}"
                        class="bg-gray-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-gray-700 transition">Lihat
                        Pesanan</a>
                </div>
            </div>
        </div>
    </section>



@endsection
