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
 * White Box Test – Branch `if ($search)` pada buildPiutangQuery()
 *
 * Branch yang diuji (method private, diuji via laporanPiutang):
 *   - $search = null   → if ($search) = false → tidak ada filter whereHas
 *   - $search = ''     → if ($search) = false → string kosong falsy di PHP
 *   - $search = 'nama' → if ($search) = true  → whereHas dijalankan
 *
 * Kondisi OR dalam whereHas:
 *   - Cocok via name LIKE %search%
 *   - Cocok via kontak LIKE %search%
 */
class TC_RPT08_LaporanPiutangTest extends TestCase
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

    private function buatTransaksiDp(Pelanggan $pelanggan, int $sisa = 50000): Transaksi
    {
        return Transaksi::create([
            'no_invoice'        => 'INV-' . uniqid(),
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $pelanggan->id,
            'tanggal_order'     => Carbon::today()->toDateString(),
            'subtotal'          => 100000,
            'potongan'          => 0,
            'total_harga'       => 100000,
            'jumlah_bayar'      => 100000 - $sisa,
            'sisa_bayar'        => $sisa,
            'status_pembayaran' => 'DP',
        ]);
    }

    /** @test */
    public function tanpa_parameter_search_cabang_if_search_false_semua_piutang_tampil()
    {
        // $search = null → if ($search) = false → tidak ada whereHas
        $pelangganLain = Pelanggan::create(['name' => 'Budi Santoso', 'kontak' => '082200000000']);

        $this->buatTransaksiDp($this->pelanggan);
        $this->buatTransaksiDp($pelangganLain);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 2);
    }

    /** @test */
    public function search_string_kosong_dianggap_falsy_semua_piutang_tampil()
    {
        // '' (string kosong) → if ($search) = false → tidak ada filter
        $pelangganLain = Pelanggan::create(['name' => 'Citra Dewi', 'kontak' => '083300000000']);

        $this->buatTransaksiDp($this->pelanggan);
        $this->buatTransaksiDp($pelangganLain);

        $response = $this->actingAs($this->admin)->get(route('report.piutang', ['search' => '']));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 2);
    }

    /** @test */
    public function search_dengan_nama_pelanggan_mengaktifkan_filter_wherehas()
    {
        // 'Andi' → if ($search) = true → whereHas name LIKE %Andi%
        $pelangganLain = Pelanggan::create(['name' => 'Zulkifli', 'kontak' => '084400000000']);

        $this->buatTransaksiDp($this->pelanggan);
        $this->buatTransaksiDp($pelangganLain);

        $response = $this->actingAs($this->admin)->get(route('report.piutang', ['search' => 'Andi']));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 1);
    }

    /** @test */
    public function search_dengan_kontak_pelanggan_mengaktifkan_filter_wherehas()
    {
        // Kondisi OR: kontak LIKE %081234567890%
        $this->buatTransaksiDp($this->pelanggan);

        $response = $this->actingAs($this->admin)->get(route('report.piutang', ['search' => '081234567890']));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 1);
    }

    /** @test */
    public function search_mencocokkan_kondisi_or_nama_dan_kontak_sekaligus()
    {
        // Pelanggan 1: nama mengandung 'Andi' (cocok via name)
        // Pelanggan 2: kontaknya dimulai '0812' (cocok via kontak)
        $pelangganKontak = Pelanggan::create(['name' => 'Rudi', 'kontak' => '08123XXXXX']);

        $this->buatTransaksiDp($this->pelanggan);
        $this->buatTransaksiDp($pelangganKontak);

        // '0812' cocok untuk kontak keduanya (081234567890 dan 08123XXXXX)
        $response = $this->actingAs($this->admin)->get(route('report.piutang', ['search' => '0812']));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 2);
    }

    /** @test */
    public function search_yang_tidak_cocok_mengembalikan_koleksi_kosong()
    {
        $this->buatTransaksiDp($this->pelanggan);

        $response = $this->actingAs($this->admin)->get(route('report.piutang', ['search' => 'tidakada']));

        $response->assertViewHas('piutangs', fn($p) => $p->count() === 0);
    }
}
