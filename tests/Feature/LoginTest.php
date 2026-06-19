<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        User::query()->update(['password' => Hash::make('password123')]);
    }

    /** @test */
    public function tc_log_01_login_dengan_username_spasi_di_akhir()
    {
        $response = $this->post('/login', [
            'username' => 'admin ',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    /** @test */
    public function tc_log_02_login_normal_dengan_username_dan_password_benar()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    /** @test */
    public function tc_log_03_login_dengan_username_dan_password_kosong()
    {
        $response = $this->post('/login', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    /** @test */
    public function tc_log_04_login_dengan_username_dan_password_salah()
    {
        $response = $this->post('/login', [
            'username' => 'usersalah',
            'password' => 'password_salah',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    /** @test */
    public function tc_log_05_login_dengan_username_huruf_besar()
    {
        $response = $this->post('/login', [
            'username' => 'ADMIN',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    /** @test */
    public function tc_log_06_login_dengan_username_karakter_khusus()
    {
        $response = $this->post('/login', [
            'username' => 'userbaru@#$',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    /** @test */
    public function tc_log_07_login_dengan_username_dan_password_hanya_spasi()
    {
        $response = $this->post('/login', [
            'username' => '   ',
            'password' => '   ',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    /** @test */
    public function tc_log_08_logout_setelah_login()
    {
        $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function tc_log_09_akses_dashboard_langsung_tanpa_login()
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function tc_log_10_registrasi_user_baru()
    {
        $response = $this->post('/register', [
            'name' => 'User Baru Lagi',
            'username' => 'userbaru2',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kasir'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', ['username' => 'userbaru2']);
    }
}
