<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_07_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 7: Membatalkan penambahan layanan setelah form diisi
     * Expected: Form tertutup dan data tidak tersimpan ke tabel layanan
     */
    public function test_batal_tambah_form_layanan()
    {
        $existingCount = Layanan::count();

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        $finalCount = Layanan::count();
        $this->assertEquals($existingCount, $finalCount);

        $this->assertDatabaseMissing('layanans', [
            'name' => 'Layanan Yang Dibatalkan',
        ]);

        $response->assertViewHasAll(['layanans', 'search', 'sort']);
    }
}

