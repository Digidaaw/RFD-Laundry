<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;
use Illuminate\Http\UploadedFile;

class TC_LAY_05_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 5: Menampilkan dan menutup form tambah layanan
     * Expected: Form tambah layanan berhasil ditampilkan dan ditutup
     */
    public function test_tampilkan_dan_tutup_form_layanan()
    {
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');
        $response->assertViewHasAll(['layanans', 'search', 'sort']);

        $existingLayanan = Layanan::create([
            'name' => 'Cuci Jasa Express',
            'deskripsi' => 'Layanan cuci jasa express dengan hasil cepat',
            'gambar' => ['image1.jpg', 'image2.jpg'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        $layanans = $response->viewData('layanans');
        $this->assertTrue($layanans->contains($existingLayanan));

        $validData = [
            'name' => 'Layanan Setrika',
            'deskripsi' => 'Layanan setrika dengan hasil rapi dan profesional',
            'units' => [
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 8000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('setrika.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $validData);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil ditambahkan.');
    }
}



