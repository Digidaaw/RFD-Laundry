<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KasirTest extends TestCase
{
    use RefreshDatabase;

    protected $faker;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create('id_ID');

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->admin = User::create([
            'name'     => $this->faker->name(),
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        $this->actingAs($this->admin);
    }

    /** @test */
    public function tc_ksr_01_halaman_kasir_tampil_dengan_daftar_kasir()
    {
        $kasirName = $this->faker->name();

        User::create([
            'name'     => $kasirName,
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make($this->faker->password(8)),
            'role'     => 'kasir',
        ]);

        $response = $this->get('/users');
        $response->assertStatus(200);
        $response->assertSee($kasirName);
    }

    /** @test */
    public function tc_ksr_02_tambah_kasir_baru_berhasil()
    {
        $name     = $this->faker->name();
        $username = $this->faker->unique()->userName();
        $password = 'kasir123';

        $data = [
            'name'                  => $name,
            'username'              => $username,
            'password'              => $password,
            'password_confirmation' => $password,
            'role'                  => 'kasir',
        ];

        $response = $this->post('/users', $data);
        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'name'     => $name,
            'username' => $username,
            'role'     => 'kasir',
        ]);
    }

    /** @test */
    public function tc_ksr_03_tambah_kasir_dengan_nama_kosong_gagal()
    {
        $username = $this->faker->unique()->userName();

        $data = [
            'name'                  => '',
            'username'              => $username,
            'password'              => 'kasir123',
            'password_confirmation' => 'kasir123',
            'role'                  => 'kasir',
        ];

        $response = $this->post('/users', $data);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('users', [
            'username' => $username,
        ]);
    }

    /** @test */
    public function tc_ksr_04_tambah_kasir_dengan_username_yang_sudah_ada_gagal()
    {
        $existingUsername = $this->faker->unique()->userName();
        $existingName     = $this->faker->name();
        $newName          = $this->faker->name();

        User::create([
            'name'     => $existingName,
            'username' => $existingUsername,
            'password' => Hash::make('password123'),
            'role'     => 'kasir',
        ]);

        $data = [
            'name'                  => $newName,
            'username'              => $existingUsername,
            'password'              => 'kasir123',
            'password_confirmation' => 'kasir123',
            'role'                  => 'kasir',
        ];

        $response = $this->post('/users', $data);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('users', [
            'name' => $newName,
        ]);
    }

    /** @test */
    public function tc_ksr_05_update_kasir_berhasil()
    {
        $username = $this->faker->unique()->userName();
        $newName  = $this->faker->name();

        $kasir = User::create([
            'name'     => $this->faker->name(),
            'username' => $username,
            'password' => Hash::make('kasir123'),
            'role'     => 'kasir',
        ]);

        $data = [
            'name'     => $newName,
            'username' => $username,
            'role'     => 'kasir',
        ];

        $response = $this->put("/users/{$kasir->id}", $data);
        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'id'       => $kasir->id,
            'name'     => $newName,
            'username' => $username,
        ]);
    }

    /** @test */
    public function tc_ksr_06_delete_kasir_berhasil()
    {
        $kasir = User::create([
            'name'     => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password123'),
            'role'     => 'kasir',
        ]);

        $response = $this->delete("/users/{$kasir->id}");
        $response->assertStatus(302);

        $this->assertDatabaseMissing('users', [
            'id' => $kasir->id,
        ]);
    }

    /** @test */
    public function tc_ksr_07_search_kasir_berdasarkan_keyword()
    {
        User::create([
            'name'     => 'Kasir ' . $this->faker->firstName(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password123'),
            'role'     => 'kasir',
        ]);

        User::create([
            'name'     => 'Staff ' . $this->faker->firstName(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password123'),
            'role'     => 'kasir',
        ]);

        $response = $this->get('/users?search=Kasir');
        $response->assertStatus(200);
        $response->assertSee('Kasir');
    }
}
