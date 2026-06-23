<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;
use Illuminate\Http\UploadedFile;

class TC_LAY_01_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 1: Menambahkan data layanan dengan data valid
     * Expected: Sistem berhasil menyimpan data layanan dan menampilkan data pada tabel layanan
     */
    public function test_add_dengan_data_valid()
    {
        $validData = [
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa reguler dengan kualitas terjamin dan harga terjangkau',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('laundry1.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $validData);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil ditambahkan.');

        $this->assertDatabaseHas('layanans', [
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa reguler dengan kualitas terjamin dan harga terjangkau',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $layanan = Layanan::where('name', 'Cuci Jasa Reguler')->first();
        $this->assertNotNull($layanan);
        $this->assertCount(1, $layanan->units);
        $this->assertTrue($layanan->units->contains('unit_satuan', 'kg'));

        $this->assertIsArray($layanan->gambar);
        $this->assertCount(1, $layanan->gambar);
    }
}

