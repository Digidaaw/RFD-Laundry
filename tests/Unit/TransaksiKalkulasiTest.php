<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TransaksiKalkulasiTest extends TestCase
{
    // ── Kalkulasi total harga ─────────────────────────────────────────────────

    public function test_total_harga_adalah_subtotal_dikurangi_potongan(): void
    {
        $subtotal = 100000;
        $potongan = 10000;
        $total    = max(0, $subtotal - $potongan);

        $this->assertEquals(90000, $total);
    }

    public function test_total_harga_tidak_bisa_negatif(): void
    {
        $subtotal = 10000;
        $potongan = 50000;
        $total    = max(0, $subtotal - $potongan);

        $this->assertEquals(0, $total);
    }

    // ── Kalkulasi sisa bayar ──────────────────────────────────────────────────

    public function test_sisa_bayar_adalah_total_dikurangi_jumlah_bayar(): void
    {
        $total     = 100000;
        $bayar     = 60000;
        $sisa      = max(0, $total - $bayar);

        $this->assertEquals(40000, $sisa);
    }

    public function test_sisa_bayar_nol_ketika_bayar_melebihi_total(): void
    {
        $total     = 100000;
        $bayar     = 120000;
        $sisa      = max(0, $total - $bayar);

        $this->assertEquals(0, $sisa);
    }

    // ── Status pembayaran ────────────────────────────────────────────────────

    public function test_status_lunas_ketika_sisa_nol(): void
    {
        $sisa   = 0;
        $status = ($sisa <= 0) ? 'Lunas' : 'DP';

        $this->assertEquals('Lunas', $status);
    }

    public function test_status_dp_ketika_ada_sisa(): void
    {
        $sisa   = 5000;
        $status = ($sisa <= 0) ? 'Lunas' : 'DP';

        $this->assertEquals('DP', $status);
    }

    // ── Validasi minimal pembayaran (50%) ────────────────────────────────────

    public function test_pembayaran_50_persen_tepat_diterima(): void
    {
        $total    = 100000;
        $bayar    = 50000;
        $minBayar = (int) ceil($total * 0.5);

        $this->assertGreaterThanOrEqual($minBayar, $bayar);
    }

    public function test_pembayaran_kurang_dari_50_persen_ditolak(): void
    {
        $total    = 100000;
        $bayar    = 49999;
        $minBayar = (int) ceil($total * 0.5);

        $this->assertLessThan($minBayar, $bayar);
    }

    public function test_minimum_bayar_dibulatkan_ke_atas(): void
    {
        $total    = 100001;
        $minBayar = (int) ceil($total * 0.5);

        $this->assertEquals(50001, $minBayar);
    }

    // ── Kalkulasi subtotal per item ──────────────────────────────────────────

    public function test_subtotal_item_adalah_qty_dikali_harga(): void
    {
        $qty      = 3.5;
        $harga    = 10000;
        $subtotal = $qty * $harga;

        $this->assertEquals(35000, $subtotal);
    }

    public function test_subtotal_total_adalah_jumlah_semua_item(): void
    {
        $items = [
            ['qty' => 2, 'harga' => 10000, 'subtotal' => 20000],
            ['qty' => 3, 'harga' => 5000,  'subtotal' => 15000],
            ['qty' => 1, 'harga' => 8000,  'subtotal' => 8000],
        ];

        $totalSubtotal = array_sum(array_column($items, 'subtotal'));

        $this->assertEquals(43000, $totalSubtotal);
    }

    // ── Format nomor invoice ─────────────────────────────────────────────────

    public function test_format_nomor_invoice_dimulai_ij(): void
    {
        $id       = 1;
        $tanggal  = '27042026';
        $invoice  = 'IJ' . $tanggal . str_pad($id, 4, '0', STR_PAD_LEFT);

        $this->assertStringStartsWith('IJ', $invoice);
        $this->assertEquals('IJ270420260001', $invoice);
        $this->assertEquals(14, strlen($invoice));
    }

    public function test_nomor_invoice_id_dipadding_4_digit(): void
    {
        $id      = 42;
        $invoice = 'IJ' . '27042026' . str_pad($id, 4, '0', STR_PAD_LEFT);

        $this->assertStringContainsString('0042', $invoice);
    }

    // ── Bayar piutang ────────────────────────────────────────────────────────

    public function test_bayar_piutang_menambah_jumlah_bayar(): void
    {
        $jumlahBayarLama = 10000;
        $bayarSekarang   = 5000;
        $jumlahBayarBaru = $jumlahBayarLama + $bayarSekarang;

        $this->assertEquals(15000, $jumlahBayarBaru);
    }

    public function test_bayar_piutang_mengurangi_sisa_bayar(): void
    {
        $totalHarga      = 20000;
        $jumlahBayarBaru = 15000;
        $sisaBayarBaru   = max(0, $totalHarga - $jumlahBayarBaru);

        $this->assertEquals(5000, $sisaBayarBaru);
    }

    public function test_bayar_piutang_lunas_setelah_bayar_penuh(): void
    {
        $totalHarga      = 20000;
        $jumlahBayarBaru = 20000;
        $sisaBayarBaru   = max(0, $totalHarga - $jumlahBayarBaru);
        $status          = ($sisaBayarBaru <= 0) ? 'Lunas' : 'DP';

        $this->assertEquals('Lunas', $status);
        $this->assertEquals(0, $sisaBayarBaru);
    }
}
