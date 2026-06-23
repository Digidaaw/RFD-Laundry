<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_08_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 8: Mencari data layanan yang tersedia
     * Expected: Sistem menampilkan data layanan sesuai keyword pencarian
     */
    public function test_cari_layanan_ada()
    {
        $layanan1 = Layanan::create([
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa standar',
            'gambar' => ['image1.jpg'],
        ]);

        $layanan2 = Layanan::create([
            'name' => 'Setrika Profesional',
            'deskripsi' => 'Layanan setrika dengan hasil sempurna',
            'gambar' => ['image2.jpg'],
        ]);

        $layanan3 = Layanan::create([
            'name' => 'Cuci Kering Express',
            'deskripsi' => 'Layanan cuci kering cepat',
            'gambar' => ['image3.jpg'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Cuci']));

        $response->assertStatus(200);

        $layanans = $response->viewData('layanans');
        $names = $layanans->pluck('name')->all();
        $this->assertContains($layanan1->name, $names);
        $this->assertContains($layanan3->name, $names);
        $this->assertNotContains($layanan2->name, $names);

        $response->assertSee($layanan1->name);
        $response->assertSee($layanan3->name);
        $response->assertDontSee($layanan2->name);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Setrika']));

        $response->assertStatus(200);
        $response->assertSee($layanan2->name);
        $response->assertDontSee($layanan1->name);
        $response->assertDontSee($layanan3->name);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Express']));

        $response->assertStatus(200);
        $response->assertSee($layanan3->name);
        $response->assertDontSee($layanan1->name);
        $response->assertDontSee($layanan2->name);
        $response->assertViewHas('search', 'Express');
    }
}

