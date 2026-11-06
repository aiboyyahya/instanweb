<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
        });

        // Truncate postal codes longer dari 5 karakter sebelum mengubah kolom.
        DB::table('transactions')
            ->whereNotNull('postal_code')
            ->whereRaw('LENGTH(postal_code) > 5')
            ->orderBy('id')
            ->each(function ($row) {
                DB::table('transactions')
                    ->where('id', $row->id)
                    ->update(['postal_code' => substr($row->postal_code, 0, 5)]);
            });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('postal_code', 5)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->text('address')->nullable(false)->change();
            $table->string('postal_code', 5)->nullable(false)->change();
        });
    }
};
