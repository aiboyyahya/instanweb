@extends('layouts.app')

@section('title', 'Pesanan Saya | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $tabs = [
            'all' => ['label' => 'Semua', 'empty' => 'Belum ada pesanan.', 'count' => $transactions->count()],
            'pending' => [
                'label' => 'Belum Bayar',
                'empty' => 'Belum ada pesanan yang menunggu pembayaran.',
                'count' => $transactions->where('status', 'pending')->count(),
            ],
            'packing' => [
                'label' => 'Dikemas',
                'empty' => 'Belum ada pesanan yang sedang dikemas.',
                'count' => $transactions->where('status', 'packing')->count(),
            ],
            'sent' => [
                'label' => 'Dikirim',
                'empty' => 'Belum ada pesanan yang sedang dikirim.',
                'count' => $transactions->where('status', 'sent')->count(),
            ],
            'done' => [
                'label' => 'Selesai',
                'empty' => 'Belum ada pesanan yang telah selesai.',
                'count' => $transactions->where('status', 'done')->count(),
            ],
        ];
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.page-header eyebrow="Akun saya" title="Pesanan Saya"
            description="Pantau status pesanan dan konfirmasi pembayaran Anda.">
            <x-ui.button :href="route('products')" variant="outline">
                <x-ui.icon name="shopping-bag" class="h-4 w-4" /> Belanja Lagi
            </x-ui.button>
        </x-ui.page-header>

        {{-- Tab status --}}
        <div class="mt-8 overflow-x-auto">
            <div role="tablist" aria-label="Filter status pesanan"
                class="flex min-w-max items-center gap-1 border-b border-line pb-px">
                @foreach ($tabs as $key => $tab)
                    <a href="{{ route('orders', ['status' => $key]) }}" role="tab"
                        data-order-tab="{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="orders-panel" tabindex="{{ $loop->first ? '0' : '-1' }}"
                        class="order-tab inline-flex min-h-11 items-center gap-2 rounded-t-lg border-b-2 px-4 text-sm font-medium transition-colors {{ $loop->first ? 'border-cta text-fg' : 'border-transparent text-fg-muted hover:text-fg' }}">
                        {{ $tab['label'] }}
                        <span
                            class="rounded-full bg-surface-soft px-1.5 py-0.5 text-[11px] font-semibold text-fg-secondary">{{ $tab['count'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div id="orders-panel" role="tabpanel" class="mt-6 space-y-5">
            @forelse ($transactions as $transaction)
                @php
                    $statusClass = match ($transaction->status) {
                        'packing' => 'border-info/30',
                        'sent' => 'border-shipping/30',
                        'done' => 'border-success/30',
                        'cancelled' => 'border-danger/30',
                        default => 'border-warning/30',
                    };

                    $allRated =
                        $transaction->status === 'done' &&
                        $transaction->items->every(function ($item) use ($transaction) {
                            return \App\Models\Rating::where('customer_id', auth()->id())
                                ->where('product_id', $item->product->id)
                                ->where('transaction_id', $transaction->id)
                                ->exists();
                        });
                @endphp

                <article data-order-item data-status="{{ $transaction->status }}"
                    class="rounded-panel border {{ $statusClass }} bg-surface shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                        <div>
                            <p class="font-mono text-sm font-semibold text-fg">#{{ $transaction->order_code }}</p>
                            <p class="mt-1 text-xs text-fg-muted">
                                {{ $transaction->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.status-badge type="order" :status="$transaction->status" />
                            <x-ui.status-badge type="payment" :status="$transaction->payment_status" />
                        </div>
                    </div>

                    @if ($transaction->status === 'sent' && $transaction->tracking_number)
                        <div class="mx-4 mt-4 flex items-start gap-3 rounded-card border border-shipping/30 bg-shipping-soft p-3 sm:mx-5">
                            <x-ui.icon name="truck" class="mt-0.5 h-5 w-5 text-shipping" />
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-shipping">Nomor resi</p>
                                <p class="mt-0.5 font-mono text-sm text-shipping">{{ $transaction->tracking_number }}</p>
                            </div>
                        </div>
                    @endif

                    <ul class="divide-y divide-line px-4 sm:px-5">
                        @foreach ($transaction->items as $item)
                            @php
                                $image = $item->product && $item->product->image
                                    ? asset('storage/' . $item->product->image)
                                    : asset('images/gambar3.jpeg');
                            @endphp

                            <li class="flex items-center gap-3 py-4">
                                <img src="{{ $image }}" alt=""
                                    class="h-16 w-16 shrink-0 rounded-lg border border-line object-cover">

                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-fg">
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

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line px-4 py-4 sm:px-5">
                        <div>
                            <p class="text-xs text-fg-muted">Total pembayaran</p>
                            <p class="text-lg font-bold text-fg">Rp
                                {{ number_format($transaction->total, 0, ',', '.') }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.button :href="route('order.detail', $transaction->id)" variant="outline" size="sm">
                                <x-ui.icon name="search" class="h-4 w-4" /> Detail
                            </x-ui.button>

                            @if ($transaction->status === 'done' && ! $allRated)
                                <x-ui.button :href="route('ratings.create', [
                                    'product_id' => $transaction->items->first()->product->id,
                                    'transaction_id' => $transaction->id,
                                ])" size="sm">
                                    Kirim Ulasan
                                </x-ui.button>
                            @endif

                            @if ($transaction->status === 'pending')
                                <form method="POST" action="{{ route('order.delete', $transaction->id) }}"
                                    data-confirm="Batalkan pesanan #{{ $transaction->order_code }}? Stok produk akan dikembalikan.">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" size="sm">Batalkan</x-ui.button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div data-orders-empty="all">
                    <x-ui.empty-state title="Belum ada pesanan"
                        description="Pesanan Anda akan tampil di sini setelah Anda melakukan checkout." icon="package">
                        <x-ui.button :href="route('products')">Mulai Belanja</x-ui.button>
                    </x-ui.empty-state>
                </div>
            @endforelse

            @foreach ($tabs as $key => $tab)
                @if ($key !== 'all')
                    <div data-orders-empty="{{ $key }}" class="hidden">
                        <x-ui.empty-state :title="'Belum ada pesanan ' . strtolower($tab['label'])"
                            :description="$tab['empty']" icon="package">
                            <x-ui.button :href="route('products')" variant="outline">Lihat Produk</x-ui.button>
                        </x-ui.empty-state>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = Array.from(document.querySelectorAll('[data-order-tab]'));
            const items = Array.from(document.querySelectorAll('[data-order-item]'));
            const emptyStates = Array.from(document.querySelectorAll('[data-orders-empty]'));

            if (!tabs.length) {
                return;
            }

            function activate(status) {
                tabs.forEach(function (tab) {
                    const active = tab.dataset.orderTab === status;
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    tab.setAttribute('tabindex', active ? '0' : '-1');
                    tab.classList.toggle('border-cta', active);
                    tab.classList.toggle('text-fg', active);
                    tab.classList.toggle('border-transparent', !active);
                    tab.classList.toggle('text-fg-muted', !active);
                });

                let visible = 0;

                items.forEach(function (item) {
                    const show = status === 'all' || item.dataset.status === status;
                    item.classList.toggle('hidden', !show);
                    if (show) {
                        visible += 1;
                    }
                });

                emptyStates.forEach(function (empty) {
                    const show = visible === 0 && empty.dataset.ordersEmpty === status;
                    empty.classList.toggle('hidden', !show);
                });
            }

            tabs.forEach(function (tab, index) {
                tab.addEventListener('click', function (event) {
                    event.preventDefault();
                    activate(tab.dataset.orderTab);
                });

                tab.addEventListener('keydown', function (event) {
                    if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
                        return;
                    }

                    event.preventDefault();
                    const direction = event.key === 'ArrowRight' ? 1 : -1;
                    const next = tabs[(index + direction + tabs.length) % tabs.length];
                    next.focus();
                    activate(next.dataset.orderTab);
                });
            });

            activate('all');
        });
    </script>
@endpush
