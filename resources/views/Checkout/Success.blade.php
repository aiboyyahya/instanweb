@extends('layouts.app')

@section('title', 'Pesanan Berhasil | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $subtotal = $transaction->total - ($transaction->shipping_cost ?? 0);
        $canReview = in_array($transaction->status, ['success', 'done'], true);
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        {{-- Header sukses --}}
        <div class="rounded-panel border border-success/30 bg-surface p-6 text-center shadow-sm sm:p-8">
            <span
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success-soft text-success"
                aria-hidden="true">
                <x-ui.icon name="check-circle" class="h-9 w-9" />
            </span>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-fg sm:text-3xl">Pesanan Berhasil Dibuat</h1>
            <p class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-fg-muted">
                Terima kasih. Pesanan Anda sudah kami terima dan akan diproses oleh penjual.
            </p>

            <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                <x-ui.status-badge type="order" :status="$transaction->status" />
                <x-ui.status-badge type="payment" :status="$transaction->payment_status" />
            </div>

            <p class="mt-4 text-sm text-fg-muted">
                Kode pesanan
                <span class="ml-1 rounded-md bg-surface-soft px-2 py-1 font-mono text-sm font-semibold text-fg">
                    {{ $transaction->order_code }}
                </span>
            </p>
        </div>

        {{-- Detail pesanan --}}
        <section class="mt-5 rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6" aria-labelledby="detail-pesanan">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 id="detail-pesanan" class="text-lg font-semibold text-fg">Detail Pesanan</h2>
                <a href="{{ route('orders') }}"
                    class="text-sm font-semibold text-info underline underline-offset-2">Lihat semua pesanan</a>
            </div>

            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Tanggal</dt>
                    <dd class="mt-1 text-fg">{{ $transaction->created_at->format('d M Y, H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Total</dt>
                    <dd class="mt-1 font-semibold text-fg">Rp {{ number_format($transaction->total, 0, ',', '.') }}</dd>
                </div>
                @if ($transaction->courier)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kurir</dt>
                        <dd class="mt-1 uppercase text-fg">{{ $transaction->courier }}</dd>
                    </div>
                @endif
                @if ($transaction->phone_number)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Nomor telepon</dt>
                        <dd class="mt-1 text-fg">{{ $transaction->phone_number }}</dd>
                    </div>
                @endif
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Alamat pengiriman</dt>
                    <dd class="mt-1 leading-relaxed text-fg">{{ $transaction->address }}</dd>
                </div>
                @if ($transaction->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Catatan</dt>
                        <dd class="mt-1 leading-relaxed text-fg">{{ $transaction->notes }}</dd>
                    </div>
                @endif
            </dl>

            <dl class="mt-5 space-y-2 border-t border-line pt-4 text-sm">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-fg-muted">Subtotal</dt>
                    <dd class="font-medium text-fg">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-fg-muted">Ongkos kirim</dt>
                    <dd class="font-medium text-fg">Rp
                        {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                    <dt class="font-medium text-fg">Total pembayaran</dt>
                    <dd class="text-xl font-bold text-fg">Rp {{ number_format($transaction->total, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </section>

        {{-- Produk & ulasan --}}
        <section class="mt-5 rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
            aria-labelledby="produk-dipesan">
            <h2 id="produk-dipesan" class="text-lg font-semibold text-fg">Produk yang Dipesan</h2>

            <div class="mt-4 space-y-4">
                @foreach ($transaction->items as $item)
                    @php
                        $product = $item->product;
                        $productId = $product->id ?? null;
                        $image = $product && $product->image
                            ? asset('storage/' . $product->image)
                            : asset('images/gambar3.jpeg');
                    @endphp

                    <article class="rounded-card border border-line bg-surface-muted p-4">
                        <div class="flex gap-3">
                            <img src="{{ $image }}" alt=""
                                class="h-16 w-16 shrink-0 rounded-lg border border-line object-cover">

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-fg">
                                    {{ $product->product_name ?? 'Produk tidak tersedia' }}
                                </p>
                                <p class="mt-1 text-xs text-fg-muted">
                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>

                            <p class="text-sm font-semibold text-fg">
                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="mt-4 border-t border-line pt-3">
                            @auth
                                @if ($productId && isset($userRatings) && $userRatings->has($productId))
                                    @php $r = $userRatings->get($productId); @endphp
                                    <div class="rounded-lg bg-surface p-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-ui.rating-stars :rating="$r->rating" />
                                            <span class="text-xs text-fg-muted">Ulasan Anda sudah terkirim.</span>
                                        </div>

                                        @if ($r->comment)
                                            <p class="mt-2 text-sm leading-relaxed text-fg-secondary">"{{ $r->comment }}"
                                            </p>
                                        @endif
                                    </div>
                                @elseif ($canReview)
                                    <form action="{{ route('ratings.store') }}" method="POST"
                                        enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $productId }}">
                                        <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                                        <div class="grid gap-3 sm:grid-cols-[auto_1fr]">
                                            <div>
                                                <label for="rating-{{ $productId }}"
                                                    class="block text-xs font-medium text-fg-secondary">Rating</label>
                                                <select name="rating" id="rating-{{ $productId }}" required
                                                    class="mt-1 h-10 rounded-field border border-line-strong bg-surface px-2 text-sm text-fg">
                                                    @for ($s = 5; $s >= 1; $s--)
                                                        <option value="{{ $s }}"
                                                            @selected($s === 5)>{{ $s }} bintang</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div>
                                                <label for="comment-{{ $productId }}"
                                                    class="block text-xs font-medium text-fg-secondary">Ulasan
                                                    (opsional)</label>
                                                <textarea name="comment" id="comment-{{ $productId }}" rows="2"
                                                    placeholder="Bagikan pengalaman Anda tentang produk ini..."
                                                    class="mt-1 w-full rounded-field border border-line-strong bg-surface px-3 py-2 text-sm text-fg placeholder:text-fg-muted"></textarea>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div>
                                                <label for="image-{{ $productId }}"
                                                    class="block text-xs font-medium text-fg-secondary">Foto
                                                    (opsional, maks 2 MB)</label>
                                                <input type="file" name="image" id="image-{{ $productId }}"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    class="mt-1 block w-full text-xs text-fg-muted file:mr-3 file:rounded-md file:border-0 file:bg-surface-soft file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-fg">
                                            </div>

                                            <x-ui.button type="submit" size="sm">Kirim Ulasan</x-ui.button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-xs leading-relaxed text-fg-muted">
                                        Ulasan dapat dikirim setelah status pesanan selesai. Anda dapat memantaunya di
                                        <a href="{{ route('orders') }}"
                                            class="font-semibold text-info underline underline-offset-2">Pesanan Saya</a>.
                                    </p>
                                @endif
                            @else
                                <p class="text-xs text-fg-muted">
                                    <a href="{{ route('login.form') }}"
                                        class="font-semibold text-info underline underline-offset-2">Masuk</a> untuk
                                    mengirim ulasan.
                                </p>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Aksi --}}
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <x-ui.button :href="route('home')" size="lg">Lanjut Belanja</x-ui.button>
            <x-ui.button :href="route('orders')" variant="outline" size="lg">Lihat Pesanan</x-ui.button>
        </div>
    </div>
@endsection
