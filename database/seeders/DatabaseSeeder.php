<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::factory()->admin()->create([
            'name'     => 'Admin Utama',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $kasirs = User::factory(3)->kasir()->create();

        // Layanan dengan unit satuan
        $layananData = [
            ['name' => 'Cuci Reguler',  'deskripsi' => 'Cuci pakaian biasa dengan waktu 2-3 hari'],
            ['name' => 'Cuci Express',  'deskripsi' => 'Cuci pakaian selesai dalam 1 hari'],
            ['name' => 'Cuci Setrika',  'deskripsi' => 'Paket cuci lengkap beserta setrika'],
            ['name' => 'Setrika Saja',  'deskripsi' => 'Hanya layanan setrika pakaian'],
            ['name' => 'Cuci Sepatu',   'deskripsi' => 'Cuci dan sikat sepatu hingga bersih'],
            ['name' => 'Cuci Tas',      'deskripsi' => 'Cuci dan perawatan berbagai jenis tas'],
            ['name' => 'Cuci Selimut',  'deskripsi' => 'Cuci selimut dan bed cover'],
        ];

        $unitsByLayanan = [
            'Cuci Reguler' => [['kg', 7000], ['pcs', 5000]],
            'Cuci Express' => [['kg', 12000], ['pcs', 9000]],
            'Cuci Setrika' => [['kg', 10000], ['pcs', 8000]],
            'Setrika Saja' => [['kg', 5000], ['pcs', 3000]],
            'Cuci Sepatu'  => [['pcs', 25000]],
            'Cuci Tas'     => [['pcs', 30000]],
            'Cuci Selimut' => [['pcs', 35000], ['kg', 15000]],
        ];

        $layanans = [];
        foreach ($layananData as $data) {
            $layanan = Layanan::create([
                'name'      => $data['name'],
                'deskripsi' => $data['deskripsi'],
                'gambar'    => ['default.jpg'],
            ]);

            foreach ($unitsByLayanan[$data['name']] as [$unit, $harga]) {
                LayananUnit::create([
                    'layanan_id'  => $layanan->id,
                    'unit_satuan' => $unit,
                    'harga'       => $harga,
                ]);
            }

            $layanans[] = $layanan;
        }

        // Pelanggan
        $pelanggans = Pelanggan::factory(30)->create();

        // Transaksi
        $statuses = ['Lunas', 'Lunas', 'Lunas', 'DP', 'Belum Bayar'];
        $counter  = 1;

        for ($i = 0; $i < 50; $i++) {
            $tanggal   = now()->subDays(rand(0, 60));
            $kasir     = $kasirs->random();
            $pelanggan = $pelanggans->random();
            $status    = $statuses[array_rand($statuses)];

            $noInvoice = 'IJ' . $tanggal->format('dmY') . str_pad($counter++, 4, '0', STR_PAD_LEFT);

            $transaksi = Transaksi::create([
                'no_invoice'        => $noInvoice,
                'id_user'           => $kasir->id,
                'created_by'        => 'admin',
                'id_pelanggan'      => $pelanggan->id,
                'id_layanan'        => null,
                'tanggal_order'     => $tanggal,
                'deskripsi'         => null,
                'subtotal'          => 0,
                'potongan'          => 0,
                'total_harga'       => 0,
                'jumlah_bayar'      => 0,
                'sisa_bayar'        => 0,
                'status_pembayaran' => $status,
            ]);

            // Items transaksi (1-3 item per transaksi)
            $itemCount = rand(1, 3);
            $subtotal  = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $layanan = $layanans[array_rand($layanans)];
                $units   = LayananUnit::where('layanan_id', $layanan->id)->get();
                if ($units->isEmpty()) continue;

                $unit         = $units->random();
                $qty          = rand(1, 5);
                $harga        = $unit->harga;
                $itemSubtotal = $qty * $harga;
                $subtotal    += $itemSubtotal;

                TransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'layanan_id'   => $layanan->id,
                    'unit_satuan'  => $unit->unit_satuan,
                    'qty'          => $qty,
                    'harga_satuan' => $harga,
                    'subtotal'     => $itemSubtotal,
                ]);
            }

            $total = $subtotal;
            $bayar = match ($status) {
                'DP'          => (int) ceil($total * 0.5),
                'Belum Bayar' => 0,
                default       => $total,
            };
            $sisa = $total - $bayar;

            $transaksi->update([
                'subtotal'     => $subtotal,
                'total_harga'  => $total,
                'jumlah_bayar' => $bayar,
                'sisa_bayar'   => $sisa,
            ]);
        }
    }
}
