<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Carbon\Carbon;

class TestingSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ──────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            ['name' => 'Administrator', 'role' => 'admin', 'password' => 'admin123']
        );
        $kasir = User::firstOrCreate(
            ['username' => 'kasir'],
            ['name' => 'Kasir', 'role' => 'kasir', 'password' => 'kasir123']
        );

        // ── 2. Pelanggan ──────────────────────────────────────────
        $andi   = Pelanggan::firstOrCreate(['kontak' => '081234567890'], ['name' => 'Andi Susanto']);
        $budi   = Pelanggan::firstOrCreate(['kontak' => '089876543210'], ['name' => 'Budi Setiawan']);
        $citra  = Pelanggan::firstOrCreate(['kontak' => '085555555555'], ['name' => 'Citra Dewi']);
        $dewi   = Pelanggan::firstOrCreate(['kontak' => '082233445566'], ['name' => 'Dewi Lestari']);
        $eko    = Pelanggan::firstOrCreate(['kontak' => '087711223344'], ['name' => 'Eko Prasetyo']);

        // ── 3. Layanan ────────────────────────────────────────────
        $cuciKomplit = Layanan::firstOrCreate(
            ['name' => 'Cuci Komplit Reguler'],
            ['deskripsi' => 'Cuci dan Setrika Reguler', 'gambar' => '[]']
        );
        $setrika = Layanan::firstOrCreate(
            ['name' => 'Setrika Saja'],
            ['deskripsi' => 'Setrika Kilat', 'gambar' => '[]']
        );
        $cuciKilat = Layanan::firstOrCreate(
            ['name' => 'Cuci Kilat'],
            ['deskripsi' => 'Cuci Express 6 Jam', 'gambar' => '[]']
        );

        // ── 4. Transaksi ──────────────────────────────────────────
        // Helper buat transaksi
        $buat = function ($invoice, $user, $pelanggan, $layanan, $tgl, $subtotal, $potongan, $bayar, $status, $qty, $unit, $harga) {
            $total = $subtotal - $potongan;
            $sisa  = $total - $bayar;
            $t = Transaksi::create([
                'no_invoice'        => $invoice,
                'id_user'           => $user->id,
                'id_pelanggan'      => $pelanggan->id,
                'tanggal_order'     => $tgl,
                'subtotal'          => $subtotal,
                'potongan'          => $potongan,
                'total_harga'       => $total,
                'jumlah_bayar'      => $bayar,
                'sisa_bayar'        => $sisa,
                'status_pembayaran' => $status,
            ]);
            TransaksiItem::create([
                'transaksi_id' => $t->id,
                'layanan_id'   => $layanan->id,
                'unit_satuan'  => $unit,
                'qty'          => $qty,
                'harga_satuan' => $harga,
                'subtotal'     => $subtotal,
            ]);
            return $t;
        };

        $now       = Carbon::now();
        $bulanIni  = $now->format('Y-m');
        $bulanLalu = $now->copy()->subMonth()->format('Y-m');
        $duaBulan  = $now->copy()->subMonths(2)->format('Y-m');

        // === Bulan ini — Lunas ===
        $buat("INV-{$bulanIni}-001", $admin, $andi,  $cuciKomplit, "{$bulanIni}-02", 50000,  0,     50000, 'Lunas', 5,  'Kg',   10000);
        $buat("INV-{$bulanIni}-002", $kasir, $budi,  $setrika,     "{$bulanIni}-03", 30000,  0,     30000, 'Lunas', 15, 'Pcs',  2000);
        $buat("INV-{$bulanIni}-003", $admin, $citra, $cuciKilat,   "{$bulanIni}-05", 80000,  5000,  75000, 'Lunas', 8,  'Kg',   10000);
        $buat("INV-{$bulanIni}-004", $kasir, $dewi,  $cuciKomplit, "{$bulanIni}-07", 60000,  0,     60000, 'Lunas', 6,  'Kg',   10000);

        // === Bulan ini — DP / Belum Lunas (untuk TC-RPT-10 piutang) ===
        $buat("INV-{$bulanIni}-005", $admin, $andi,  $cuciKomplit, "{$bulanIni}-08", 100000, 10000, 50000, 'DP',    10, 'Kg',   10000);
        $buat("INV-{$bulanIni}-006", $kasir, $eko,   $setrika,     "{$bulanIni}-09", 40000,  0,     20000, 'DP',    20, 'Pcs',  2000);
        $buat("INV-{$bulanIni}-007", $admin, $citra, $cuciKilat,   "{$bulanIni}-10", 70000,  0,     30000, 'DP',    7,  'Kg',   10000);

        // === Bulan lalu — Lunas (untuk TC-RPT-09 rentang periode) ===
        $buat("INV-{$bulanLalu}-001", $admin, $budi,  $cuciKomplit, "{$bulanLalu}-05", 45000, 0,     45000, 'Lunas', 4,  'Kg',  10000);
        $buat("INV-{$bulanLalu}-002", $kasir, $dewi,  $setrika,     "{$bulanLalu}-10", 25000, 0,     25000, 'Lunas', 12, 'Pcs', 2000);
        $buat("INV-{$bulanLalu}-003", $admin, $eko,   $cuciKilat,   "{$bulanLalu}-15", 90000, 5000,  85000, 'Lunas', 9,  'Kg',  10000);

        // === Dua bulan lalu — mix (untuk rentang panjang) ===
        $buat("INV-{$duaBulan}-001", $admin, $andi,  $cuciKomplit, "{$duaBulan}-10", 55000, 0, 55000, 'Lunas', 5, 'Kg',  11000);
        $buat("INV-{$duaBulan}-002", $kasir, $citra, $setrika,     "{$duaBulan}-20", 35000, 0, 20000, 'DP',    17, 'Pcs', 2000);

        $this->command->info('✅ TestingSeeder selesai — ' . Transaksi::count() . ' transaksi dummy berhasil dibuat.');
    }
}
