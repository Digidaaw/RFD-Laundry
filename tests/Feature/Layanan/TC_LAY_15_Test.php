<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_15_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 15: Mengupdate data layanan dengan menambahkan unit layanan
     * Expected: Sistem berhasil menyimpan layanan dengan lebih dari satu unit
     */
    public function test_update_dengan_tambah_unit()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Layanan cuci jasa standar',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertCount(1, $layanan->fresh()->units);

        $updatedData = [
            'name' => 'Cuci Jasa Lengkap',
            'deskripsi' => 'Layanan cuci jasa dengan banyak pilihan unit',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 12000,
                ],
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 6000,
                ],
                [
                    'unit_satuan' => 'meter',
                    'harga' => 8000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $updatedData);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil diperbarui.');

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa Lengkap',
            'deskripsi' => 'Layanan cuci jasa dengan banyak pilihan unit',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 12000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'pcs',
            'harga' => 6000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'meter',
            'harga' => 8000,
        ]);

        $updatedLayanan = Layanan::find($layanan->id);
        $this->assertCount(3, $updatedLayanan->units);
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'pcs'));
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'meter'));

        $kgUnit = $updatedLayanan->units()->where('unit_satuan', 'kg')->first();
        $pcsUnit = $updatedLayanan->units()->where('unit_satuan', 'pcs')->first();
        $meterUnit = $updatedLayanan->units()->where('unit_satuan', 'meter')->first();

        $this->assertEquals(12000, $kgUnit->harga);
        $this->assertEquals(6000, $pcsUnit->harga);
        $this->assertEquals(8000, $meterUnit->harga);
        $this->assertEquals('Cuci Jasa Lengkap', $updatedLayanan->name);
        $this->assertEquals('Layanan cuci jasa dengan banyak pilihan unit', $updatedLayanan->deskripsi);
    }
}

