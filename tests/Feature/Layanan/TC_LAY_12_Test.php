<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_12_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 12: Mengupdate data layanan dengan field wajib kosong
     * Expected: Sistem menampilkan pesan validasi field wajib
     */
    public function test_update_dengan_field_kosong()
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

        $dataWithoutName = [
            'name' => '',
            'deskripsi' => 'Deskripsi yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutName);

        $response->assertSessionHasErrors('name');
        $response->assertRedirect();

        $dataWithoutDeskripsi = [
            'name' => 'Cuci Jasa',
            'deskripsi' => '',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutDeskripsi);

        $response->assertSessionHasErrors('deskripsi');
        $response->assertRedirect();

        $dataWithoutUnits = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid dan lengkap',
            'units' => [],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutUnits);

        $response->assertSessionHasErrors('units');
        $response->assertRedirect();

        $dataWithoutGambar = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'images_to_delete' => ['image.jpg'],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutGambar);

        $response->assertSessionHasErrors('gambar');
        $response->assertRedirect();

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
        ]);

        $this->assertDatabaseMissing('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa',
        ]);
    }
}

