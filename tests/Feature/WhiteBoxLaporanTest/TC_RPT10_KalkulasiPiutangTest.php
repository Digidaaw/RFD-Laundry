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
 * White Box Test – Kalkulasi Agregat pada laporanPiutang()
 *
 * Logika yang diuji:
 *   $totalPiutang = $piutangs->sum('sisa_bayar')
 *
 * Juga memverifikasi variabel yang dikirim ke view:
 *   'piutangs', 'totalPiutang', 'pelanggans', 'layanans'
 */
class TC_RPT10_KalkulasiPiutangTest extends TestCase
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
    public function total_piutang_adalah_penjumlahan_sisa_bayar_semua_transaksi_dp()
    {
        $this->buatTransaksi(['status_pembayaran' => 'DP', 'sisa_bayar' => 40000, 'jumlah_bayar' => 60000]);
        $this->buatTransaksi(['status_pembayaran' => 'DP', 'sisa_bayar' => 25000, 'jumlah_bayar' => 75000]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('totalPiutang', 65000);
    }

    /** @test */
    public function total_piutang_nol_ketika_tidak_ada_transaksi_piutang()
    {
        // Koleksi kosong → sum() = 0
        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('totalPiutang', 0);
    }

    /** @test */
    public function total_piutang_tidak_menghitung_sisa_bayar_dari_transaksi_lunas()
    {
        // Lunas tidak masuk filter → tidak ikut dihitung dalam sum
        $this->buatTransaksi(['status_pembayaran' => 'DP',    'sisa_bayar' => 30000, 'jumlah_bayar' => 70000]);
        $this->buatTransaksi(['status_pembayaran' => 'Lunas', 'sisa_bayar' => 0,     'jumlah_bayar' => 100000]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        // Hanya 30000 yang dihitung, bukan 30000 + 0
        $response->assertViewHas('totalPiutang', 30000);
    }

    /** @test */
    public function laporan_piutang_mengirimkan_variabel_pelanggans_dan_layanans_ke_view()
    {
        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs');
        $response->assertViewHas('totalPiutang');
        $response->assertViewHas('pelanggans');
        $response->assertViewHas('layanans');
    }
}
