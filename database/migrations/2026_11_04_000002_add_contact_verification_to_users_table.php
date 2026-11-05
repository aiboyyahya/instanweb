<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan status dan kode verifikasi email/nomor telepon.
     *
     * Akun lama ditandai terverifikasi agar alur yang sudah berjalan tidak
     * terputus. Akun baru wajib melewati verifikasi kode.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('phone_number');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('email_verification_code')->nullable();
            $table->timestamp('email_verification_expires_at')->nullable();
            $table->timestamp('email_verification_sent_at')->nullable();
            $table->string('phone_verification_code')->nullable();
            $table->timestamp('phone_verification_expires_at')->nullable();
            $table->timestamp('phone_verification_sent_at')->nullable();
        });

        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);

        DB::table('users')
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->whereNull('phone_verified_at')
            ->update(['phone_verified_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'email_verification_code',
                'email_verification_expires_at',
                'email_verification_sent_at',
                'phone_verification_code',
                'phone_verification_expires_at',
                'phone_verification_sent_at',
            ]);
        });
    }
};
