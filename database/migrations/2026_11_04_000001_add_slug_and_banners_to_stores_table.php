<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tambahkan alamat storefront unik dan maksimal tiga banner toko.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('store_name');
            $table->string('banner_1')->nullable()->after('logo');
            $table->string('banner_2')->nullable()->after('banner_1');
            $table->string('banner_3')->nullable()->after('banner_2');
        });

        $usedSlugs = [];

        DB::table('stores')->orderBy('id')->get(['id', 'store_name'])->each(function ($store) use (&$usedSlugs) {
            $base = Str::slug((string) ($store->store_name ?: 'toko'));
            $base = $base !== '' ? $base : 'toko';
            $slug = $base;
            $suffix = 2;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }

            $usedSlugs[] = $slug;

            DB::table('stores')->where('id', $store->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'banner_1', 'banner_2', 'banner_3']);
        });
    }
};
