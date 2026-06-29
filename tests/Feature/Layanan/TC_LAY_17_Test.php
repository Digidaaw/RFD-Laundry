<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_17_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 17: Mengakses halaman layanan tanpa login
     * Expected: Sistem mengarahkan user ke halaman login
     */
    public function test_guest_tidak_akses_halaman_layanan()
    {
        $response = $this->get(route('layanan.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('layanan.store'), []);
        $response->assertRedirect(route('login'));

        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
            'gambar' => ['image.jpg'],
        ]);

        $response = $this->put(route('layanan.update', $layanan->id), [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                ['unit_satuan' => 'kg', 'harga' => 10000],
            ],
        ]);
        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
        ]);
    }
}



