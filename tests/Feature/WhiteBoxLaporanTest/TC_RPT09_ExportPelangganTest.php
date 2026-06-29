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
 * White Box Test – Export PDF dan Excel per Pelanggan
 *
 * Path eksekusi yang diuji:
 *   exportPdfPelanggan()  → selalu return PDF download
 *   exportPelangganXls()  → selalu return Excel download
 *
 * Edge case:
 *   - Pelanggan dengan transaksi   → file tergenerate berisi data
 *   - Pelanggan tanpa transaksi    → file tetap tergenerate (koleksi kosong)
 */
class TC_RPT09_ExportPelangganTest extends TestCase
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
    public function export_pdf_pelanggan_dengan_transaksi_mengunduh_file_pdf()
    {
        $this->buatTransaksi();

        $response = $this->actingAs($this->admin)
            ->get(route('report.pelanggan.pdf', $this->pelanggan));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function export_excel_pelanggan_dengan_transaksi_mengunduh_file_excel()
    {
        $this->buatTransaksi();

        $response = $this->actingAs($this->admin)
            ->get(route('report.pelanggan.excel', $this->pelanggan));

        $response->assertStatus(200);
        $response->assertDownload();
    }

    /** @test */
    public function export_pdf_pelanggan_tanpa_transaksi_tetap_berhasil_diunduh()
    {
        // Koleksi transaksi kosong → PDF tetap tergenerate tanpa data
        $pelangganBaru = Pelanggan::create(['name' => 'Tanpa Transaksi', 'kontak' => '089900000000']);

        $response = $this->actingAs($this->admin)
            ->get(route('report.pelanggan.pdf', $pelangganBaru));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function export_excel_pelanggan_tanpa_transaksi_tetap_berhasil_diunduh()
    {
        $pelangganBaru = Pelanggan::create(['name' => 'Kosong Excel', 'kontak' => '088800000000']);

        $response = $this->actingAs($this->admin)
            ->get(route('report.pelanggan.excel', $pelangganBaru));

        $response->assertStatus(200);
        $response->assertDownload();
    }
}
