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
            // Drop existing foreign key
            $table->dropForeign(['id_user']);
            // Make id_user nullable
            $table->unsignedBigInteger('id_user')->nullable()->change();
            // Add foreign key with set null on delete
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['id_user']);
            // Make id_user not nullable
            $table->unsignedBigInteger('id_user')->nullable(false)->change();
            // Add back cascade delete
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
