<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kategori dimiliki oleh toko masing-masing sehingga nama kategori
     * yang sama boleh dipakai oleh toko yang berbeda.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('store_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['category_name']);
            $table->unique(['store_id', 'category_name']);
        });

        // Backfill: ikuti toko dari produk yang memakai kategori tersebut.
        // Jika kategori belum dipakai produk, assign ke toko pertama.
        $firstStoreId = DB::table('stores')->orderBy('id')->value('id');

        DB::table('categories')->orderBy('id')->get()->each(function ($category) use ($firstStoreId) {
            $storeId = DB::table('products')
                ->where('category_id', $category->id)
                ->whereNotNull('store_id')
                ->value('store_id');

            $storeId = $storeId ?: $firstStoreId;

            if ($storeId) {
                DB::table('categories')
                    ->where('id', $category->id)
                    ->update(['store_id' => $storeId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'category_name']);
            $table->unique('category_name');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });
    }
};
