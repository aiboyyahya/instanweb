<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('district');
            }

            if (! Schema::hasColumn('transactions', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }

            if (! Schema::hasColumn('transactions', 'distance_km')) {
                $afterColumn = Schema::hasColumn('transactions', 'shipping_cost')
                    ? 'shipping_cost'
                    : 'total';

                $table->decimal('distance_km', 8, 2)->nullable()->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            foreach (['distance_km', 'longitude', 'latitude'] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
