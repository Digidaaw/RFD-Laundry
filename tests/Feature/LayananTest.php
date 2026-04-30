<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LayananTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->admin()->create());
    }

    // ── Akses tanpa auth ─────────────────────────────────────────────────────

    public function test_tamu_tidak_dapat_akses_layanan(): void
    {
        $this->get(route('layanan.index'))->assertRedirect('/login');
    }

    // ── Index & Search ───────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_layanan(): void
    {
        $this->actingAsAdmin()->get(route('layanan.index'))
            ->assertStatus(200);
    }

    public function test_daftar_layanan_dapat_dicari_by_nama(): void
    {
        $admin = User::factory()->admin()->create();
        Layanan::factory()->create(['name' => 'Cuci Express']);
        Layanan::factory()->create(['name' => 'Setrika Biasa']);

        $response = $this->actingAs($admin)
            ->get(route('layanan.index', ['search' => 'Express']));

        $response->assertSee('Cuci Express');
        $response->assertDontSee('Setrika Biasa');
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function test_dapat_membuat_layanan_baru_dengan_gambar_dan_unit(): void
    {
        Storage::fake('public');

        $response = $this->actingAsAdmin()->post(route('layanan.store'), [
            'name'         => 'Cuci Kering',
            'deskripsi'    => 'Layanan cuci dan kering pakaian',
            'gambar'       => [UploadedFile::fake()->image('foto.jpg')],
            'units'        => [
                ['unit_satuan' => 'kg', 'harga' => 5000],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('layanans', ['name' => 'Cuci Kering']);
        $this->assertDatabaseHas('layanan_units', ['unit_satuan' => 'kg', 'harga' => 5000]);
    }

    public function test_validasi_nama_layanan_wajib(): void
    {
        Storage::fake('public');

        $response = $this->actingAsAdmin()->post(route('layanan.store'), [
            'deskripsi' => 'Deskripsi layanan',
            'gambar'    => [UploadedFile::fake()->image('foto.jpg')],
            'units'     => [['unit_satuan' => 'kg', 'harga' => 5000]],
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_validasi_deskripsi_minimal_5_karakter(): void
    {
        Storage::fake('public');

        $response = $this->actingAsAdmin()->post(route('layanan.store'), [
            'name'      => 'Layanan Test',
            'deskripsi' => 'hi',
            'gambar'    => [UploadedFile::fake()->image('foto.jpg')],
            'units'     => [['unit_satuan' => 'kg', 'harga' => 5000]],
        ]);

        $response->assertSessionHasErrors(['deskripsi']);
    }

    public function test_validasi_units_wajib_minimal_1(): void
    {
        Storage::fake('public');

        $response = $this->actingAsAdmin()->post(route('layanan.store'), [
            'name'      => 'Layanan Test',
            'deskripsi' => 'Deskripsi yang cukup panjang',
            'gambar'    => [UploadedFile::fake()->image('foto.jpg')],
            'units'     => [],
        ]);

        $response->assertSessionHasErrors(['units']);
    }

    public function test_validasi_unit_satuan_hanya_kg_pcs_meter(): void
    {
        Storage::fake('public');

        $response = $this->actingAsAdmin()->post(route('layanan.store'), [
            'name'      => 'Layanan Test',
            'deskripsi' => 'Deskripsi yang cukup panjang',
            'gambar'    => [UploadedFile::fake()->image('foto.jpg')],
            'units'     => [['unit_satuan' => 'liter', 'harga' => 5000]],
        ]);

        $response->assertSessionHasErrors(['units.0.unit_satuan']);
    }

    public function test_validasi_gambar_wajib(): void
    {
        $response = $this->actingAsAdmin()->post(route('layanan.store'), [
            'name'      => 'Layanan Test',
            'deskripsi' => 'Deskripsi yang cukup panjang',
            'gambar'    => [],
            'units'     => [['unit_satuan' => 'kg', 'harga' => 5000]],
        ]);

        $response->assertSessionHasErrors(['gambar']);
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function test_dapat_melihat_detail_layanan(): void
    {
        $layanan = Layanan::factory()->create();

        $this->actingAsAdmin()->get(route('layanan.show', $layanan))
            ->assertStatus(200);
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_dapat_update_layanan(): void
    {
        Storage::fake('public');
        $layanan = Layanan::factory()->create(['name' => 'Nama Lama']);
        LayananUnit::factory()->create(['layanan_id' => $layanan->id, 'unit_satuan' => 'kg', 'harga' => 5000]);

        $response = $this->actingAsAdmin()->put(route('layanan.update', $layanan), [
            'name'      => 'Nama Baru',
            'deskripsi' => $layanan->deskripsi,
            'units'     => [['unit_satuan' => 'kg', 'harga' => 7000]],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('layanans', ['id' => $layanan->id, 'name' => 'Nama Baru']);
    }

    // ── Soft Delete ──────────────────────────────────────────────────────────

    public function test_menghapus_layanan_adalah_soft_delete(): void
    {
        $layanan = Layanan::factory()->create();

        $this->actingAsAdmin()->delete(route('layanan.destroy', $layanan));

        $this->assertSoftDeleted('layanans', ['id' => $layanan->id]);
    }

    public function test_layanan_yang_dihapus_tidak_muncul_di_daftar(): void
    {
        $admin   = User::factory()->admin()->create();
        $layanan = Layanan::factory()->create(['name' => 'Layanan Terhapus']);
        $layanan->delete();

        $response = $this->actingAs($admin)->get(route('layanan.index'));

        $response->assertDontSee('Layanan Terhapus');
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function test_layanan_memiliki_banyak_units(): void
    {
        $layanan = Layanan::factory()->create();
        LayananUnit::factory()->count(3)->create(['layanan_id' => $layanan->id]);

        $this->assertCount(3, $layanan->units);
    }
}
