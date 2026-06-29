<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;
use Illuminate\Http\UploadedFile;

class TC_LAY_14_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 14: Mengupdate gambar layanan dengan format file tidak valid
     * Expected: Sistem menampilkan validasi format file tidak didukung
     */
    public function test_update_dengan_format_gambar_tidak_valid()
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

        $dataWithPdfFile = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($dataWithPdfFile, ['_method' => 'PUT']));

        $response->assertSessionHasErrors('gambar.0');
        $response->assertRedirect();

        $dataWithTxtFile = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('text.txt', 100, 'text/plain'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($dataWithTxtFile, ['_method' => 'PUT']));

        $response->assertSessionHasErrors('gambar.0');
        $response->assertRedirect();

        $dataWithLargeFile = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
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
            ->post(route('layanan.update', $layanan->id), array_merge($dataWithLargeFile, ['_method' => 'PUT']));

        $response->assertSessionHasErrors('gambar.0');
        $response->assertRedirect();

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
        ]);
    }
}



