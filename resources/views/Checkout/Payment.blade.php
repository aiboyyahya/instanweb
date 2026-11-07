@extends('layouts.app')

@section('title', 'Pembayaran Pesanan | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $store = store();
        $whatsappNumber = $store && ! empty($store->whatsapp) ? preg_replace('/[^0-9]/', '', $store->whatsapp) : null;
        $subtotal = $transaction->total - ($transaction->shipping_cost ?? 0);
        $isPaid = in_array($transaction->payment_status, ['paid', 'settlement'], true);
        $confirmMessage = rawurlencode(
            'Halo, saya ingin mengonfirmasi pembayaran pesanan ' .
                $transaction->order_code .
                ' dengan total Rp ' .
                number_format($transaction->total, 0, ',', '.') .
                '.',
        );
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Pesanan Saya', 'url' => route('orders')],
            ['label' => 'Pembayaran'],
        ]" />

        <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Pembayaran</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-fg">Pesanan #{{ $transaction->order_code }}</h1>
                <p class="mt-2 text-sm text-fg-muted">
                    Dibuat {{ $transaction->created_at->format('d M Y, H:i') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-ui.status-badge type="order" :status="$transaction->status" />
                <x-ui.status-badge type="payment" :status="$transaction->payment_status" />
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2 lg:items-start">
            <div class="space-y-5">
                {{-- Produk --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="produk-pesanan">
                    <h2 id="produk-pesanan" class="text-lg font-semibold text-fg">Detail Produk</h2>

                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($transaction->items as $item)
                            @php
                                $image = $item->product && $item->product->image
                                    ? asset('storage/' . $item->product->image)
                                    : asset('images/gambar3.jpeg');
                            @endphp

                            <li class="flex gap-3 py-3 first:pt-0">
                                <img src="{{ $image }}" alt=""
                                    class="h-16 w-16 shrink-0 rounded-lg border border-line object-cover">

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-fg">
                                        {{ $item->product->product_name ?? 'Produk tidak tersedia' }}
                                    </p>
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

                {{-- Pengiriman --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="pengiriman">
                    <h2 id="pengiriman" class="text-lg font-semibold text-fg">Informasi Pengiriman</h2>

                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Alamat</dt>
                            <dd class="mt-1 leading-relaxed text-fg">{{ $transaction->address }}</dd>
                        </div>

                        @if ($transaction->province || $transaction->city || $transaction->district)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Wilayah</dt>
                                <dd class="mt-1 text-fg">
                                    {{ $transaction->district }}, {{ $transaction->city }}, {{ $transaction->province }}
                                </dd>
                            </div>
                        @endif

                        @if ($transaction->postal_code)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kode Pos</dt>
                                <dd class="mt-1 text-fg">{{ $transaction->postal_code }}</dd>
                            </div>
                        @endif

                        @if ($transaction->courier)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Kurir</dt>
                                <dd class="mt-1 uppercase text-fg">{{ $transaction->courier }}</dd>
                            </div>
                        @endif

                        @if ($transaction->courier_service)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Layanan</dt>
                                <dd class="mt-1 text-fg">{{ $transaction->courier_service }}</dd>
                            </div>
                        @endif

                        @if ($transaction->tracking_number)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Nomor Resi</dt>
                                <dd class="mt-1 font-mono text-fg">{{ $transaction->tracking_number }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </div>

            <div class="space-y-5 lg:sticky lg:top-24">
                {{-- Ringkasan --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="ringkasan-pembayaran">
                    <h2 id="ringkasan-pembayaran" class="text-lg font-semibold text-fg">Ringkasan Pembayaran</h2>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-fg-muted">Subtotal</dt>
                            <dd class="font-medium text-fg">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-fg-muted">Biaya pengiriman</dt>
                            <dd class="font-medium text-fg">Rp
                                {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-line pt-3">
                            <dt class="font-medium text-fg">Total pembayaran</dt>
                            <dd class="text-2xl font-bold text-fg">Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>

                    <div
                        class="mt-4 flex items-center justify-between gap-3 rounded-card border border-line bg-surface-muted px-4 py-3">
                        <span class="text-sm text-fg-secondary">Status pembayaran</span>
                        <x-ui.status-badge type="payment" :status="$transaction->payment_status" />
                    </div>
                </section>

                {{-- Panel pembayaran --}}
                <section class="rounded-panel border border-line bg-surface p-5 shadow-sm sm:p-6"
                    aria-labelledby="panel-pembayaran">
                    <h2 id="panel-pembayaran" class="text-lg font-semibold text-fg">Cara Pembayaran</h2>

                    @if ($isPaid)
                        <x-ui.alert variant="success" title="Pembayaran diterima">
                            Pembayaran pesanan ini sudah tercatat lunas. Penjual akan memproses pengiriman.
                        </x-ui.alert>
                    @elseif (in_array($transaction->status, ['done', 'cancelled'], true))
                        <x-ui.alert variant="{{ $transaction->status === 'done' ? 'info' : 'warning' }}"
                            title="{{ $transaction->status === 'done' ? 'Pesanan selesai' : 'Pesanan dibatalkan' }}">
                            {{ $transaction->status === 'done'
                                ? 'Pesanan ini sudah selesai diproses oleh penjual.'
                                : 'Pesanan ini sudah dibatalkan. Hubungi penjual jika ada pertanyaan.' }}
                        </x-ui.alert>
                    @else
                        <p class="mt-2 text-sm text-fg-muted">
                            Konfirmasi pembayaran Anda ke penjual melalui channel berikut setelah melakukan transfer atau siap menerima barang.
                        </p>

                        <div class="mt-4 space-y-3 rounded-card border border-line bg-surface-muted p-4 text-sm">
                            @switch($transaction->payment_method)
                                @case('transfer_bank')
                                    <div>
                                        <p class="font-semibold text-fg">Transfer Bank</p>
                                        <p class="mt-1 text-fg-secondary">
                                            Silakan transfer ke rekening penjual, lalu kirim bukti transfer melalui WhatsApp.
                                        </p>
                                    </div>
                                @break
                                @case('qris')
                                    <div>
                                        <p class="font-semibold text-fg">QRIS / E-Wallet</p>
                                        <p class="mt-1 text-fg-secondary">
                                            Scanner kode QRIS yang dikirim penjual melalui WhatsApp untuk melakukan pembayaran.
                                        </p>
                                    </div>
                                @break
                                @case('cod')
                                    <div>
                                        <p class="font-semibold text-fg">Bayar di Tempat (COD)</p>
                                        <p class="mt-1 text-fg-secondary">
                                            Bayar tunai saat barang diterima oleh kurir atau penjual. Status pembayaran akan dikonfirmasi otomatis setelah barang sampai.
                                        </p>
                                    </div>
                                @break
                                @default
                                    <div>
                                        <p class="font-semibold text-fg">Konfirmasi Manual</p>
                                        <p class="mt-1 text-fg-secondary">
                                            Hubungi penjual untuk instruksi pembayaran dan kirim bukti pembayaran melalui WhatsApp.
                                        </p>
                                    </div>
                            @endswitch
                        </div>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-2 border-t border-line pt-4">
                        <x-ui.button :href="route('orders')" variant="ghost" size="sm">Lihat Pesanan</x-ui.button>
                        <x-ui.button :href="route('home')" variant="ghost" size="sm">Kembali ke Beranda</x-ui.button>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
