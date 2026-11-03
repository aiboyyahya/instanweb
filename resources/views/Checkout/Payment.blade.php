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
                    <h2 id="panel-pembayaran" class="text-lg font-semibold text-fg">Selesaikan Pembayaran</h2>

                    <div id="payment-error" class="mt-4 hidden">
                        <x-ui.alert variant="danger" title="Pembayaran online belum dapat dimulai">
                            Silakan coba lagi, atau konfirmasi langsung ke penjual melalui WhatsApp.
                        </x-ui.alert>
                    </div>

                    <div id="payment-ready" class="mt-4 space-y-3">
                        @if ($isPaid)
                            <x-ui.alert variant="success" title="Pembayaran diterima">
                                Pembayaran pesanan ini sudah tercatat lunas. Penjual akan memproses pengiriman.
                            </x-ui.alert>
                        @else
                            <x-ui.alert variant="info" title="Menunggu konfirmasi pembayaran">
                                Pesanan sudah dibuat. Penjual akan mengirim rincian pembayaran melalui WhatsApp.
                            </x-ui.alert>

                            <button type="button" id="manual-pay-btn"
                                class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-field bg-cta px-4 text-sm font-semibold text-on-cta shadow-sm transition hover:bg-cta-hover">
                                <x-ui.icon name="check-circle" class="h-4 w-4" />
                                Coba Bayar Online
                            </button>

                            @if ($whatsappNumber)
                                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $confirmMessage }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                    <x-ui.icon name="chat" class="h-4 w-4" /> Konfirmasi via WhatsApp
                                </a>
                            @endif
                        @endif
                    </div>

                    <div id="payment-loading" class="mt-4 hidden items-center justify-center gap-3 rounded-card bg-surface-muted p-4 text-sm text-fg-secondary">
                        <span
                            class="h-5 w-5 animate-spin rounded-full border-2 border-line border-t-cta"
                            aria-hidden="true"></span>
                        Menyiapkan pembayaran...
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2 border-t border-line pt-4">
                        <x-ui.button :href="route('orders')" variant="ghost" size="sm">Lihat Pesanan</x-ui.button>
                        <x-ui.button :href="route('home')" variant="ghost" size="sm">Kembali ke Beranda</x-ui.button>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('manual-pay-btn');
            const loading = document.getElementById('payment-loading');
            const errorBox = document.getElementById('payment-error');
            const ready = document.getElementById('payment-ready');

            if (!button) {
                return;
            }

            function setLoading(isLoading) {
                loading?.classList.toggle('hidden', !isLoading);
                loading?.classList.toggle('flex', isLoading);
                button.disabled = isLoading;
                button.classList.toggle('opacity-60', isLoading);
            }

            function triggerPayment() {
                errorBox?.classList.add('hidden');
                setLoading(true);

                fetch("{{ route('transaction.getSnapToken', $transaction->id) }}", {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        if (!data.snap_token) {
                            throw new Error('Snap token tidak tersedia.');
                        }

                        if (typeof window.snap === 'undefined') {
                            throw new Error('Snap.js belum termuat.');
                        }

                        window.snap.pay(data.snap_token, {
                            onSuccess: function () {
                                window.location.href =
                                    "{{ route('checkout.success', $transaction->id) }}";
                            },
                            onPending: function () {
                                window.location.href =
                                    "{{ route('checkout.success', $transaction->id) }}";
                            },
                            onError: function () {
                                errorBox?.classList.remove('hidden');
                            },
                            onClose: function () {
                                setLoading(false);
                            }
                        });
                    })
                    .catch(function (error) {
                        console.error(error);
                        errorBox?.classList.remove('hidden');
                    })
                    .finally(function () {
                        setLoading(false);
                    });
            }

            button.addEventListener('click', triggerPayment);
        });
    </script>
@endpush
