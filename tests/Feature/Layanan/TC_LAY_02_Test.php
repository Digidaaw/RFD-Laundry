<?php

namespace Tests\Feature\Layanan;

use Illuminate\Http\UploadedFile;

class TC_LAY_02_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 2: Menambahkan data layanan dengan field kosong
     * Expected: Sistem menampilkan pesan validasi bahwa field wajib harus diisi
     */
    public function test_add_dengan_field_kosong()
    {
        $dataWithoutName = [
            'name' => '',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap minimal 5 karakter',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutName);

        $response->assertSessionHasErrors('name');
        $response->assertSessionHas('errors');

        $dataWithoutDeskripsi = [
            'name' => 'Layanan Valid',
            'deskripsi' => '',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutDeskripsi);

        $response->assertSessionHasErrors('deskripsi');

        $dataWithoutUnits = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutUnits);

        $response->assertSessionHasErrors('units');

        $dataWithoutImage = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutImage);

        $response->assertSessionHasErrors('gambar');
        $this->assertDatabaseMissing('layanans', ['name' => 'Layanan Valid']);
    }
}



