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
 * White Box Test – Kondisi WHERE pada buildPiutangQuery()
 *
 * Kondisi yang diuji:
 *   WHERE status_pembayaran = 'DP' AND sisa_bayar > 0
 *
 * 4 kombinasi kondisi (truth table):
 *   status=DP,    sisa>0  → TRUE  AND TRUE  → masuk
 *   status=DP,    sisa=0  → TRUE  AND FALSE → tidak masuk
 *   status=Lunas, sisa>0  → FALSE AND TRUE  → tidak masuk
 *   status=Lunas, sisa=0  → FALSE AND FALSE → tidak masuk
 *
 * Juga menguji batas tepat sisa_bayar = 1 (nilai terkecil > 0)
 */
class TC_RPT07_LaporanTransaksiTest extends TestCase
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
    public function status_dp_sisa_lebih_dari_nol_masuk_piutang()
    {
        // status=DP AND sisa_bayar>0 → kedua kondisi TRUE → masuk
        $this->buatTransaksi([
            'status_pembayaran' => 'DP',
            'jumlah_bayar'      => 50000,
            'sisa_bayar'        => 50000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 1);
    }

    /** @test */
    public function status_dp_sisa_bayar_tepat_nol_tidak_masuk_piutang()
    {
        // status=DP AND sisa_bayar=0 → sisa_bayar > 0 = FALSE → tidak masuk
        $this->buatTransaksi([
            'status_pembayaran' => 'DP',
            'jumlah_bayar'      => 100000,
            'sisa_bayar'        => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 0);
    }

    /** @test */
    public function status_lunas_sisa_bayar_nol_tidak_masuk_piutang()
    {
        // status=Lunas AND sisa_bayar=0 → status ≠ 'DP' → tidak masuk
        $this->buatTransaksi([
            'status_pembayaran' => 'Lunas',
            'jumlah_bayar'      => 100000,
            'sisa_bayar'        => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 0);
    }

    /** @test */
    public function status_lunas_sisa_bayar_lebih_dari_nol_tidak_masuk_piutang()
    {
        // status=Lunas AND sisa_bayar>0 → status ≠ 'DP' → tidak masuk
        $this->buatTransaksi([
            'status_pembayaran' => 'Lunas',
            'jumlah_bayar'      => 50000,
            'sisa_bayar'        => 50000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 0);
    }

    /** @test */
    public function status_dp_sisa_bayar_tepat_satu_adalah_batas_minimum_yang_masuk()
    {
        // sisa_bayar = 1 → nilai terkecil yang memenuhi sisa_bayar > 0
        $this->buatTransaksi([
            'status_pembayaran' => 'DP',
            'jumlah_bayar'      => 99999,
            'sisa_bayar'        => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 1);
    }
}
