@props([
    'status' => 'pending',
    'type' => 'order',
])

@php
    $orderMap = [
        'pending' => ['label' => 'Menunggu', 'variant' => 'warning', 'icon' => 'clock'],
        'packing' => ['label' => 'Dikemas', 'variant' => 'info', 'icon' => 'package'],
        'sent' => ['label' => 'Dikirim', 'variant' => 'shipping', 'icon' => 'truck'],
        'done' => ['label' => 'Selesai', 'variant' => 'success', 'icon' => 'check-circle'],
        'success' => ['label' => 'Selesai', 'variant' => 'success', 'icon' => 'check-circle'],
        'cancelled' => ['label' => 'Dibatalkan', 'variant' => 'danger', 'icon' => 'close'],
    ];

    $paymentMap = [
        'pending' => ['label' => 'Belum dibayar', 'variant' => 'warning', 'icon' => 'clock'],
        'paid' => ['label' => 'Lunas', 'variant' => 'success', 'icon' => 'check-circle'],
        'settlement' => ['label' => 'Lunas', 'variant' => 'success', 'icon' => 'check-circle'],
        'failed' => ['label' => 'Gagal', 'variant' => 'danger', 'icon' => 'alert-circle'],
        'deny' => ['label' => 'Ditolak', 'variant' => 'danger', 'icon' => 'alert-circle'],
        'expired' => ['label' => 'Kedaluwarsa', 'variant' => 'danger', 'icon' => 'alert-circle'],
        'cancel' => ['label' => 'Dibatalkan', 'variant' => 'neutral', 'icon' => 'close'],
    ];

    $map = $type === 'payment' ? $paymentMap : $orderMap;
    $meta = $map[$status] ?? [
        'label' => ucfirst(str_replace('_', ' ', (string) $status)),
        'variant' => 'neutral',
        'icon' => 'info',
    ];
@endphp

<x-ui.badge :variant="$meta['variant']">
    {{-- Status tidak hanya dibedakan warna: selalu ada teks + ikon. --}}
    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $meta['label'] }}
</x-ui.badge>
