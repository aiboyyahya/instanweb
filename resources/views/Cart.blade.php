@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
    <section class="py-16 bg-white min-h-screen">
        <div class="max-w-6xl mx-auto px-6 lg:px-10">

            <h1 class="text-3xl font-bold text-gray-900 mb-10 text-center">Keranjang Belanja</h1>
            @if (session('success'))
                <div
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-center shadow">
                    {{ session('success') }}
                </div>
            @endif

            @if (count($cart) > 0)

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2 space-y-6">

                        @php $total = 0; @endphp
                        @foreach ($cart as $id => $item)
                            @php
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            @endphp

                            <div
                                class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl border border-gray-100 transition">

                                <div class="flex items-center gap-5 w-full">

                                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/150' }}"
                                        alt="{{ $item['name'] }}" class="w-24 h-24 object-cover rounded-xl shadow-sm">

                                    <div class="flex-1">

                                        <h3 class="text-lg font-semibold text-gray-900 leading-tight">
                                            {{ $item['name'] }}
                                        </h3>

                                        <p class="text-orange-600 font-bold mt-1">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </p>

                                        <div class="flex items-center gap-3 mt-4">
                                            <form action="{{ route('updateCart', $id) }}" method="POST"
                                                class="flex items-center gap-2" id="quantity-form-{{ $id }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="button"
                                                    onclick="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                                    class="w-7 h-7 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition text-sm"
                                                    {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                    -
                                                </button>

                                                <input type="number" readonly name="quantity"
                                                    id="quantity-{{ $id }}" value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    class="w-12 text-center roundedpy-1 text-sm">

                                                <button type="button"
                                                    onclick="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                                    class="w-7 h-7 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition text-sm">
                                                    +
                                                </button>

                                            </form>
                                        </div>
                                    </div>

                                    <div class="text-right space-y-3">

                                        <div>
                                            <p class="text-sm text-gray-500">Subtotal</p>
                                            <p class="text-lg font-bold text-gray-900">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <form action="{{ route('removeCart', $id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="p-2 rounded-full hover:bg-red-100 text-red-500 hover:text-red-700 transition">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                                01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                                00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-7 h-fit sticky top-24 border border-gray-100">

                        <h2 class="text-xl font-bold text-gray-900 mb-5">Ringkasan Belanja</h2>

                        <div class="flex justify-between text-gray-700 mb-2">
                            <span>Total Produk</span>
                            <span>{{ count($cart) }}</span>
                        </div>

                        <div class="flex justify-between text-gray-700 mb-5">
                            <span>Total Pembayaran</span>
                            <span class="text-xl font-bold text-orange-600">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('checkout.page') }}"
                            class="block w-full bg-orange-600 text-white py-3 rounded-xl text-center font-medium hover:bg-orange-700 transition shadow-lg">
                            Checkout Sekarang
                        </a>

                        <a href="{{ route('home') }}"
                            class="block w-full mt-3 border border-gray-400 text-gray-700 py-3 rounded-xl text-center font-medium hover:bg-gray-100 transition">
                            Lanjut Belanja
                        </a>
                    </div>

                </div>
            @else
                <div class="bg-white rounded-2xl shadow-lg p-10 text-center border border-gray-100">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1 5M7 13h10m0 0v8a2
                        2 0 01-2 2H9a2 2 0 01-2-2v-8z" />
                    </svg>

                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Keranjang Kosong</h3>
                    <p class="text-gray-600 mb-6">Belum ada produk di keranjang belanja Anda.</p>

                    <a href="{{ route('home') }}"
                        class="inline-flex items-center bg-orange-600 text-white py-3 px-8 rounded-xl hover:bg-orange-700 transition font-medium shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Mulai Belanja
                    </a>
                </div>

            @endif
        </div>
    </section>

    <script>
        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) return;
            const form = document.getElementById('quantity-form-' + productId);
            const quantityInput = document.getElementById('quantity-' + productId);
            quantityInput.value = newQuantity;
            form.submit();
        }
    </script>

@endsection
