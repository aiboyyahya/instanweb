<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyelaraskan tabel transaksi pada basis data yang pernah mengalami
 * perubahan manual. Migrasi historis mungkin tercatat sudah dijalankan
 * tetapi kolomnya hilang, sehingga kolom yang dibutuhkan aplikasi
 * ditambahkan kembali bila belum ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('transactions', 'phone_number')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('phone_number')->nullable()->after('customer_id');
            });
        }

        if (! Schema::hasColumn('transactions', 'shipping_cost')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->integer('shipping_cost')->nullable()->after('total');
            });
        }

        if (! Schema::hasColumn('transactions', 'snap_token')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('snap_token')->nullable()->after('payment_method');
            });
        }

        if (! Schema::hasColumn('transactions', 'tracking_number')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('tracking_number')->nullable()->after('courier_service');
            });
        }

        if (! Schema::hasColumn('transactions', 'payment_status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])
                    ->default('pending')
                    ->after('payment_method');
            });
        }
    }

    public function down(): void
    {
        // Kolom-kolom ini adalah bagian dari skema aplikasi, bukan tambahan
        // eksklusif migrasi ini. Rollback tidak menghapusnya.
    }
};
