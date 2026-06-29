<?php

namespace Tests\Feature\WhiteBoxLaporanTest;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TC_RPT04_FilterTanggalSamaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Administrator',
            'role'     => 'admin',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        Pelanggan::create([
            'name'   => 'Andi Susanto',
            'kontak' => '081234567890',
        ]);

        Layanan::create([
            'name'      => 'Cuci Reguler',
            'gambar'    => [],
            'deskripsi' => 'Layanan cuci biasa',
        ]);
    }

    /** @test */
    public function end_date_sebelum_start_date_auto_default_ke_bulan_berjalan()
    {
        // Behavior baru: tanggal terbalik → auto-reset ke default bulan ini, tidak error
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-31',
            'end_date'   => '2025-01-01',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('shared.report.periode');
        $response->assertSessionDoesntHaveErrors();
        $response->assertViewHas('startDate', fn($d) => $d->isSameDay(Carbon::now()->startOfMonth()));
        $response->assertViewHas('endDate', fn($d) => $d->isSameDay(Carbon::now()->endOfMonth()));
    }

    /** @test */
    public function end_date_sama_dengan_start_date_lolos_validasi()
    {
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-05-20',
            'end_date'   => '2025-05-20',
        ]));

        $response->assertStatus(200);
        $response->assertSessionDoesntHaveErrors('end_date');
    }

    /** @test */
    public function end_date_setelah_start_date_lolos_validasi()
    {
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-01-31',
        ]));

        $response->assertStatus(200);
        $response->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function start_date_bukan_format_tanggal_valid_ditolak()
    {
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => 'bukan-tanggal',
            'end_date'   => '2025-01-31',
        ]));

        $response->assertSessionHasErrors('start_date');
    }

    /** @test */
    public function end_date_bukan_format_tanggal_valid_ditolak()
    {
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => '2025-01-01',
            'end_date'   => 'bukan-tanggal',
        ]));

        $response->assertSessionHasErrors('end_date');
    }
}
