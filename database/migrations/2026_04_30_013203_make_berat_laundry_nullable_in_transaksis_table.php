<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('transaksis', 'berat_laundry')) {
            DB::statement('ALTER TABLE transaksis MODIFY COLUMN berat_laundry DOUBLE NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaksis', 'berat_laundry')) {
            DB::statement('ALTER TABLE transaksis MODIFY COLUMN berat_laundry DOUBLE NOT NULL DEFAULT 0');
        }
    }
};
