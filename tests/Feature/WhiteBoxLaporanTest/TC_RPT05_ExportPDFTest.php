<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TC_RPT06a_ExportPDFTest extends TestCase
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
    public function tanpa_export_melewati_kedua_cabang_pdf_excel_dan_mengembalikan_view()
    {
        // Path 1: export=null → if pdf = false, if excel = false → return view
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-01-31',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $this->assertNull($response->headers->get('Content-Disposition'));
    }

    /** @test */
    public function export_pdf_memasuki_cabang_pdf_dan_mengunduh_file_pdf()
    {
        // Path 2: export='pdf' → if pdf = TRUE → return PDF download
        $this->buatTransaksi();

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-01-31',
            'export'     => 'pdf',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function export_excel_melewati_cabang_pdf_masuk_cabang_excel_dan_mengunduh_file()
    {
        // Path 3: export='excel' → if pdf = false → if excel = TRUE → return Excel download
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
    public function export_dengan_nilai_tidak_valid_ditolak_validasi()
    {
        // Validasi rule: 'in:pdf,excel' — nilai lain ditolak
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-01-31',
            'export'     => 'csv',
        ]));

        $response->assertSessionHasErrors('export');
    }
}
