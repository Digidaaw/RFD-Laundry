<?php

namespace Tests\Feature\Layanan;

use Illuminate\Http\UploadedFile;

class TC_LAY_04_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 4: Mengupload gambar layanan dengan format file tidak valid
     * Expected: Sistem hanya menerima format gambar yang didukung
     */
    public function testCannotStoreLayananWithInvalidImageFormat()
    {
        $dataWithPdfFile = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('document.pdf', 512, 'application/pdf'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithPdfFile);

        $response->assertSessionHasErrors('gambar.0');

        $dataWithTextFile = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('document.txt', 512, 'text/plain'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithTextFile);

        $response->assertSessionHasErrors('gambar.0');

        $dataWithLargeImage = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('large.jpg')->size(3000),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithLargeImage);

        $response->assertSessionHasErrors('gambar.0');
        $this->assertDatabaseMissing('layanans', ['name' => 'Layanan Valid']);
    }
}
