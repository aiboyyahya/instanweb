<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('province')->nullable()->after('notes');
            $table->string('city')->nullable()->after('province');
            $table->string('district')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('district');
            $table->string('courier')->nullable()->after('postal_code');
            $table->string('courier_service')->nullable()->after('courier');
            $table->string('tracking_number')->nullable()->after('courier_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'district', 'postal_code', 'courier', 'courier_service', 'tracking_number']);
        });
    }
};
