<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tarif Ongkos Kirim Berbasis Jarak dan Kurir
    |--------------------------------------------------------------------------
    |
    | Ongkir dihitung dari jarak garis lurus (Haversine) antara titik lokasi
    | toko dan titik pengiriman pelanggan, lalu dikalikan tarif kurir yang
    | dipilih pelanggan.
    |
    | rumus: biaya = base_fare_kurir + (jarak_km_dibulatkan * per_km_kurir)
    |
    | Setiap kurir dapat memiliki tarif dasar dan tarif per kilometer yang
    | berbeda. Nilai di bawah dapat ditimpa melalui .env.
    |
    */
    'base_fare' => (int) env('SHIPPING_BASE_FARE', 5000),
    'per_km' => (int) env('SHIPPING_PER_KM', 2500),
    'minimum_km' => (float) env('SHIPPING_MINIMUM_KM', 1),
    'round_up' => (bool) env('SHIPPING_ROUND_UP_KM', true),
    'default_courier' => env('SHIPPING_DEFAULT_COURIER', 'JNE'),

    /*
    |--------------------------------------------------------------------------
    | Daftar Kurir dan Tarifnya
    |--------------------------------------------------------------------------
    |
    | Key array dipakai sebagai nilai `courier` pada transaksi. Label dipakai
    | untuk tampilan, sedangkan service dicatat sebagai layanan kurir.
    |
    */
    'couriers' => [
        'JNE' => [
            'label' => 'JNE',
            'service' => 'Reguler',
            'base_fare' => (int) env('SHIPPING_JNE_BASE_FARE', 6000),
            'per_km' => (int) env('SHIPPING_JNE_PER_KM', 2600),
        ],
        'J&T' => [
            'label' => 'J&T Express',
            'service' => 'Reguler',
            'base_fare' => (int) env('SHIPPING_JNT_BASE_FARE', 5500),
            'per_km' => (int) env('SHIPPING_JNT_PER_KM', 2400),
        ],
        'Sicepat' => [
            'label' => 'SiCepat',
            'service' => 'Reguler',
            'base_fare' => (int) env('SHIPPING_SICEPAT_BASE_FARE', 5000),
            'per_km' => (int) env('SHIPPING_SICEPAT_PER_KM', 2300),
        ],
        'AnterAja' => [
            'label' => 'AnterAja',
            'service' => 'Reguler',
            'base_fare' => (int) env('SHIPPING_ANTERAJA_BASE_FARE', 4800),
            'per_km' => (int) env('SHIPPING_ANTERAJA_PER_KM', 2200),
        ],
        'COD / Kurir Toko' => [
            'label' => 'COD / Kurir Toko',
            'service' => 'Kurir Toko',
            'base_fare' => (int) env('SHIPPING_COD_BASE_FARE', 4000),
            'per_km' => (int) env('SHIPPING_COD_PER_KM', 2000),
        ],
    ],
];
