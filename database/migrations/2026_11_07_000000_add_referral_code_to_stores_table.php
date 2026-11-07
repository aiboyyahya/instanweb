<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique()->after('slug');
        });

        // Generate kode referral untuk toko yang sudah ada
        DB::table('stores')->orderBy('id')->get(['id'])->each(function ($store) {
            DB::table('stores')
                ->where('id', $store->id)
                ->update(['referral_code' => $this->generateCode()]);
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropUnique(['referral_code']);
            $table->dropColumn('referral_code');
        });
    }

    /**
     * Buat kode referral unik sepanjang 8 karakter.
     */
    private function generateCode(): string
    {
        do {
            $code = Str::random(8);
        } while (\App\Models\Store::where('referral_code', $code)->exists());

        return $code;
    }
};
