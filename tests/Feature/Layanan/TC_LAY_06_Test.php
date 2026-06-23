<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;
use Illuminate\Http\UploadedFile;

class TC_LAY_06_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 6: Menambahkan layanan dengan lebih dari satu unit layanan
     * Expected: Sistem berhasil menyimpan layanan dengan lebih dari satu unit
     */
    public function test_add_dengan_multiple_unit()
    {
        $dataWithMultipleUnits = [
            'name' => 'Cuci Kering Plus',
            'deskripsi' => 'Layanan cuci kering dengan pengharum premium dan hasil sempurna',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 15000,
                ],
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 7500,
                ],
                [
                    'unit_satuan' => 'meter',
                    'harga' => 12000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('cuci_kering.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithMultipleUnits);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil ditambahkan.');

        $this->assertDatabaseHas('layanans', [
            'name' => 'Cuci Kering Plus',
            'deskripsi' => 'Layanan cuci kering dengan pengharum premium dan hasil sempurna',
        ]);

        $layanan = Layanan::where('name', 'Cuci Kering Plus')->first();
        $this->assertNotNull($layanan);
        $this->assertCount(3, $layanan->units);
        $this->assertTrue($layanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($layanan->units->contains('unit_satuan', 'pcs'));
        $this->assertTrue($layanan->units->contains('unit_satuan', 'meter'));

        $kgUnit = $layanan->units->where('unit_satuan', 'kg')->first();
        $pcsUnit = $layanan->units->where('unit_satuan', 'pcs')->first();
        $meterUnit = $layanan->units->where('unit_satuan', 'meter')->first();

        $this->assertEquals(15000, $kgUnit->harga);
        $this->assertEquals(7500, $pcsUnit->harga);
        $this->assertEquals(12000, $meterUnit->harga);
    }
}

