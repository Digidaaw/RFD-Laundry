<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    // ── Akses tanpa auth ─────────────────────────────────────────────────────

    public function test_tamu_tidak_dapat_akses_report(): void
    {
        $this->get(route('report.index'))->assertRedirect('/login');
    }

    // ── Report Index ─────────────────────────────────────────────────────────

    public function test_admin_dapat_akses_halaman_report(): void
    {
        $this->actingAs($this->admin)->get(route('report.index'))
            ->assertStatus(200);
    }

    // ── Report Periode ───────────────────────────────────────────────────────

    public function test_report_periode_dapat_diakses(): void
    {
        $this->actingAs($this->admin)
            ->get(route('report.periode'))
            ->assertStatus(200);
    }

    public function test_report_periode_dengan_filter_tanggal(): void
    {
        $pelanggan = Pelanggan::factory()->create();

        Transaksi::factory()->create([
            'id_user'       => $this->admin->id,
            'id_pelanggan'  => $pelanggan->id,
            'tanggal_order' => now()->subDays(2),
            'total_harga'   => 50000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => now()->subDays(7)->format('Y-m-d'),
            'end_date'   => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('transaksis');
    }

    public function test_validasi_end_date_tidak_boleh_sebelum_start_date(): void
    {
        $response = $this->actingAs($this->admin)->get(route('report.periode', [
            'start_date' => now()->format('Y-m-d'),
            'end_date'   => now()->subDays(5)->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors(['end_date']);
    }

    // ── Report Piutang ───────────────────────────────────────────────────────

    public function test_report_piutang_dapat_diakses(): void
    {
        $this->actingAs($this->admin)
            ->get(route('report.piutang'))
            ->assertStatus(200);
    }

    public function test_report_piutang_hanya_menampilkan_status_dp(): void
    {
        $pelanggan = Pelanggan::factory()->create(['name' => 'Pelanggan Piutang']);

        Transaksi::factory()->create([
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $pelanggan->id,
            'no_invoice'        => 'IJDP00001',
            'status_pembayaran' => 'DP',
            'sisa_bayar'        => 5000,
        ]);
        Transaksi::factory()->create([
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $pelanggan->id,
            'no_invoice'        => 'IJLUNAS001',
            'status_pembayaran' => 'Lunas',
            'sisa_bayar'        => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('report.piutang'));

        $response->assertSee('IJDP00001');
        $response->assertDontSee('IJLUNAS001');
    }

    // ── Report Pelanggan ─────────────────────────────────────────────────────

    public function test_report_pelanggan_dapat_diakses(): void
    {
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('report.pelanggan', $pelanggan))
            ->assertStatus(200);
    }

    public function test_report_pelanggan_menampilkan_total_transaksi(): void
    {
        $pelanggan = Pelanggan::factory()->create();

        Transaksi::factory()->create([
            'id_user'      => $this->admin->id,
            'id_pelanggan' => $pelanggan->id,
            'total_harga'  => 75000,
            'jumlah_bayar' => 75000,
            'sisa_bayar'   => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('report.pelanggan', $pelanggan));

        $response->assertStatus(200);
        $response->assertViewHas('pelanggan');
    }
}
