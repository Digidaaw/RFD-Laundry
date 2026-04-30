<?php

namespace Tests\Unit;

use App\Models\Layanan;
use App\Models\LayananUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayananModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_layanan_memiliki_relasi_units(): void
    {
        $layanan = Layanan::factory()->create();
        LayananUnit::factory()->count(2)->create(['layanan_id' => $layanan->id]);

        $this->assertCount(2, $layanan->fresh()->units);
        $this->assertInstanceOf(LayananUnit::class, $layanan->units->first());
    }

    public function test_layanan_mendukung_soft_delete(): void
    {
        $layanan = Layanan::factory()->create();
        $layanan->delete();

        $this->assertSoftDeleted('layanans', ['id' => $layanan->id]);
        $this->assertDatabaseHas('layanans', ['id' => $layanan->id]);
    }

    public function test_layanan_yang_terhapus_tidak_muncul_di_query_biasa(): void
    {
        $layanan = Layanan::factory()->create();
        $layanan->delete();

        $this->assertNull(Layanan::find($layanan->id));
        $this->assertNotNull(Layanan::withTrashed()->find($layanan->id));
    }

    public function test_kolom_gambar_dicast_ke_array(): void
    {
        $layanan = Layanan::factory()->create();

        $this->assertIsArray($layanan->gambar);
    }

    public function test_layanan_unit_memiliki_relasi_ke_layanan(): void
    {
        $layanan = Layanan::factory()->create();
        $unit    = LayananUnit::factory()->create(['layanan_id' => $layanan->id]);

        $this->assertEquals($layanan->id, $unit->layanan->id);
        $this->assertInstanceOf(Layanan::class, $unit->layanan);
    }
}
