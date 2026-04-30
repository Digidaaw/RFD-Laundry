<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('layanans', 'harga')) {
            DB::statement('ALTER TABLE layanans MODIFY COLUMN harga DOUBLE NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('layanans', 'harga')) {
            DB::statement('ALTER TABLE layanans MODIFY COLUMN harga DOUBLE NOT NULL DEFAULT 0');
        }
    }
};
