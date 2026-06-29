<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TC_RPT01_GenerateTanpaTanggalTest extends TestCase
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
    public function sistem_menolak_jika_start_date_dikosongkan()
    {
        // User menghapus start_date → form mengirim string kosong → required gagal
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '',
            'end_date'   => '2025-01-31',
        ]));

        $response->assertSessionHasErrors('start_date');
    }

    /** @test */
    public function sistem_menolak_jika_end_date_dikosongkan()
    {
        // User menghapus end_date → form mengirim string kosong → required gagal
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '',
        ]));

        $response->assertSessionHasErrors('end_date');
    }

    /** @test */
    public function sistem_menolak_jika_kedua_tanggal_dikosongkan()
    {
        // User mengosongkan keduanya → dua error sekaligus
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '',
            'end_date'   => '',
        ]));

        $response->assertSessionHasErrors(['start_date', 'end_date']);
    }

    /** @test */
    public function sistem_menampilkan_laporan_jika_kedua_tanggal_diisi_dengan_benar()
    {
        // Kedua tanggal valid → laporan berhasil ditampilkan
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
