<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_13_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 13: Mengupdate data layanan dengan format harga tidak valid
     * Expected: Sistem menampilkan validasi bahwa harga harus angka
     */
    public function testCannotUpdateLayananWithInvalidPrice()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $dataWithNonNumericPrice = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 'bukan angka',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithNonNumericPrice);

        $response->assertSessionHasErrors('units.0.harga');
        $response->assertRedirect();

        $dataWithZeroPrice = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithZeroPrice);

        $response->assertSessionHasErrors('units.0.harga');
        $response->assertRedirect();

        $dataWithNegativePrice = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => -5000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithNegativePrice);

        $response->assertSessionHasErrors('units.0.harga');
        $response->assertRedirect();

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertDatabaseMissing('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa',
        ]);
    }
}
