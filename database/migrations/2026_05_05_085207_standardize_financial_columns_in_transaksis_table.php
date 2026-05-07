<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->decimal('total_harga', 12, 2)->change();
            $table->decimal('jumlah_bayar', 12, 2)->change();
            $table->decimal('sisa_bayar', 12, 2)->change();
            $table->decimal('subtotal', 12, 2)->change();
            $table->decimal('potongan', 12, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->double('total_harga')->change();
            $table->double('jumlah_bayar')->change();
            $table->double('sisa_bayar')->change();
            $table->decimal('subtotal', 10, 2)->change();
            $table->decimal('potongan', 10, 2)->change();
        });
    }
};
