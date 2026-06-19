<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KasirTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Buat user admin untuk login
        $admin = User::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
    }

    /** @test */
    public function tc_ksr_01_halaman_kasir_tampil_dengan_daftar_kasir()
    {
        User::create([
            'name' => 'Kasir Satu',
            'username' => 'kasir1',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        $response = $this->get('/users');
        $response->assertStatus(200);
        $response->assertSee('Kasir Satu');
    }

    /** @test */
    public function tc_ksr_02_tambah_kasir_baru_berhasil()
    {
        $data = [
            'name' => 'Kasir Baru',
            'username' => 'kasir_baru',
            'password' => 'kasir123',
            'password_confirmation' => 'kasir123',
            'role' => 'kasir',
        ];

        $response = $this->post('/users', $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'name' => 'Kasir Baru',
            'username' => 'kasir_baru',
            'role' => 'kasir',
        ]);
    }

    /** @test */
    public function tc_ksr_03_tambah_kasir_dengan_nama_kosong_gagal()
    {
        $data = [
            'name' => '',
            'username' => 'kasir2',
            'password' => 'kasir123',
            'password_confirmation' => 'kasir123',
            'role' => 'kasir',
        ];

        $response = $this->post('/users', $data);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('users', [
            'username' => 'kasir2',
        ]);
    }

    /** @test */
    public function tc_ksr_04_tambah_kasir_dengan_username_yang_sudah_ada_gagal()
    {
        User::create([
            'name' => 'Kasir Existing',
            'username' => 'kasir_existing',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        $data = [
            'name' => 'Kasir Baru',
            'username' => 'kasir_existing',
            'password' => 'kasir123',
            'password_confirmation' => 'kasir123',
            'role' => 'kasir',
        ];

        $response = $this->post('/users', $data);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('users', [
            'name' => 'Kasir Baru',
        ]);
    }

    /** @test */
public function tc_ksr_05_update_kasir_berhasil()
{
    $kasir = User::create([
        'name' => 'Kasir Lama',
        'username' => 'kasir_update',
        'password' => Hash::make('kasir123'),
        'role' => 'kasir',
    ]);

    $data = [
        'name' => 'Kasirnya',
        'username' => 'kasir_update',
        'role' => 'kasir',
    ];

    $response = $this->put("/users/{$kasir->id}", $data);
    $response->assertStatus(302);

    $this->assertDatabaseHas('users', [
        'id' => $kasir->id,
        'name' => 'Kasirnya',
        'username' => 'kasir_update',
    ]);
}

    /** @test */
    public function tc_ksr_06_delete_kasir_berhasil()
    {
        $kasir = User::create([
            'name' => 'Kasir To Delete',
            'username' => 'kasir_delete',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
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
            'name' => 'Kasir A',
            'username' => 'kasir_a',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        User::create([
            'name' => 'Staff Toko',
            'username' => 'staff_toko',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        $response = $this->get('/users?search=kasir');
        $response->assertStatus(200);
        $response->assertSee('Kasir A');
    }
}
