<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;
use Illuminate\Http\UploadedFile;

class TC_LAY_11_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 11: Mengupdate data layanan dengan data valid
     * Expected: Sistem berhasil memperbarui data layanan
     */
    public function test_update_dengan_data_valid()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi awal cuci reguler',
            'gambar' => ['old_image.jpg'],
        ]);

        $layanan->units()->createMany([
            [
                'unit_satuan' => 'kg',
                'harga' => 10000,
            ],
            [
                'unit_satuan' => 'pcs',
                'harga' => 5000,
            ],
        ]);

        $updatedData = [
            'name' => 'Cuci Jasa Premium',
            'deskripsi' => 'Layanan cuci jasa premium dengan kualitas terbaik dan hasil maksimal',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 15000,
                ],
                [
                    'unit_satuan' => 'meter',
                    'harga' => 8000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('new_image1.jpg', 500, 500),
                UploadedFile::fake()->image('new_image2.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($updatedData, ['_method' => 'PUT']));

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil diperbarui.');

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa Premium',
            'deskripsi' => 'Layanan cuci jasa premium dengan kualitas terbaik dan hasil maksimal',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 15000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'meter',
            'harga' => 8000,
        ]);

        $this->assertDatabaseMissing('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'pcs',
        ]);

        $updatedLayanan = Layanan::find($layanan->id);
        $this->assertEquals('Cuci Jasa Premium', $updatedLayanan->name);
        $this->assertCount(2, $updatedLayanan->units);
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'meter'));
    }
}



