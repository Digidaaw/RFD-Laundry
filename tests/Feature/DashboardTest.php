<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_login_dari_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_terotentikasi_dapat_akses_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')
            ->assertStatus(200);
    }

    public function test_dashboard_menampilkan_total_user(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->kasir()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalUser', 4);
    }

    public function test_dashboard_menampilkan_total_transaksi(): void
    {
        $admin     = User::factory()->admin()->create();
        $pelanggan = Pelanggan::factory()->create();

        Transaksi::factory()->count(5)->create([
            'id_user'      => $admin->id,
            'id_pelanggan' => $pelanggan->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertViewHas('totalOrder', 5);
    }

    public function test_dashboard_menampilkan_total_pendapatan(): void
    {
        $admin     = User::factory()->admin()->create();
        $pelanggan = Pelanggan::factory()->create();

        Transaksi::factory()->create([
            'id_user'      => $admin->id,
            'id_pelanggan' => $pelanggan->id,
            'total_harga'  => 50000,
            'jumlah_bayar' => 50000,
            'sisa_bayar'   => 0,
        ]);
        Transaksi::factory()->create([
            'id_user'      => $admin->id,
            'id_pelanggan' => $pelanggan->id,
            'total_harga'  => 30000,
            'jumlah_bayar' => 30000,
            'sisa_bayar'   => 0,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertViewHas('totalSales', 80000);
    }

    public function test_dashboard_menampilkan_total_piutang(): void
    {
        $admin     = User::factory()->admin()->create();
        $pelanggan = Pelanggan::factory()->create();

        Transaksi::factory()->create([
            'id_user'           => $admin->id,
            'id_pelanggan'      => $pelanggan->id,
            'total_harga'       => 20000,
            'jumlah_bayar'      => 10000,
            'sisa_bayar'        => 10000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertViewHas('orderPending', 10000);
    }
}
