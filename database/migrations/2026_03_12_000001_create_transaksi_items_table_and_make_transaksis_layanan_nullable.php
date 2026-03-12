<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('layanan_id');

            // mengikuti konsep yang ada: harga per kg/pcs * berat
            $table->decimal('berat', 10, 2);
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            $table->foreign('transaksi_id')->references('id')->on('transaksis')->onDelete('cascade');
            $table->foreign('layanan_id')->references('id')->on('layanans')->onDelete('restrict');
        });

        // Transaksi lama tetap kompatibel, transaksi baru bisa multi layanan (id_layanan = null)
        Schema::table('transaksis', function (Blueprint $table) {
            // jika sudah ada foreign key, drop dulu sebelum change()
            $table->dropForeign(['id_layanan']);
            $table->unsignedBigInteger('id_layanan')->nullable()->change();
            $table->foreign('id_layanan')->references('id')->on('layanans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['id_layanan']);
            $table->unsignedBigInteger('id_layanan')->nullable(false)->change();
            $table->foreign('id_layanan')->references('id')->on('layanans')->onDelete('cascade');
        });

        Schema::dropIfExists('transaksi_items');
    }
};

