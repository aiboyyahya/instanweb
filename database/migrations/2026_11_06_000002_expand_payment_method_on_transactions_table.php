<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Metode pembayaran kini mengikuti pilihan pelanggan pada transaksi
     * (mis. transfer bank, COD, QRIS). Kolom enum lama diperluas menjadi
     * string agar nilai baru dapat disimpan tanpa memutus data lama.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('manual')->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('manual')->change();
        });
    }
};
