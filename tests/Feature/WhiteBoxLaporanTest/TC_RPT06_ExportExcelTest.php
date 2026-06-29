<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TC_RPT06b_ExportExcelTest extends TestCase
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
            'tanggal_order'     => '2025-01-10',
            'subtotal'          => 100000,
            'potongan'          => 0,
            'total_harga'       => 100000,
            'jumlah_bayar'      => 100000,
            'sisa_bayar'        => 0,
            'status_pembayaran' => 'Lunas',
        ], $override));
    }

    /** @test */
    public function export_excel_dengan_data_menghasilkan_file_unduhan()
    {
        // TC-RPT-06b: export=excel ada data → download file
        $this->buatTransaksi();

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-01-31',
            'export'     => 'excel',
        ]));

        $response->assertStatus(200);
        $response->assertDownload();
    }

    /** @test */
    public function export_excel_tanpa_data_tetap_menghasilkan_file_unduhan()
    {
        // export=excel tanpa transaksi → file kosong tetap dihasilkan
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-31',
            'export'     => 'excel',
        ]));

        $response->assertStatus(200);
        $response->assertDownload();
    }
}
