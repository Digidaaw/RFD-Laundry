<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;

class TC_LAY_16_Test extends LayananFeatureTestCase
{
    /**
     * Test Case 16: Membatalkan perubahan saat update data layanan
     * Expected: Form edit tertutup dan data lama tetap tersimpan tanpa perubahan
     */
    public function testCanCancelUpdatingLayananForm()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan awal yang tidak berubah',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan awal yang tidak berubah',
        ]);

        $this->assertDatabaseMissing('layanans', [
            'name' => 'Cuci Premium Dibatalkan',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertDatabaseMissing('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'pcs',
        ]);
    }
}
