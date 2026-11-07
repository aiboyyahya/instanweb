@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $transaction->order_code . ' | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $subtotal = $transaction->total - ($transaction->shipping_cost ?? 0);
        $isCancelled = $transaction->status === 'cancelled';

        $steps = [
            ['key' => 'pending', 'label' => 'Pesanan dibuat', 'date' => $transaction->created_at],
            ['key' => 'packing', 'label' => 'Dikemas', 'date' => $transaction->created_at->copy()->addDay()],
            ['key' => 'sent', 'label' => 'Dikirim', 'date' => $transaction->created_at->copy()->addDays(2)],
            ['key' => 'done', 'label' => 'Selesai', 'date' => $transaction->updated_at],
        ];

        $orderKeys = array_column($steps, 'key');
        $currentStep = array_search($transaction->status, $orderKeys, true);
        $currentStep = $currentStep === false ? 0 : $currentStep;
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders')],
            ['label' => '#' . $transaction->order_code],
        ]" />

        <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Detail pesanan</p>
                <h1 class="mt-1 font-mono text-2xl font-bold tracking-tight text-fg sm:text-3xl">
                    #{{ $transaction->order_code }}
                </h1>
                <p class="mt-2 text-sm text-fg-muted">Dibuat
                    {{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-ui.status-badge type="order" :status="$transaction->status" />
                <x-ui.status-badge type="payment" :status="$transaction->payment_status" />
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3 lg:items-start">
            <div class="space-y-5 lg:col-span-2">
                {{-- Produk --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="produk-pesanan">
                    <h2 id="produk-pesanan" class="text-lg font-semibold text-fg">Produk dalam Pesanan</h2>

                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($transaction->items as $item)
                            @php
                                $image = $item->product && $item->product->image
                                    ? asset('storage/' . $item->product->image)
                                    : asset('images/gambar3.jpeg');
                            @endphp

                            <li class="flex items-center gap-3 py-3 first:pt-0">
                                <img src="{{ $image }}" alt=""
                                    class="h-16 w-16 shrink-0 rounded-lg border border-line object-cover">

                                <div class="min-w-0 flex-1">
                                    @if ($item->product)
                                        <a href="{{ route('product.show', $item->product->id) }}"
                                            class="line-clamp-2 text-sm font-semibold text-fg transition hover:text-cta">
                                            {{ $item->product->product_name }}
                                        </a>
                                    @else
                                        <p class="text-sm font-semibold text-fg">Produk tidak tersedia</p>
                                    @endif

                                    <p class="mt-1 text-xs text-fg-muted">
                                        {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <p class="text-sm font-semibold text-fg">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                </section>

                {{-- Ringkasan meta --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="info-pesanan">
                    <h2 id="info-pesanan" class="text-lg font-semibold text-fg">Informasi Pesanan</h2>

                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Nomor pesanan</dt>
                            <dd class="mt-1 font-mono text-fg">{{ $transaction->order_code }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Tanggal pemesanan</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->created_at->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Tanggal dikirim</dt>
                            <dd class="mt-1 text-fg">
                                {{ $transaction->status === 'sent' ? $transaction->updated_at->translatedFormat('d M Y') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Jumlah barang</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->items->count() }} produk</dd>
                        </div>
                    </dl>
                </section>

                {{-- Pengiriman --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="info-pengiriman">
                    <h2 id="info-pengiriman" class="text-lg font-semibold text-fg">Informasi Pengiriman</h2>

                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Nomor resi</dt>
                            <dd class="mt-1 font-mono text-fg">{{ $transaction->tracking_number ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kurir</dt>
                            <dd class="mt-1 uppercase text-fg">{{ $transaction->courier ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Layanan</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->courier_service ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kode pos</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->postal_code ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Alamat lengkap</dt>
                            <dd class="mt-1 leading-relaxed text-fg">{{ $transaction->address ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Provinsi</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->province ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kota / Kabupaten</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->city ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kecamatan</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->district ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Ongkos kirim</dt>
                            <dd class="mt-1 text-fg">Rp
                                {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</dd>
                            @if ($transaction->distance_km)
                                <dd class="mt-0.5 text-xs text-fg-muted">
                                    ± {{ number_format($transaction->distance_km, 2, ',', '.') }} km dari toko
                                </dd>
                            @endif
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Metode pembayaran</dt>
                            <dd class="mt-1 text-fg">{{ $transaction->paymentMethodLabel() }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- Timeline --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="pelacakan">
                    <h2 id="pelacakan" class="text-lg font-semibold text-fg">Pelacakan Pesanan</h2>

                    @if ($isCancelled)
                        <div class="mt-4">
                            <x-ui.alert variant="danger" title="Pesanan dibatalkan">
                                Pesanan ini telah dibatalkan. Stok produk sudah dikembalikan.
                            </x-ui.alert>
                        </div>
                    @else
                        <ol class="mt-5 space-y-0 sm:flex sm:items-start sm:justify-between sm:gap-2">
                            @foreach ($steps as $index => $step)
                                @php
                                    $isActive = $index <= $currentStep;
                                    $isLast = $loop->last;
                                @endphp

                                <li class="relative flex gap-3 pb-6 last:pb-0 sm:flex-1 sm:flex-col sm:items-center sm:gap-2 sm:pb-0 sm:text-center">
                                    <div class="relative flex flex-col items-center">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $isActive ? 'bg-cta text-on-cta' : 'bg-surface-soft text-fg-muted' }}">
                                            @if ($isActive)
                                                <x-ui.icon name="check" class="h-5 w-5" />
                                            @else
                                                {{ $loop->iteration }}
                                            @endif
                                        </span>

                                        @if (! $isLast)
                                            <span
                                                class="absolute top-10 h-full w-0.5 {{ $isActive && $index + 1 <= $currentStep ? 'bg-cta' : 'bg-line' }} sm:hidden"
                                                aria-hidden="true"></span>
                                        @endif
                                    </div>

                                    <div class="sm:mt-1">
                                        <p class="text-sm font-semibold {{ $isActive ? 'text-fg' : 'text-fg-muted' }}">
                                            {{ $step['label'] }}</p>
                                        <p class="text-xs text-fg-muted">
                                            {{ $step['date']->translatedFormat('d M Y') }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            </div>

            {{-- Ringkasan biaya --}}
            <aside class="lg:sticky lg:top-24">
                <div class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-semibold text-fg">Ringkasan Pembayaran</h2>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-fg-muted">Subtotal produk</dt>
                            <dd class="font-medium text-fg">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-fg-muted">Ongkos kirim</dt>
                            <dd class="font-medium text-fg">Rp
                                {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                            <dt class="font-medium text-fg">Total</dt>
                            <dd class="text-xl font-bold text-fg">Rp
                                {{ number_format($transaction->total ?? 0, 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 rounded-card bg-surface-muted p-3 text-sm">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-fg-muted">Status pembayaran</span>
                            <x-ui.status-badge type="payment" :status="$transaction->payment_status" />
                        </div>
                    </div>

                    <div class="mt-5 space-y-2">
                        @if ($transaction->payment_status === 'pending' && $transaction->status !== 'done' && ! $isCancelled)
                            <x-ui.button :href="route('checkout.payment', $transaction->id)" block size="lg" variant="secondary">
                                Lihat Cara Pembayaran
                            </x-ui.button>
                        @elseif ($transaction->payment_status === 'paid')
                            <div class="inline-flex items-center gap-2 rounded-card bg-success-soft px-4 py-2 text-sm font-medium text-success">
                                <x-ui.icon name="check-circle" class="h-4 w-4" />
                                Sudah Lunas
                            </div>
                        @endif

                        <x-ui.button :href="route('orders')" variant="outline" block>
                            Kembali ke Pesanan
                        </x-ui.button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
