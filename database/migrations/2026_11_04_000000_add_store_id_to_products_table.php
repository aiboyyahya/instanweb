<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hubungkan produk dengan toko pemiliknya.
     *
     * Kolom nullable dan backfill menjaga kompatibilitas data produk lama
     * yang dibuat sebelum konsep multi-toko diterapkan.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')
                ->nullable()
                ->after('id')
                ->constrained('stores')
                ->nullOnDelete();
        });

        $firstStoreId = DB::table('stores')->orderBy('id')->value('id');

        if ($firstStoreId) {
            DB::table('products')
                ->whereNull('store_id')
                ->update(['store_id' => $firstStoreId]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });
    }
};
