<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('transaksis', 'status_pemabayaran')) {
            // Fix typo in column name and change to varchar(50) in one statement
            DB::statement("ALTER TABLE transaksis CHANGE status_pemabayaran status_pembayaran VARCHAR(50) NOT NULL DEFAULT 'DP'");
        } elseif (Schema::hasColumn('transaksis', 'status_pembayaran')) {
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_pembayaran VARCHAR(50) NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaksis', 'status_pembayaran')) {
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_pembayaran VARCHAR(255) NOT NULL");
        }
    }
};
