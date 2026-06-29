<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    protected $faker;
    protected $validPassword = 'password123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('id_ID');

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        User::query()->update(['password' => Hash::make($this->validPassword)]);
    }

    /** @test */
    public function tc_log_01_login_dengan_username_spasi_di_akhir()
    {
        $response = $this->post('/login', [
            'username' => 'admin ',
            'password' => $this->validPassword . ' ',
        ]);

        $response->assertStatus(302);
         $this->assertGuest();
    }

    /** @test */
    public function tc_log_02_login_normal_dengan_username_dan_password_benar()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => $this->validPassword,
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
        //untuk menambahkan data random
        $response = $this->post('/login', [
            'username' => $this->faker->userName(),
            'password' => $this->faker->password(12),
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
            'password' => $this->validPassword,
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    /** @test */
    public function tc_log_06_login_dengan_username_karakter_khusus()
    {
        $response = $this->post('/login', [
            'username' => $this->faker->userName() . '@#$',
            'password' => $this->validPassword,
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
            'password' => $this->validPassword,
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
        $username = $this->faker->unique()->userName();
        $name     = $this->faker->name();
        $password = 'password123';

        $response = $this->post('/register', [
            'name'                  => $name,
            'username'              => $username,
            'password'              => $password,
            'password_confirmation' => $password,
            'role'                  => 'kasir',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', ['username' => $username]);
    }
}
