<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_09_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 9: Mencari data layanan yang tidak tersedia
     * Expected: Sistem menampilkan pesan bahwa data layanan tidak ditemukan
     */
    public function test_cari_layanan_tidak_ada()
    {
        Layanan::create([
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa standar',
            'gambar' => ['image1.jpg'],
        ]);

        Layanan::create([
            'name' => 'Setrika Profesional',
            'deskripsi' => 'Layanan setrika dengan hasil sempurna',
            'gambar' => ['image2.jpg'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Jahit']));

        $response->assertStatus(200);

        $layanans = $response->viewData('layanans');
        $this->assertCount(0, $layanans);
        $this->assertEquals('Jahit', $response->viewData('search'));

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'xyz']));

        $response->assertStatus(200);
        $layanans = $response->viewData('layanans');
        $this->assertCount(0, $layanans);
    }
}

