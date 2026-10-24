@extends('layouts.app')

@section('title', 'Kirim Ulasan')

@section('content')
<section class="py-20 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-6 lg:px-12">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-left">Kirim Ulasan</h1>

        @if (session('success'))
            <div id="success-alert" class="fixed top-6 right-6 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-bounce">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('success-alert');
                    if (alert) alert.remove();
                }, 3000);
            </script>
        @endif

        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 transition hover:shadow-xl">
            @if ($product)
                <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-6">
                    <img src="{{ asset('storage/' . ($product->image ?? 'placeholder.jpg')) }}" class="w-20 h-20 object-cover rounded-xl shadow-sm">
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ $product->product_name }}</p>
                        <p class="text-sm text-gray-500 line-clamp-2">{{ $product->description ?? '-' }}</p>
                        <p class="text-base font-bold text-orange-600 mt-2">
                            Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endif

            <form action="{{ route('ratings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product ? $product->id : old('product_id') }}">
                <input type="hidden" name="transaction_id" value="{{ $transaction_id ?? old('transaction_id') }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Beri Rating</label>
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1 star-rating" data-rating="0">
                            @for ($s = 1; $s <= 5; $s++)
                                <button type="button" class="star-btn text-gray-300 hover:text-yellow-400 transition transform hover:scale-110" data-value="{{ $s }}">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.947z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" class="rating-input" value="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ulasan (opsional)</label>
                    <textarea name="comment" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition" placeholder="Bagikan pengalaman Anda tentang produk ini..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tambah Foto (opsional)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-28 h-28 bg-gray-100 rounded-xl border border-gray-200 overflow-hidden flex items-center justify-center shadow-sm">
                            <img id="image-preview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            <div id="image-placeholder" class="text-xs text-gray-400 px-2 text-center">Belum ada foto</div>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="image-input" name="image" accept="image/*" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-medium file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200 transition">
                            <p class="text-xs text-gray-400 mt-1">Maks 2MB • jpeg, jpg, png, webp</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                    <a href="{{ route('orders', ['status' => 'done']) }}" class="px-6 py-2.5 border border-black text-gray-700 rounded-lg font-semibold hover:bg-green-50 transition">
                        Kembali
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-black text-white rounded-lg font-semibold text-center hover:bg-gray-700 transition">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.star-rating').forEach(starContainer => {
            const buttons = starContainer.querySelectorAll('.star-btn');
            const input = starContainer.closest('form').querySelector('.rating-input');
            buttons.forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const rating = btn.dataset.value;
                    input.value = rating;
                    buttons.forEach((b, idx) => {
                        if (idx < rating) b.classList.replace('text-gray-300', 'text-yellow-400');
                        else b.classList.replace('text-yellow-400', 'text-gray-300');
                    });
                });
                btn.addEventListener('mouseenter', () => {
                    const hoverRating = btn.dataset.value;
                    buttons.forEach((b, idx) => {
                        if (idx < hoverRating) b.classList.replace('text-gray-300', 'text-yellow-400');
                        else b.classList.replace('text-yellow-400', 'text-gray-300');
                    });
                });
            });
            starContainer.addEventListener('mouseleave', () => {
                const currentRating = input.value;
                buttons.forEach((b, idx) => {
                    if (idx < currentRating) b.classList.replace('text-gray-300', 'text-yellow-400');
                    else b.classList.replace('text-yellow-400', 'text-gray-300');
                });
            });
        });

        const imageInput = document.getElementById('image-input');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('image-placeholder');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        preview.src = evt.target.result;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            });
        }
    });
</script>
@endsection
