<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Artikel dimiliki oleh toko masing-masing sehingga setiap storefront
     * hanya menampilkan artikel dari tokonya sendiri.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('store_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });

        // Backfill: ikuti toko milik penulis artikel, lalu toko pertama.
        $firstStoreId = DB::table('stores')->orderBy('id')->value('id');

        DB::table('articles')->orderBy('id')->get()->each(function ($article) use ($firstStoreId) {
            $storeId = DB::table('stores')
                ->where('user_id', $article->user_id)
                ->value('id');

            $storeId = $storeId ?: $firstStoreId;

            if ($storeId) {
                DB::table('articles')
                    ->where('id', $article->id)
                    ->update(['store_id' => $storeId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });
    }
};
