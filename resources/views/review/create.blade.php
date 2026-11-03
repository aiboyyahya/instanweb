@extends('layouts.app')

@section('title', 'Kirim Ulasan | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders', ['status' => 'done'])],
            ['label' => 'Kirim Ulasan'],
        ]" />

        <x-ui.page-header class="mt-4" eyebrow="Ulasan" title="Kirim Ulasan"
            description="Bantu pembeli lain dengan menilai produk yang Anda terima." />

        <div class="mt-8 rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-8">
            @if ($product)
                <div class="flex items-center gap-4 border-b border-line pb-6">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}"
                            class="h-20 w-20 shrink-0 rounded-xl border border-line object-cover">
                    @else
                        <span
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl bg-surface-soft text-fg-muted">
                            <x-ui.icon name="package" class="h-7 w-7" />
                        </span>
                    @endif

                    <div class="min-w-0">
                        <p class="font-semibold text-fg">{{ $product->product_name }}</p>
                        <p class="mt-0.5 line-clamp-2 text-sm text-fg-muted">{{ $product->description ?? '-' }}</p>
                        <p class="mt-1.5 text-base font-bold text-cta">
                            Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endif

            <form action="{{ route('ratings.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product ? $product->id : old('product_id') }}">
                <input type="hidden" name="transaction_id" value="{{ $transaction_id ?? old('transaction_id') }}">

                <fieldset>
                    <legend class="text-sm font-semibold text-fg">
                        Beri rating <span class="text-danger" aria-hidden="true">*</span>
                        <span class="sr-only">(wajib)</span>
                    </legend>

                    <div class="mt-2 flex flex-wrap items-center gap-1" data-star-rating>
                        @for ($s = 1; $s <= 5; $s++)
                            <label
                                class="group cursor-pointer rounded-md p-1 text-fg-muted transition hover:text-warning has-[:checked]:text-warning focus-within:ring-2 focus-within:ring-info focus-within:ring-offset-2 focus-within:ring-offset-surface">
                                <input type="radio" name="rating" value="{{ $s }}" class="peer sr-only"
                                    @checked((string) old('rating') === (string) $s) required>
                                <span class="sr-only">{{ $s }} bintang</span>
                                <svg class="h-9 w-9 transition group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 00.95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 00-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 00-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 00-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 00.95-.69l1.286-3.947z" />
                                </svg>
                            </label>
                        @endfor

                        <p class="ml-2 text-sm text-fg-muted" data-rating-label aria-live="polite">
                            @if (old('rating'))
                                {{ old('rating') }} dari 5 bintang
                            @else
                                Pilih 1–5 bintang
                            @endif
                        </p>
                    </div>

                    @error('rating')
                        <p class="mt-2 flex items-start gap-1.5 text-xs font-medium text-danger" role="alert">
                            <x-ui.icon name="alert-circle" class="mt-0.5 h-4 w-4" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </fieldset>

                <x-ui.field label="Ulasan (opsional)" name="comment">
                    <textarea name="comment" id="comment" rows="4"
                        placeholder="Bagikan pengalaman Anda tentang produk ini..."
                        class="w-full rounded-field border border-line-strong bg-surface px-3.5 py-3 text-sm text-fg placeholder:text-fg-muted">{{ old('comment') }}</textarea>
                </x-ui.field>

                <div>
                    <label for="image" class="block text-sm font-medium text-fg-secondary">Tambah foto (opsional)</label>

                    <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div
                            class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-line bg-surface-soft">
                            <img id="image-preview" src="" alt="Pratinjau foto ulasan"
                                class="hidden h-full w-full object-cover">
                            <span id="image-placeholder" class="px-2 text-center text-xs text-fg-muted">Belum ada
                                foto</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                                class="block w-full rounded-field border border-line-strong bg-surface text-sm text-fg-secondary file:mr-3 file:min-h-11 file:rounded-l-field file:border-0 file:bg-surface-soft file:px-4 file:text-sm file:font-semibold file:text-fg hover:file:bg-line">
                            <p class="mt-1.5 text-xs text-fg-muted">Maks 2 MB · JPEG, JPG, PNG, atau WEBP.</p>
                            @error('image')
                                <p class="mt-1.5 flex items-start gap-1.5 text-xs font-medium text-danger" role="alert">
                                    <x-ui.icon name="alert-circle" class="mt-0.5 h-4 w-4" />
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:justify-end">
                    <x-ui.button :href="route('orders', ['status' => 'done'])" variant="outline">Kembali</x-ui.button>
                    <x-ui.button type="submit">
                        <x-ui.icon name="check" class="h-4 w-4" /> Kirim Ulasan
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const group = document.querySelector('[data-star-rating]');

            if (group) {
                const radios = Array.from(group.querySelectorAll('input[name="rating"]'));
                const labels = Array.from(group.querySelectorAll('label'));
                const output = group.querySelector('[data-rating-label]');

                function paint(value) {
                    labels.forEach(function (label, index) {
                        const active = index < value;
                        label.classList.toggle('text-warning', active);
                        label.classList.toggle('text-fg-muted', !active);
                    });

                    if (output) {
                        output.textContent = value > 0
                            ? value + ' dari 5 bintang'
                            : 'Pilih 1–5 bintang';
                    }
                }

                radios.forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        paint(parseInt(radio.value, 10) || 0);
                    });

                    radio.addEventListener('focus', function () {
                        paint(parseInt(radio.value, 10) || 0);
                    });
                });

                const checked = radios.find(function (radio) {
                    return radio.checked;
                });

                paint(checked ? parseInt(checked.value, 10) : 0);

                labels.forEach(function (label) {
                    label.addEventListener('mouseenter', function () {
                        const radio = label.querySelector('input');
                        paint(parseInt(radio.value, 10) || 0);
                    });
                });

                group.addEventListener('mouseleave', function () {
                    const current = radios.find(function (radio) {
                        return radio.checked;
                    });
                    paint(current ? parseInt(current.value, 10) : 0);
                });
            }

            const imageInput = document.getElementById('image');
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');

            if (imageInput && preview) {
                imageInput.addEventListener('change', function () {
                    const file = this.files && this.files[0];

                    if (!file || !file.type.startsWith('image/')) {
                        preview.src = '';
                        preview.classList.add('hidden');
                        placeholder?.classList.remove('hidden');
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function (event) {
                        preview.src = event.target.result;
                        preview.classList.remove('hidden');
                        placeholder?.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
@endpush
