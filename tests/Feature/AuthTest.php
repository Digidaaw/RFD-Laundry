<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Halaman Login ────────────────────────────────────────────────────────

    public function test_halaman_login_dapat_diakses_tanpa_auth(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_terotentikasi_diarahkan_dari_halaman_login(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect();
    }

    // ── Login ────────────────────────────────────────────────────────────────

    public function test_user_dapat_login_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'salah_password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_login_gagal_dengan_username_tidak_ada(): void
    {
        $response = $this->post('/login', [
            'username' => 'tidakada',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_login_validasi_field_wajib(): void
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['username', 'password']);
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function test_user_dapat_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    // ── Register ─────────────────────────────────────────────────────────────

    public function test_halaman_register_dapat_diakses(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_registrasi_berhasil_membuat_user_baru(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'User Baru',
            'username'              => 'userbaru',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'kasir',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['username' => 'userbaru']);
    }

    public function test_registrasi_gagal_username_duplikat(): void
    {
        User::factory()->create(['username' => 'duplikat']);

        $response = $this->post('/register', [
            'name'                  => 'User Lain',
            'username'              => 'duplikat',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'kasir',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_registrasi_gagal_password_tidak_cocok(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'User Baru',
            'username'              => 'userbaru',
            'password'              => 'password123',
            'password_confirmation' => 'berbeda',
            'role'                  => 'kasir',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_password_disimpan_dalam_bentuk_hash(): void
    {
        $this->post('/register', [
            'name'                  => 'User Hash',
            'username'              => 'userhash',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'kasir',
        ]);

        $user = User::where('username', 'userhash')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('password123', $user->getAuthPassword());
    }
}
