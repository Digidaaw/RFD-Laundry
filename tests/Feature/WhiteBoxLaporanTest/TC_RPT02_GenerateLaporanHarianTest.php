<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


/*
transaksi_tepat_pada_start_date_termasuk       → boundary
transaksi_tepat_pada_end_date_termasuk         → boundary  
transaksi_sehari_sebelum_start_date_tidak...   → boundary invalid
transaksi_sehari_setelah_end_date_tidak...     → boundary invalid
laporan_rentang_satu_hari_hanya_menampilkan... → boundary
*/

class TC_RPT02_GenerateLaporanHarianTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Pelanggan $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Administrator',
            'role'     => 'admin',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $this->pelanggan = Pelanggan::create([
            'name'   => 'Andi Susanto',
            'kontak' => '081234567890',
        ]);

        Layanan::create([
            'name'      => 'Cuci Reguler',
            'gambar'    => [],
            'deskripsi' => 'Layanan cuci biasa',
        ]);
    }

    private function buatTransaksi(array $override = []): Transaksi
    {
        return Transaksi::create(array_merge([
            'no_invoice'        => 'INV-' . uniqid(),
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $this->pelanggan->id,
            'tanggal_order'     => Carbon::today()->toDateString(),
            'subtotal'          => 100000,
            'potongan'          => 0,
            'total_harga'       => 100000,
            'jumlah_bayar'      => 100000,
            'sisa_bayar'        => 0,
            'status_pembayaran' => 'Lunas',
        ], $override));
    }

    /** @test */
    public function transaksi_tepat_pada_start_date_termasuk_dalam_laporan()
    {
        $this->buatTransaksi(['tanggal_order' => '2025-06-01']);

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-30',
        ]));

        $response->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }

    /** @test */
    public function transaksi_tepat_pada_end_date_termasuk_dalam_laporan()
    {
        $this->buatTransaksi(['tanggal_order' => '2025-06-30']);

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-30',
        ]));

        $response->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }

    /** @test */
    public function transaksi_sehari_sebelum_start_date_tidak_termasuk_dalam_laporan()
    {
        $this->buatTransaksi(['tanggal_order' => '2025-05-31']); // satu hari sebelum

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-30',
        ]));

        $response->assertViewHas('transaksis', fn($t) => $t->count() === 0);
    }

    /** @test */
    public function transaksi_sehari_setelah_end_date_tidak_termasuk_dalam_laporan()
    {
        $this->buatTransaksi(['tanggal_order' => '2025-07-01']); // satu hari setelah

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-06-01',
            'end_date'   => '2025-06-30',
        ]));

        $response->assertViewHas('transaksis', fn($t) => $t->count() === 0);
    }

    /** @test */
    public function laporan_periode_rentang_satu_hari_hanya_menampilkan_transaksi_hari_itu()
    {
        $this->buatTransaksi(['tanggal_order' => '2025-07-15']);
        $this->buatTransaksi(['tanggal_order' => '2025-07-14']); // di luar
        $this->buatTransaksi(['tanggal_order' => '2025-07-16']); // di luar

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-07-15',
            'end_date'   => '2025-07-15',
        ]));

        $response->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }
}
