<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // ── Akses tanpa auth ─────────────────────────────────────────────────────

    public function test_tamu_tidak_dapat_akses_daftar_user(): void
    {
        $this->get(route('users.index'))->assertRedirect('/login');
    }

    public function test_tamu_tidak_dapat_membuat_user(): void
    {
        $this->post(route('users.store'), [])->assertRedirect('/login');
    }

    // ── Admin: lihat daftar user ─────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('users.index'))
            ->assertStatus(200);
    }

    public function test_daftar_user_dapat_dicari_by_nama(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'Kasir Khusus']);
        User::factory()->create(['name' => 'Operator Umum']);

        $response = $this->actingAs($admin)
            ->get(route('users.index', ['search' => 'Khusus']));

        $response->assertStatus(200);
        $response->assertSee('Kasir Khusus');
        $response->assertDontSee('Operator Umum');
    }

    // ── Admin: buat user ─────────────────────────────────────────────────────

    public function test_admin_dapat_membuat_user_kasir(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'     => 'Kasir Baru',
            'username' => 'kasirbaru',
            'password' => 'password123',
            'role'     => 'kasir',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'username' => 'kasirbaru',
            'role'     => 'kasir',
        ]);
    }

    public function test_validasi_username_wajib_min_4_karakter(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'     => 'User Test',
            'username' => 'ab',
            'password' => 'password123',
            'role'     => 'kasir',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_validasi_username_harus_unik(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'sudahada']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'     => 'User Test',
            'username' => 'sudahada',
            'password' => 'password123',
            'role'     => 'kasir',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_validasi_password_minimal_6_karakter(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'     => 'User Test',
            'username' => 'usertest',
            'password' => '12345',
            'role'     => 'kasir',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validasi_role_hanya_admin_atau_kasir(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'     => 'User Test',
            'username' => 'usertest',
            'password' => 'password123',
            'role'     => 'superuser',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    // ── Admin: update user ───────────────────────────────────────────────────

    public function test_admin_dapat_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->kasir()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put(route('users.update', $user), [
            'name'     => 'Nama Baru',
            'username' => $user->username,
            'role'     => 'kasir',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nama Baru']);
    }

    public function test_update_user_dapat_ganti_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->kasir()->create();

        $this->actingAs($admin)->put(route('users.update', $user), [
            'name'     => $user->name,
            'username' => $user->username,
            'password' => 'newpassword123',
            'role'     => 'kasir',
        ]);

        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->password));
    }

    // ── Admin: hapus user ────────────────────────────────────────────────────

    public function test_admin_dapat_hapus_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->kasir()->create();

        $response = $this->actingAs($admin)->delete(route('users.destroy', $user));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
