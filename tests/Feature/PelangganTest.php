<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->admin()->create());
    }

    // ── Akses tanpa auth ─────────────────────────────────────────────────────

    public function test_tamu_tidak_dapat_akses_pelanggan(): void
    {
        $this->get(route('pelanggan.index'))->assertRedirect('/login');
    }

    // ── Index & Search ───────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_pelanggan(): void
    {
        $this->actingAsAdmin()->get(route('pelanggan.index'))
            ->assertStatus(200);
    }

    public function test_daftar_pelanggan_dapat_dicari_by_nama(): void
    {
        $admin = User::factory()->admin()->create();
        Pelanggan::factory()->create(['name' => 'Budi Santoso']);
        Pelanggan::factory()->create(['name' => 'Siti Aminah']);

        $response = $this->actingAs($admin)
            ->get(route('pelanggan.index', ['search' => 'Budi']));

        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Siti Aminah');
    }

    public function test_daftar_pelanggan_dapat_dicari_by_kontak(): void
    {
        $admin = User::factory()->admin()->create();
        Pelanggan::factory()->create(['name' => 'Budi', 'kontak' => '081234567890']);
        Pelanggan::factory()->create(['name' => 'Siti', 'kontak' => '089876543210']);

        $response = $this->actingAs($admin)
            ->get(route('pelanggan.index', ['search' => '081234567890']));

        $response->assertSee('Budi');
        $response->assertDontSee('Siti');
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function test_dapat_membuat_pelanggan_baru(): void
    {
        $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'name'   => 'Pelanggan Test',
            'kontak' => '081234567890',
        ]);

        $this->assertDatabaseHas('pelanggans', ['name' => 'Pelanggan Test']);
    }

    public function test_validasi_nama_pelanggan_wajib(): void
    {
        $response = $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'kontak' => '081234567890',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_validasi_kontak_wajib(): void
    {
        $response = $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'name' => 'Pelanggan Test',
        ]);

        $response->assertSessionHasErrors(['kontak']);
    }

    public function test_validasi_kontak_minimal_10_digit(): void
    {
        $response = $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'name'   => 'Pelanggan Test',
            'kontak' => '081234',
        ]);

        $response->assertSessionHasErrors(['kontak']);
    }

    public function test_validasi_kontak_maksimal_13_digit(): void
    {
        $response = $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'name'   => 'Pelanggan Test',
            'kontak' => '0812345678901234',
        ]);

        $response->assertSessionHasErrors(['kontak']);
    }

    public function test_validasi_kontak_harus_unik(): void
    {
        Pelanggan::factory()->create(['kontak' => '081234567890']);

        $response = $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'name'   => 'Pelanggan Lain',
            'kontak' => '081234567890',
        ]);

        $response->assertSessionHasErrors(['kontak']);
    }

    public function test_validasi_kontak_harus_numerik(): void
    {
        $response = $this->actingAsAdmin()->post(route('pelanggan.store'), [
            'name'   => 'Pelanggan Test',
            'kontak' => 'abcdefghij',
        ]);

        $response->assertSessionHasErrors(['kontak']);
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_dapat_update_pelanggan(): void
    {
        $pelanggan = Pelanggan::factory()->create(['name' => 'Nama Lama']);

        $this->actingAsAdmin()->put(route('pelanggan.update', $pelanggan), [
            'name'   => 'Nama Baru',
            'kontak' => $pelanggan->kontak,
        ]);

        $this->assertDatabaseHas('pelanggans', ['id' => $pelanggan->id, 'name' => 'Nama Baru']);
    }

    public function test_update_kontak_dapat_sama_dengan_diri_sendiri(): void
    {
        $pelanggan = Pelanggan::factory()->create(['kontak' => '081234567890']);

        $response = $this->actingAsAdmin()->put(route('pelanggan.update', $pelanggan), [
            'name'   => $pelanggan->name,
            'kontak' => '081234567890',
        ]);

        $response->assertSessionDoesntHaveErrors(['kontak']);
    }

}
