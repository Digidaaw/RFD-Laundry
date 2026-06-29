<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


/**
 * TC-RPT-01: Default Tanggal Otomatis Saat Tanggal Tidak Diisi
 *
 * Behavior: Jika tanggal kosong atau tidak diisi, sistem otomatis
 * menampilkan laporan dengan default awal & akhir bulan berjalan.
 */
class TC_RPT01_DefaultTanggalOtomatisTest extends TestCase
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
    public function sistem_default_bulan_ini_jika_start_date_dikosongkan()
    {
        // start_date kosong → salah satu kosong → auto-default ke bulan berjalan
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '',
            'end_date'   => '2025-01-31',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $response->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function sistem_default_bulan_ini_jika_end_date_dikosongkan()
    {
        // end_date kosong → salah satu kosong → auto-default ke bulan berjalan
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $response->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function sistem_default_bulan_ini_jika_kedua_tanggal_dikosongkan()
    {
        // keduanya kosong → auto-default ke awal & akhir bulan berjalan
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '',
            'end_date'   => '',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $response->assertSessionDoesntHaveErrors();
        $response->assertViewHas('startDate', fn($d) => $d->isSameDay(Carbon::now()->startOfMonth()));
        $response->assertViewHas('endDate', fn($d) => $d->isSameDay(Carbon::now()->endOfMonth()));
    }

    /** @test */
    public function sistem_menampilkan_laporan_jika_kedua_tanggal_diisi_dengan_benar()
    {
        // Kedua tanggal valid → laporan berhasil ditampilkan (tidak default)
        $this->buatTransaksi(['tanggal_order' => '2025-01-15']);

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-01-31',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $response->assertSessionDoesntHaveErrors();
        $response->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }
}
