<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_10_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 10: Mengurutkan data layanan
     * Expected: Sistem menampilkan data sesuai urutan yang dipilih
     */
    public function testCanSortLayananData()
    {
        $layanan1 = Layanan::create([
            'name' => 'Layanan A',
            'deskripsi' => 'Deskripsi layanan A',
            'gambar' => ['image1.jpg'],
        ]);
        $layanan1->forceFill([
            'created_at' => '2026-01-01 10:00:00',
            'updated_at' => '2026-01-01 10:00:00',
        ])->save();

        $layanan2 = Layanan::create([
            'name' => 'Layanan B',
            'deskripsi' => 'Deskripsi layanan B',
            'gambar' => ['image2.jpg'],
        ]);
        $layanan2->forceFill([
            'created_at' => '2026-01-01 10:01:00',
            'updated_at' => '2026-01-01 10:01:00',
        ])->save();

        $layanan3 = Layanan::create([
            'name' => 'Layanan C',
            'deskripsi' => 'Deskripsi layanan C',
            'gambar' => ['image3.jpg'],
        ]);
        $layanan3->forceFill([
            'created_at' => '2026-01-01 10:02:00',
            'updated_at' => '2026-01-01 10:02:00',
        ])->save();

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['sort' => 'updated_latest']));

        $response->assertStatus(200);
        $layanans = $response->viewData('layanans');
        $response->assertViewHas('sort', 'updated_latest');
        $firstItem = $layanans->first();
        $this->assertEquals('Layanan C', $firstItem->name);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['sort' => 'updated_oldest']));

        $response->assertStatus(200);
        $layanans = $response->viewData('layanans');
        $response->assertViewHas('sort', 'updated_oldest');
        $firstItem = $layanans->first();
        $this->assertEquals('Layanan A', $firstItem->name);

        $layanan4 = Layanan::create([
            'name' => 'Cuci A',
            'deskripsi' => 'Deskripsi cuci',
            'gambar' => ['image4.jpg'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', [
                'search' => 'Layanan',
                'sort' => 'updated_oldest'
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('search', 'Layanan');
        $response->assertViewHas('sort', 'updated_oldest');
        $response->assertSee('Layanan A');
        $response->assertSee('Layanan B');
        $response->assertSee('Layanan C');
        $response->assertDontSee('Cuci A');
    }
}
