<?php

use App\Models\Product;
use App\Models\Store;

if (!function_exists('cart_items')) {
    /**
     * Item keranjang milik toko yang sedang aktif.
     *
     * Keranjang tetap disimpan dalam satu session, tetapi hanya item dari
     * toko aktif yang ditampilkan dan diproses agar belanja antar toko tidak
     * tercampur.
     */
    function cart_items(?Store $store = null): array
    {
        $cart = session()->get('cart', []);
        $store ??= store_context();

        if (! $store || empty($cart)) {
            return $cart;
        }

        $missingStoreIds = [];

        foreach ($cart as $productId => $item) {
            if (! array_key_exists('store_id', $item)) {
                $missingStoreIds[] = $productId;
            }
        }

        $productStores = empty($missingStoreIds)
            ? collect()
            : Product::query()
                ->whereIn('id', $missingStoreIds)
                ->pluck('store_id', 'id');

        return array_filter(
            $cart,
            function ($item, $productId) use ($store, $productStores) {
                $itemStoreId = $item['store_id'] ?? $productStores[$productId] ?? null;

                return (int) $itemStoreId === (int) $store->id;
            },
            ARRAY_FILTER_USE_BOTH,
        );
    }
}

if (!function_exists('store_context')) {
    /**
     * Toko yang menjadi konteks navigasi saat ini.
     *
     * Storefront dan detail produk mengikuti toko pada URL/produk. Halaman
     * akun, keranjang, dan checkout mengikuti toko terakhir yang dibuka agar
     * navigasi tidak kembali ke beranda platform di tengah alur belanja.
     */
    function store_context(): ?Store
    {
        $routeStore = request()->route()?->parameter('store');

        if ($routeStore instanceof Store) {
            return $routeStore;
        }

        if (is_string($routeStore) && $routeStore !== '') {
            $resolvedStore = Store::where('slug', $routeStore)->first();

            if ($resolvedStore) {
                return $resolvedStore;
            }
        }

        // Halaman detail produk mengikuti toko pemilik produk.
        if (request()->routeIs('product.show') && ($productId = request()->route('id'))) {
            $productStore = Product::query()->with('store')->find($productId)?->store;

            if ($productStore) {
                return $productStore;
            }
        }

        // Pemilik toko selalu memakai tokonya sendiri di halaman profil.
        if (request()->routeIs('profil') && ($user = request()->user())) {
            if ($user->store) {
                return $user->store;
            }

            if ($user->role === 'admin') {
                return Store::first();
            }
        }

        // Halaman lanjutan transaksi memakai toko yang terakhir dipilih.
        if (
            request()->routeIs([
                'profil',
                'cart',
                'checkout.page',
                'checkout.shipping-estimate',
                'checkout.payment',
                'checkout.success',
                'orders',
                'order.detail',
                'ratings.create',
            ])
            && ($storeId = session('active_store_id'))
        ) {
            return Store::find($storeId);
        }

        return null;
    }
}

if (!function_exists('store')) {
    /**
     * Ambil toko aktif.
     *
     * Jika request sedang berada dalam konteks toko tertentu, gunakan toko
     * tersebut. Selain itu, gunakan toko utama agar halaman lama tetap
     * berfungsi (single-store compatibility).
     */
    function store()
    {
        $contextStore = store_context();

        if ($contextStore) {
            return $contextStore;
        }

        // Cache agar tidak query berulang
        return cache()->rememberForever('store_data', function () {
            return Store::first();
        });
    }
}
