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
        // If the table already exists (for example created manually or by a previous
        // partial migration run), skip creating it so migrations can continue.
        if (! Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
                $table->unsignedTinyInteger('rating')->default(1); // 1-5
                $table->text('comment')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();

                // One rating per customer per product per order
                $table->unique(['customer_id', 'product_id', 'transaction_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
