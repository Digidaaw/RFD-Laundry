<?php

namespace Tests\Unit\Layanan_Test;

use Tests\TestCase;

class QtyValidationUnitTest extends TestCase
{
    // Helper untuk simulasi logika validasi qty berdasarkan unit satuan.
    private function validateQty(string $unitSatuan, $qty): bool
    {
        if ($unitSatuan === 'pcs') {
            if ($qty < 1 || (int) $qty != $qty) {
                return false;
            }
        }

        if (in_array($unitSatuan, ['kg', 'meter'])) {
            if ($qty < 0.1 || !is_numeric($qty)) {
                return false;
            }
        }

        return true;
    }

    // Test Qty desimal diperbolehkan untuk KG.
    public function test_qty_desimal_diperbolehkan_untuk_kg()
    {
        $this->assertTrue($this->validateQty('kg', 1.25));
        $this->assertTrue($this->validateQty('kg', 0.5));
    }

    // Test Qty desimal diperbolehkan untuk Meter.
    public function test_qty_desimal_diperbolehkan_untuk_meter()
    {
        $this->assertTrue($this->validateQty('meter', 2.5));
        $this->assertTrue($this->validateQty('meter', 0.75));
    }

    // Test Qty untuk PCS harus berupa bilangan bulat.
    public function test_qty_harus_bilangan_bulat_untuk_pcs()
    {
        $this->assertTrue($this->validateQty('pcs', 2));
        $this->assertTrue($this->validateQty('pcs', 10));

        $this->assertFalse($this->validateQty('pcs', 1.5));
        $this->assertFalse($this->validateQty('pcs', 0.5));
    }

    // Test Qty untuk PCS tidak boleh kurang dari 1.
    public function test_qty_pcs_minimal_satu()
    {
        $this->assertFalse($this->validateQty('pcs', 0));
        $this->assertFalse($this->validateQty('pcs', -1));
    }
}
