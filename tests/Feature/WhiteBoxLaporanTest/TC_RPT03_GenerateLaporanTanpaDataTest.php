<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TC_RPT03_GenerateLaporanTanpaDataTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Administrator',
            'role'     => 'admin',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        Pelanggan::create(['name' => 'Andi Susanto', 'kontak' => '081234567890']);
        Layanan::create(['name' => 'Cuci Reguler', 'gambar' => [], 'deskripsi' => 'Layanan cuci biasa']);
    }

    /** @test */
    public function laporan_periode_tanpa_transaksi_tetap_menampilkan_halaman()
    {
        // TC-RPT-03: periode valid, tidak ada transaksi → halaman 200, data kosong
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2000-01-01',
            'end_date'   => '2000-01-31',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $response->assertViewHas('transaksis', function ($koleksi) {
            return $koleksi->isEmpty();
        });
    }

    /** @test */
    public function laporan_piutang_tanpa_data_piutang_tetap_menampilkan_halaman()
    {
        // piutang: semua transaksi Lunas → koleksi piutang kosong
        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.piutang');
    }
}
