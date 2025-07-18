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
        Schema::table('transaksis', function (Blueprint $table) {
            // Memperbesar ukuran kolom agar bisa menampung teks yang lebih panjang
            $table->string('status_order', 50)->change();
            $table->string('status_pembayaran', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Mengembalikan ke ukuran default jika migrasi di-rollback
            $table->string('status_order')->change();
            $table->string('status_pembayaran')->change();
        });
    }
};
