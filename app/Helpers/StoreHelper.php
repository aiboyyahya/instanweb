<?php

use App\Models\Store;

if (!function_exists('store')) {
    /**
     * Ambil data store pertama
     */
    function store()
    {
        // Cache agar tidak query berulang
        return cache()->rememberForever('store_data', function () {
            return Store::first();
        });
    }
}
