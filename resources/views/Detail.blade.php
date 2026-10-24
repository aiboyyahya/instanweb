@extends('layouts.app')

@section('title', $product->product_name)

@section('content')
    <section class="min-h-screen bg-white py-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-16">
            <div class="bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">

                <div class="px-10 pt-10 text-center border-b border-gray-100 pb-8">
                    <h1 class="text-4xl font-bold text-gray-900">Detail Produk</h1>
                    <p class="text-blue-600 mt-3 text-lg">{{ $product->product_name }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 p-10">
                    <div class="space-y-8">
                        <div
                            class="aspect-square bg-gray-50 rounded-3xl flex items-center justify-center border border-gray-200 shadow-inner">
                            <img id="main-product-image"
                                src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/500x500' }}"
                                alt="{{ $product->product_name }}" class="object-contain w-full h-full rounded-2xl p-6">
                        </div>

                        @if ($product->images && $product->images->count())
                            <div class="flex flex-wrap justify-center gap-4">
                                @foreach ($product->images as $img)
                                    <button type="button"
                                        onclick="document.getElementById('main-product-image').src='{{ asset('storage/' . $img->image_file) }}'"
                                        class="w-16 h-16 rounded-xl overflow-hidden border border-gray-200 hover:border-orange-500 transition-all duration-200 shadow-sm">
                                        <img src="{{ asset('storage/' . $img->image_file) }}"
                                            class="object-cover w-full h-full">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <div
                            class="bg-gray-50 rounded-3xl border border-gray-200 p-4 max-h-[300px] overflow-y-auto shadow-inner">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Ulasan Produk</h3>
                            @if (isset($ratings) && $ratings->count())
                                <div class="space-y-4">
                                    @foreach ($ratings as $rating)
                                        <div
                                            class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 flex flex-col sm:flex-row gap-4">
                                            <div class="flex flex-col items-center sm:w-28">
                                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 shadow-sm">
                                                    <img src="{{ $rating->customer && $rating->customer->profile_photo ? asset('storage/' . $rating->customer->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($rating->customer->name ?? 'User') }}"
                                                        class="object-cover w-full h-full">
                                                </div>
                                                <p
                                                    class="text-xs font-semibold text-gray-800 mt-2 text-center truncate w-20">
                                                    {{ $rating->customer->name ?? 'Pengguna' }}
                                                </p>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center text-yellow-400 mb-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.947z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                                @if ($rating->comment)
                                                    <p class="text-gray-800 text-sm leading-snug mb-2">
                                                        "{{ $rating->comment }}"</p>
                                                @endif
                                                @if ($rating->image)
                                                    <img src="{{ asset('storage/' . $rating->image) }}"
                                                        class="w-20 h-20 object-cover rounded-lg border border-gray-200 shadow-sm">
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="bg-white border border-gray-200 rounded-xl p-4 text-center text-gray-500 shadow-sm text-sm">
                                    Belum ada ulasan.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div
                        class="flex flex-col max-w-lg border border-gray-200 rounded-3xl p-10 shadow-sm bg-white sticky top-10 h-fit space-y-8">

                        <div>
                            <h2 class="text-4xl font-semibold text-gray-900">{{ $product->product_name }}</h2>
                            <p class="mt-2 text-gray-700 leading-relaxed">{{ $product->description }}</p>
                        </div>

                        <div class="flex items-center mt-4 space-x-2">
                            <div class="flex items-center text-yellow-400">
                                @php $avg = isset($avgRating) ? round($avgRating) : 0; @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $avg ? 'text-yellow-400' : 'text-gray-300' }}"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.947z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-gray-600 text-sm font-medium">({{ $ratingCount ?? 0 }} ulasan) • rata-rata
                                {{ number_format($avgRating ?? 0, 1) }}</span>
                        </div>

                        <p class="text-3xl font-bold text-gray-900">
                            Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                        </p>

                        <div class="flex items-center gap-4">
                            <button type="button" id="decrement-btn"
                                class="w-12 h-12 rounded-full bg-gray-200 text-gray-800 font-bold hover:bg-gray-300">−</button>
                            <input type="number" id="product-quantity" value="1" min="1"
                                max="{{ $product->stock }}" readonly
                                class="w-20 text-center rounded-lg border border-gray-300 py-3 font-semibold text-lg">
                            <button type="button" id="increment-btn"
                                class="w-12 h-12 rounded-full bg-gray-200 text-gray-800 font-bold hover:bg-gray-300">+</button>
                        </div>

                        <p class="text-sm text-gray-600 font-semibold">Hanya tersisa {{ $product->stock }} barang</p>

                        <div class="flex gap-4 w-full">
                            <form id="buy-now-form" method="GET" action="{{ route('checkout.page') }}" class="flex-1">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" id="buy-now-qty" value="1">
                                <input type="hidden" name="direct" value="1">
                                <button type="submit"
                                    class="w-full py-4 bg-black text-white rounded-lg font-semibold hover:bg-gray-700 transition">
                                    Beli Sekarang
                                </button>
                            </form>

                            <form id="add-to-cart-form" method="POST" action="{{ route('addToCart') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" id="addcart-qty" value="1">
                                <button type="submit"
                                    class="w-full py-4 border border-black text-gray-700 rounded-lg font-semibold hover:bg-green-50 transition">
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        </div>


                        <div class="border-t border-gray-200 pt-6 space-y-6 text-gray-700 text-sm">
                            <div class="flex items-start gap-4">
                                <svg class="w-6 h-6 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Pengembalian Barang</h3>
                                    <p>Gratis pengembalian dalam 30 hari.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inc = document.getElementById('increment-btn');
            const dec = document.getElementById('decrement-btn');
            const qtyInput = document.getElementById('product-quantity');
            const max = parseInt(qtyInput.getAttribute('max'));
            const buyQty = document.getElementById('buy-now-qty');
            const cartQty = document.getElementById('addcart-qty');

            function sync() {
                buyQty.value = qtyInput.value;
                cartQty.value = qtyInput.value;
            }

            inc.addEventListener('click', () => {
                let val = parseInt(qtyInput.value);
                if (val < max) qtyInput.value = val + 1;
                sync();
            });

            dec.addEventListener('click', () => {
                let val = parseInt(qtyInput.value);
                if (val > 1) qtyInput.value = val - 1;
                sync();
            });

            sync();
        });
    </script>
@endsection
