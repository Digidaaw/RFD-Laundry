<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pelanggan $pelanggan;
    private Layanan $cuci;
    private Layanan $setrika;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Admin Transaksi',
            'role' => 'admin',
            'username' => 'admin_trx_' . uniqid(),
            'password' => 'password',
        ]);

        $this->pelanggan = Pelanggan::create([
            'name' => 'Budi Laundry',
            'kontak' => '0812' . random_int(10000000, 99999999),
        ]);

        $this->cuci = $this->createLayanan('Cuci Kering', [
            'kg' => 10000,
            'pcs' => 5000,
        ]);

        $this->setrika = $this->createLayanan('Setrika', [
            'kg' => 8000,
            'pcs' => 3000,
        ]);

        $this->actingAs($this->user);
    }

    /**
     * SCENARIO: VALID & LUNAS (FULL PAYMENT)
     */
    public function test_transaksi_valid_dan_lunas(): void
    {
        // 2 kg Cuci Kering (kg = 10000) -> subtotal = 20000
        // potongan = 2000 -> total_harga = 18000
        // jumlah_bayar = 18000 (lunas)
        $response = $this->post(route('transaksi.store'), $this->payload([
            'items' => [
                $this->item($this->cuci, 'kg', 2),
            ],
            'potongan' => 2000,
            'jumlah_bayar' => 18000,
        ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transaksis', [
            'id_user' => $this->user->id,
            'id_pelanggan' => $this->pelanggan->id,
            'subtotal' => 20000,
            'potongan' => 2000,
            'total_harga' => 18000,
            'jumlah_bayar' => 18000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    /**
     * SCENARIO: VALID & DP (DOWN PAYMENT)
     */
    public function test_transaksi_valid_dan_dp(): void
    {
        // 2 kg Cuci Kering (kg = 10000) -> subtotal = 20000
        // potongan = 0 -> total_harga = 20000
        // jumlah_bayar = 12000 (DP, minimal 50% dari 20000 adalah 10000)
        $response = $this->post(route('transaksi.store'), $this->payload([
            'items' => [
                $this->item($this->cuci, 'kg', 2),
            ],
            'potongan' => 0,
            'jumlah_bayar' => 12000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transaksis', [
            'id_user' => $this->user->id,
            'id_pelanggan' => $this->pelanggan->id,
            'subtotal' => 20000,
            'potongan' => 0,
            'total_harga' => 20000,
            'jumlah_bayar' => 12000,
            'sisa_bayar' => 8000,
            'status_pembayaran' => 'DP',
        ]);
    }

    /**
     * SCENARIO: INVALID TRANSACTION VALIDATIONS
     */
    public function test_transaksi_invalid_karena_pelanggan_kosong(): void
    {
        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'id_pelanggan' => '',
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('id_pelanggan');
    }

    public function test_transaksi_invalid_karena_pembayaran_kurang_dari_50_persen(): void
    {
        // subtotal = 20000, total_harga = 20000, minimal bayar = 10000
        // bayar 9000 (tidak valid karena < 50%)
        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'items' => [
                    $this->item($this->cuci, 'kg', 2),
                ],
                'potongan' => 0,
                'jumlah_bayar' => 9000,
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('jumlah_bayar');
    }

    public function test_transaksi_invalid_karena_potongan_melebihi_subtotal(): void
    {
        // subtotal = 20000, potongan = 25000 (tidak valid)
        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'items' => [
                    $this->item($this->cuci, 'kg', 2),
                ],
                'potongan' => 25000,
                'jumlah_bayar' => 10000,
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('potongan');
    }

    public function test_transaksi_invalid_karena_format_jumlah_bayar_bukan_angka(): void
    {
        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'jumlah_bayar' => 'lima puluh ribu',
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('jumlah_bayar');
    }

    /**
     * HELPER METHODS
     */
    private function payload(array $override = []): array
    {
        return array_replace_recursive([
            'id_pelanggan' => $this->pelanggan->id,
            'deskripsi' => 'Laundry reguler',
            'items' => [
                $this->item($this->cuci, 'kg', 2),
            ],
            'potongan' => 0,
            'jumlah_bayar' => 20000,
        ], $override);
    }

    private function item(Layanan $layanan, string $unit, float|int|string|null $qty): array
    {
        return [
            'id_layanan' => $layanan->id,
            'unit_satuan' => $unit,
            'qty' => $qty,
        ];
    }

    private function createLayanan(string $name, array $units): Layanan
    {
        $layanan = Layanan::create([
            'name' => $name,
            'gambar' => [],
            'deskripsi' => 'Layanan ' . $name,
        ]);

        foreach ($units as $unit => $harga) {
            LayananUnit::create([
                'layanan_id' => $layanan->id,
                'unit_satuan' => $unit,
                'harga' => $harga,
            ]);
        }

        return $layanan;
    }
}
