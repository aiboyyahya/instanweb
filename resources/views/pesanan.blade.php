@extends('layouts.app')

@section('title', 'Pesanan Saya')

@if (session('success'))
    <div class="fixed top-6 right-6 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-bounce">
        {{ session('success') }}
    </div>
@endif

@section('content')
    <section class="py-20 bg-white min-h-screen">
        <div class="max-w-6xl mx-auto px-6 lg:px-12 ">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-10 text-center gap-3 ">Pesanan Saya</h1>

            <div class="mb-8 border-b border-gray-200 ">
                <nav class="-mb-px flex justify-center space-x-8 " aria-label="Tabs">
                    <a href="{{ route('orders', ['status' => 'all']) }}"
                        class="tab-link {{ isset($status) && $status == 'all' ? 'border-orange-500 text-orange-600' : 'border-gray-100 text-gray-500 hover:text-gray-700 hover:border-gray-600' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-status="all">Semua</a>
                    <a href="{{ route('orders', ['status' => 'pending']) }}"
                        class="tab-link {{ isset($status) && $status == 'pending' ? 'border-orange-500 text-orange-600' : 'border-gray-100 text-gray-500 hover:text-gray-700 hover:border-gray-600' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-status="pending">Belum Bayar</a>
                    <a href="{{ route('orders', ['status' => 'packing']) }}"
                        class="tab-link {{ isset($status) && $status == 'packing' ? 'border-orange-500 text-orange-600' : 'border-gray-100 text-gray-500 hover:text-gray-700 hover:border-gray-600' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-status="packing">Dikemas</a>
                    <a href="{{ route('orders', ['status' => 'sent']) }}"
                        class="tab-link {{ isset($status) && $status == 'sent' ? 'border-orange-500 text-orange-600' : 'border-gray-100 text-gray-500 hover:text-gray-700 hover:border-gray-600' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-status="sent">Dikirim</a>
                    <a href="{{ route('orders', ['status' => 'done']) }}"
                        class="tab-link {{ isset($status) && $status == 'done' ? 'border-orange-500 text-orange-600' : 'border-gray-100 text-gray-500 hover:text-gray-700 hover:border-gray-600' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-status="done">Selesai</a>
                </nav>
            </div>

            <div id="orders-container" class="space-y-8">
                @foreach (['all', 'pending', 'packing', 'sent', 'done'] as $statusKey)
                    @php
                        $filtered = $statusKey === 'all' ? $transactions : $transactions->where('status', $statusKey);
                    @endphp

                    <div class="order-section {{ $statusKey }} space-y-8" data-status="{{ $statusKey }}">
                        @foreach ($filtered as $transaction)
                            @php
                                $statusClass = match ($transaction->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'packing' => 'bg-blue-100 text-blue-800',
                                    'sent' => 'bg-purple-100 text-purple-800',
                                    'done' => 'bg-green-100 text-green-800',
                                    default => 'bg-red-100 text-red-800',
                                };
                            @endphp

                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-lg transition order-item"
                                data-status="{{ $transaction->status }}">
                                <div class="flex justify-between mb-4">
                                    <p class="text-gray-500 text-sm font-medium">Tanggal:
                                        {{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                    <span
                                        class="inline-flex px-4 py-1.5 rounded-full text-sm font-medium {{ $statusClass }}">{{ ucfirst($transaction->status) }}</span>
                                </div>

                                @if ($transaction->status == 'sent' && $transaction->tracking_number)
                                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-blue-900">Nomor Resi</p>
                                                <p class="text-sm font-mono text-blue-800">
                                                    {{ $transaction->tracking_number }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="space-y-5">
                                    @foreach ($transaction->items as $item)
                                        <div class="bg-gray-50 p-5 rounded-2xl">
                                            <div class="flex items-center gap-4">
                                                <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/80' }}"
                                                    class="w-20 h-20 object-cover rounded-xl shadow-sm">
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-gray-900 text-lg truncate">
                                                        {{ $item->product->product_name }}</p>
                                                    <p class="text-sm text-gray-500 mt-1">Jumlah: {{ $item->quantity }} ×
                                                        Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                                </div>
                                                <p class="text-gray-800 font-bold text-lg">Rp
                                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6 flex justify-between items-center border-t pt-5">
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Total Pembayaran</p>
                                        <p class="text-xl font-bold text-orange-600">Rp
                                            {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('order.detail', $transaction->id) }}"
                                            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-md transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </a>

                                        @if ($transaction->status == 'done')
                                            @php
                                                $allRated = $transaction->items->every(function ($item) use (
                                                    $transaction,
                                                ) {
                                                    return \App\Models\Rating::where('customer_id', auth()->id())
                                                        ->where('product_id', $item->product->id)
                                                        ->where('transaction_id', $transaction->id)
                                                        ->exists();
                                                });
                                            @endphp
                                            @if (!$allRated)
                                                <a href="{{ route('ratings.create', ['product_id' => $transaction->items->first()->product->id, 'transaction_id' => $transaction->id]) }}"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                                    Kirim Ulasan
                                                </a>
                                            @endif
                                        @endif

                                        @if ($transaction->status == 'pending')
                                            <form method="POST" action="{{ route('order.delete', $transaction->id) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2.5 rounded-lg font-medium transition shadow-sm">Batal</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($filtered->isEmpty())
                            <div class="empty-state {{ $statusKey }}" data-status="{{ $statusKey }}">
                                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-5.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H3" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pesanan</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @switch($statusKey)
                                            @case('pending')
                                                Belum ada pesanan yang belum dibayar.
                                            @break

                                            @case('packing')
                                                Belum ada pesanan yang sedang dikemas.
                                            @break

                                            @case('sent')
                                                Belum ada pesanan yang sedang dikirim.
                                            @break

                                            @case('done')
                                                Belum ada pesanan yang telah selesai.
                                            @break
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-link');
            const sections = document.querySelectorAll('.order-section');
            const emptyStates = document.querySelectorAll('.empty-state');

            tabs.forEach(tab => {
                tab.addEventListener('click', e => {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('border-orange-500', 'text-orange-600'));
                    tab.classList.add('border-orange-500', 'text-orange-600');
                    const status = tab.dataset.status;
                    sections.forEach(section => {
                        section.classList.toggle('hidden', !(status === 'all' ? section
                            .dataset.status === 'all' : section.dataset.status ===
                            status));
                    });
                    emptyStates.forEach(emptyState => {
                        emptyState.classList.toggle('hidden', !(status !== 'all' &&
                            emptyState.dataset.status === status));
                    });
                });
            });

            document.querySelector('.tab-link[data-status="all"]').click();
        });
    </script>
@endsection
