<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_items', 'unit_satuan')) {
                $table->string('unit_satuan', 20)->nullable()->after('layanan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_items', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_items', 'unit_satuan')) {
                $table->dropColumn('unit_satuan');
            }
        });
    }
};
