<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // 1. USERS
        // ─────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Administrator',
            'role'     => 'admin',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $kasir = User::create([
            'name'     => 'Siti Kasir',
            'role'     => 'kasir',
            'username' => 'kasir',
            'password' => 'kasir123',
        ]);

        // ─────────────────────────────────────────────────────────────
        // 2. LAYANAN + UNIT HARGA
        // ─────────────────────────────────────────────────────────────
        $cuciSetrika = Layanan::create([
            'name'      => 'Cuci + Setrika',
            'deskripsi' => 'Layanan cuci dan setrika reguler, selesai 2-3 hari',
            'gambar'    => [],
        ]);
        LayananUnit::create(['layanan_id' => $cuciSetrika->id, 'unit_satuan' => 'kg',  'harga' => 7000]);
        LayananUnit::create(['layanan_id' => $cuciSetrika->id, 'unit_satuan' => 'pcs', 'harga' => 3000]);

        $cuciSaja = Layanan::create([
            'name'      => 'Cuci Saja',
            'deskripsi' => 'Layanan cuci tanpa setrika, selesai 1-2 hari',
            'gambar'    => [],
        ]);
        LayananUnit::create(['layanan_id' => $cuciSaja->id, 'unit_satuan' => 'kg',  'harga' => 5000]);
        LayananUnit::create(['layanan_id' => $cuciSaja->id, 'unit_satuan' => 'pcs', 'harga' => 2000]);

        $cuciKilat = Layanan::create([
            'name'      => 'Cuci Kilat',
            'deskripsi' => 'Layanan express, selesai dalam 6 jam',
            'gambar'    => [],
        ]);
        LayananUnit::create(['layanan_id' => $cuciKilat->id, 'unit_satuan' => 'kg',  'harga' => 12000]);

        $setrikaSaja = Layanan::create([
            'name'      => 'Setrika Saja',
            'deskripsi' => 'Layanan setrika tanpa cuci',
            'gambar'    => [],
        ]);
        LayananUnit::create(['layanan_id' => $setrikaSaja->id, 'unit_satuan' => 'kg',  'harga' => 4000]);
        LayananUnit::create(['layanan_id' => $setrikaSaja->id, 'unit_satuan' => 'pcs', 'harga' => 1500]);

        $cuciSepatu = Layanan::create([
            'name'      => 'Cuci Sepatu',
            'deskripsi' => 'Layanan cuci sepatu dan sandal',
            'gambar'    => [],
        ]);
        LayananUnit::create(['layanan_id' => $cuciSepatu->id, 'unit_satuan' => 'pcs', 'harga' => 25000]);

        // ─────────────────────────────────────────────────────────────
        // 3. PELANGGAN
        // ─────────────────────────────────────────────────────────────
        $p = [];
        $dataPelanggan = [
            ['name' => 'Andi Susanto',   'kontak' => '081234567890'],
            ['name' => 'Budi Setiawan',  'kontak' => '089876543210'],
            ['name' => 'Citra Dewi',     'kontak' => '085555555555'],
            ['name' => 'Dewi Lestari',   'kontak' => '082233445566'],
            ['name' => 'Eko Prasetyo',   'kontak' => '087711223344'],
            ['name' => 'Fina Rahayu',    'kontak' => '081399887766'],
            ['name' => 'Gilang Ramadan', 'kontak' => '089900112233'],
            ['name' => 'Hana Pertiwi',   'kontak' => '082244556677'],
            ['name' => 'Irfan Maulana',  'kontak' => '085600001111'],
            ['name' => 'Joko Santoso',   'kontak' => '087722334455'],
        ];
        foreach ($dataPelanggan as $d) {
            $p[] = Pelanggan::create($d);
        }

        // ─────────────────────────────────────────────────────────────
        // 4. TRANSAKSI (3 bulan terakhir)
        // Helper: buat transaksi + item sekaligus
        // ─────────────────────────────────────────────────────────────
        $buat = function (
            string $invoice, User $user, Pelanggan $pelanggan, Layanan $layanan,
            string $tgl, float $subtotal, float $potongan, float $bayar, string $status,
            float $qty, string $unit, float $hargaSatuan
        ) {
            $total = $subtotal - $potongan;
            $sisa  = max(0, $total - $bayar);

            $transaksi = Transaksi::create([
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
                'transaksi_id' => $transaksi->id,
                'layanan_id'   => $layanan->id,
                'unit_satuan'  => $unit,
                'qty'          => $qty,
                'harga_satuan' => $hargaSatuan,
                'subtotal'     => $subtotal,
            ]);

            return $transaksi;
        };

        $bln0 = Carbon::now()->format('Y-m');          // Bulan ini
        $bln1 = Carbon::now()->subMonth()->format('Y-m');    // Bulan lalu
        $bln2 = Carbon::now()->subMonths(2)->format('Y-m');  // 2 bulan lalu

        // ── BULAN INI ─────────────────────────────────────────────────
        $buat("INV-{$bln0}-001", $admin, $p[0], $cuciSetrika, "{$bln0}-01", 70000,  0,     70000, 'Lunas', 10, 'kg',  7000);
        $buat("INV-{$bln0}-002", $kasir, $p[1], $cuciSaja,    "{$bln0}-02", 25000,  0,     25000, 'Lunas',  5, 'kg',  5000);
        $buat("INV-{$bln0}-003", $admin, $p[2], $cuciKilat,   "{$bln0}-03", 96000,  6000,  90000, 'Lunas',  8, 'kg', 12000);
        $buat("INV-{$bln0}-004", $kasir, $p[3], $setrikaSaja, "{$bln0}-04", 30000,  0,     30000, 'Lunas',  7, 'kg',  4000);
        $buat("INV-{$bln0}-005", $admin, $p[4], $cuciSepatu,  "{$bln0}-05", 75000,  0,     75000, 'Lunas',  3, 'pcs',25000);
        $buat("INV-{$bln0}-006", $kasir, $p[5], $cuciSetrika, "{$bln0}-06", 84000,  0,     84000, 'Lunas', 12, 'kg',  7000);
        $buat("INV-{$bln0}-007", $admin, $p[6], $cuciSaja,    "{$bln0}-07", 40000,  5000,  35000, 'Lunas',  8, 'kg',  5000);

        // Bulan ini — Belum Lunas (DP / Piutang)
        $buat("INV-{$bln0}-008", $admin, $p[0], $cuciSetrika, "{$bln0}-10", 105000, 5000,  50000, 'DP',    15, 'kg',  7000);
        $buat("INV-{$bln0}-009", $kasir, $p[7], $cuciKilat,   "{$bln0}-11",  60000, 0,     30000, 'DP',     5, 'kg', 12000);
        $buat("INV-{$bln0}-010", $admin, $p[2], $setrikaSaja, "{$bln0}-12",  36000, 0,     20000, 'DP',     9, 'kg',  4000);
        $buat("INV-{$bln0}-011", $kasir, $p[8], $cuciSepatu,  "{$bln0}-13",  50000, 0,     25000, 'DP',     2, 'pcs',25000);

        // ── BULAN LALU ────────────────────────────────────────────────
        $buat("INV-{$bln1}-001", $admin, $p[1], $cuciSetrika, "{$bln1}-03",  56000,  0,     56000, 'Lunas',  8, 'kg',  7000);
        $buat("INV-{$bln1}-002", $kasir, $p[3], $cuciKilat,   "{$bln1}-05", 120000,  0,    120000, 'Lunas', 10, 'kg', 12000);
        $buat("INV-{$bln1}-003", $admin, $p[4], $cuciSaja,    "{$bln1}-08",  35000,  5000,  30000, 'Lunas',  7, 'kg',  5000);
        $buat("INV-{$bln1}-004", $kasir, $p[5], $setrikaSaja, "{$bln1}-10",  22500,  0,     22500, 'Lunas', 15, 'pcs', 1500);
        $buat("INV-{$bln1}-005", $admin, $p[9], $cuciSepatu,  "{$bln1}-12",  25000,  0,     25000, 'Lunas',  1, 'pcs',25000);
        $buat("INV-{$bln1}-006", $kasir, $p[6], $cuciSetrika, "{$bln1}-15",  91000,  0,     91000, 'Lunas', 13, 'kg',  7000);
        $buat("INV-{$bln1}-007", $admin, $p[7], $cuciKilat,   "{$bln1}-18",  72000,  0,     40000, 'DP',     6, 'kg', 12000);
        $buat("INV-{$bln1}-008", $kasir, $p[0], $cuciSaja,    "{$bln1}-20",  50000,  10000, 20000, 'DP',    10, 'kg',  5000);

        // ── 2 BULAN LALU ──────────────────────────────────────────────
        $buat("INV-{$bln2}-001", $admin, $p[2], $cuciSetrika, "{$bln2}-02",  63000,  0,     63000, 'Lunas',  9, 'kg',  7000);
        $buat("INV-{$bln2}-002", $kasir, $p[4], $cuciSaja,    "{$bln2}-05",  20000,  0,     20000, 'Lunas',  4, 'kg',  5000);
        $buat("INV-{$bln2}-003", $admin, $p[6], $cuciKilat,   "{$bln2}-08",  84000,  0,     84000, 'Lunas',  7, 'kg', 12000);
        $buat("INV-{$bln2}-004", $kasir, $p[1], $setrikaSaja, "{$bln2}-10",  18000,  0,     18000, 'Lunas', 12, 'pcs', 1500);
        $buat("INV-{$bln2}-005", $admin, $p[3], $cuciSepatu,  "{$bln2}-12", 100000,  0,    100000, 'Lunas',  4, 'pcs',25000);
        $buat("INV-{$bln2}-006", $kasir, $p[8], $cuciSetrika, "{$bln2}-15",  49000,  0,     49000, 'Lunas',  7, 'kg',  7000);
        $buat("INV-{$bln2}-007", $admin, $p[9], $cuciKilat,   "{$bln2}-18",  96000,  6000,  50000, 'DP',     8, 'kg', 12000);
        $buat("INV-{$bln2}-008", $kasir, $p[5], $cuciSaja,    "{$bln2}-22",  30000,  0,     15000, 'DP',     6, 'kg',  5000);

        $this->command->info('✅ Seeder selesai!');
        $this->command->info('   Users      : ' . User::count());
        $this->command->info('   Layanan    : ' . Layanan::count());
        $this->command->info('   Pelanggan  : ' . Pelanggan::count());
        $this->command->info('   Transaksi  : ' . Transaksi::count() . ' (Lunas + DP)');
    }
}
