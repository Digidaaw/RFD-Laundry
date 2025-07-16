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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); 
            $table->unsignedBigInteger('id_pelanggan'); 
            $table->unsignedBigInteger('id_layanan'); 

            $table->date("tanggal_order");
            $table->double("berat_laundry");
            $table->double("total_harga");
            $table->double("jumlah_bayar");
            $table->double("sisa_bayar");
            $table->enum("status_order", ["Proses", "Selesai", "Diambil"]);
            $table->enum("status_pemabayaran", ["DP", "Lunas" ]);
            $table->timestamps();
            
            
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_pelanggan')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->foreign('id_layanan')->references('id')->on('layanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
