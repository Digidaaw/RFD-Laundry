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
 * White Box Test – Konsistensi Data Lintas Laporan
 *
 * Memastikan bahwa satu transaksi yang sama tampil dengan konsisten
 * di laporan yang berbeda (periode, piutang, pelanggan), sesuai
 * dengan logika filter masing-masing laporan.
 */
class TC_RPT11_KonsistensiDataTest extends TestCase
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
    public function transaksi_lunas_muncul_di_laporan_periode_dan_laporan_pelanggan()
    {
        $this->buatTransaksi([
            'tanggal_order'     => '2025-04-10',
            'total_harga'       => 120000,
            'status_pembayaran' => 'Lunas',
        ]);

        $responsePeriode = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-04-01',
            'end_date'   => '2025-04-30',
        ]));
        $responsePelanggan = $this->actingAs($this->admin)->get(route('report.pelanggan', $this->pelanggan));

        $responsePeriode->assertViewHas('transaksis', fn($t) => $t->count() === 1);
        $responsePelanggan->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }

    /** @test */
    public function transaksi_dp_muncul_di_laporan_periode_piutang_dan_pelanggan()
    {
        $this->buatTransaksi([
            'tanggal_order'     => '2025-04-05',
            'status_pembayaran' => 'DP',
            'jumlah_bayar'      => 30000,
            'sisa_bayar'        => 70000,
        ]);

        // Harus muncul di laporan piutang
        $responsePiutang = $this->actingAs($this->admin)->get(route('report.piutang'));
        $responsePiutang->assertViewHas('piutangs', fn($p) => $p->count() === 1);

        // Harus muncul di laporan periode (tidak difilter berdasarkan status)
        $responsePeriode = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-04-01',
            'end_date'   => '2025-04-30',
        ]));
        $responsePeriode->assertViewHas('transaksis', fn($t) => $t->count() === 1);

        // Harus muncul di laporan pelanggan
        $responsePelanggan = $this->actingAs($this->admin)->get(route('report.pelanggan', $this->pelanggan));
        $responsePelanggan->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }

    /** @test */
    public function transaksi_lunas_tidak_muncul_di_laporan_piutang()
    {
        $this->buatTransaksi([
            'tanggal_order'     => '2025-04-05',
            'status_pembayaran' => 'Lunas',
            'jumlah_bayar'      => 100000,
            'sisa_bayar'        => 0,
        ]);

        // Tidak boleh muncul di laporan piutang
        $responsePiutang = $this->actingAs($this->admin)->get(route('report.piutang'));
        $responsePiutang->assertViewHas('piutangs', fn($p) => $p->count() === 0);

        // Tapi tetap muncul di laporan periode
        $responsePeriode = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-04-01',
            'end_date'   => '2025-04-30',
        ]));
        $responsePeriode->assertViewHas('transaksis', fn($t) => $t->count() === 1);
    }

    /** @test */
    public function laporan_periode_menghitung_potensi_pendapatan_dari_semua_status_transaksi()
    {
        // Potensi pendapatan = sum total_harga, tidak peduli status pembayaran
        $this->buatTransaksi(['tanggal_order' => '2025-05-10', 'total_harga' => 80000, 'jumlah_bayar' => 80000, 'status_pembayaran' => 'Lunas']);
        $this->buatTransaksi(['tanggal_order' => '2025-05-15', 'total_harga' => 120000, 'status_pembayaran' => 'DP', 'sisa_bayar' => 60000, 'jumlah_bayar' => 60000]);

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-05-01',
            'end_date'   => '2025-05-31',
        ]));

        $response->assertViewHas('potensiPendapatan', 200000);
        $response->assertViewHas('pendapatanLunas',   140000); // 80000 + 60000 jumlah_bayar
        $response->assertViewHas('totalTransaksi',    2);
    }
}
