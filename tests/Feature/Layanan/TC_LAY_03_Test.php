<?php

namespace Tests\Feature\Layanan;

use Illuminate\Http\UploadedFile;

class TC_LAY_03_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 3: Menambahkan data layanan dengan format harga tidak valid
     * Expected: Sistem hanya bisa menambahkan harga dengan angka
     */
    public function test_add_dengan_harga_tidak_valid()
    {
        $dataWithInvalidPrice = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 'tidak_angka',
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithInvalidPrice);

        $response->assertSessionHasErrors('units.0.harga');

        $dataWithEmptyPrice = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => '',
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithEmptyPrice);

        $response->assertSessionHasErrors('units.0.harga');

        $dataWithZeroPrice = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 0,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithZeroPrice);

        $response->assertSessionHasErrors('units.0.harga');
        $this->assertDatabaseMissing('layanans', ['name' => 'Layanan Valid']);
    }
}

