<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Pelanggan $pelanggan;
    private Layanan $layanan;
    private LayananUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin     = User::factory()->admin()->create();
        $this->pelanggan = Pelanggan::factory()->create();
        $this->layanan   = Layanan::factory()->create();
        $this->unit      = LayananUnit::factory()->create([
            'layanan_id'  => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga'       => 10000,
        ]);
    }

    // ── Akses tanpa auth ─────────────────────────────────────────────────────

    public function test_tamu_tidak_dapat_akses_transaksi(): void
    {
        $this->get(route('transaksi.index'))->assertRedirect('/login');
    }

    // ── Index & Filter ───────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_transaksi(): void
    {
        $this->actingAs($this->admin)->get(route('transaksi.index'))
            ->assertStatus(200);
    }

    public function test_filter_transaksi_by_status_lunas(): void
    {
        Transaksi::factory()->create([
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $this->pelanggan->id,
            'status_pembayaran' => 'Lunas',
            'no_invoice'        => 'IJTEST0001',
        ]);
        Transaksi::factory()->create([
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $this->pelanggan->id,
            'status_pembayaran' => 'DP',
            'no_invoice'        => 'IJTEST0002',
            'sisa_bayar'        => 5000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('transaksi.index', ['type' => 'lunas']));

        $response->assertSee('IJTEST0001');
        $response->assertDontSee('IJTEST0002');
    }

    // ── Create: Multi-item transaksi ─────────────────────────────────────────

    public function test_dapat_membuat_transaksi_multi_item_lunas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'potongan'     => 0,
            'jumlah_bayar' => 20000,
            'items'        => [
                [
                    'id_layanan'  => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty'         => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('transaksi.index'));
        $transaksi = Transaksi::first();
        $this->assertNotNull($transaksi);
        $this->assertEquals(20000, $transaksi->subtotal);
        $this->assertEquals('Lunas', $transaksi->status_pembayaran);
    }

    public function test_nomor_invoice_digenerate_otomatis(): void
    {
        $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 20000,
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
        ]);

        $transaksi = Transaksi::first();
        $this->assertStringStartsWith('IJ', $transaksi->no_invoice);
        $this->assertEquals(14, strlen($transaksi->no_invoice)); // IJ + ddmmyyyy (8) + id (4)
    }

    public function test_transaksi_dp_ketika_bayar_kurang_dari_total(): void
    {
        $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 10000, // 50% dari 20000
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
        ]);

        $transaksi = Transaksi::first();
        $this->assertEquals('DP', $transaksi->status_pembayaran);
        $this->assertEquals(10000, $transaksi->sisa_bayar);
    }

    public function test_potongan_diterapkan_ke_total_harga(): void
    {
        $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'potongan'     => 5000,
            'jumlah_bayar' => 15000, // 100% dari 15000
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
        ]);

        $transaksi = Transaksi::first();
        $this->assertEquals(20000, $transaksi->subtotal);
        $this->assertEquals(5000, $transaksi->potongan);
        $this->assertEquals(15000, $transaksi->total_harga);
        $this->assertEquals('Lunas', $transaksi->status_pembayaran);
    }

    // ── Validasi Business Logic ──────────────────────────────────────────────

    public function test_pembayaran_kurang_dari_50_persen_ditolak(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 9000, // < 50% dari 20000
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
        ]);

        $response->assertSessionHasErrors(['jumlah_bayar']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_potongan_melebihi_subtotal_ditolak(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'potongan'     => 99999,
            'jumlah_bayar' => 10000,
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
        ]);

        $response->assertSessionHasErrors(['potongan']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_unit_tidak_valid_untuk_layanan_ditolak(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 10000,
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'meter', 'qty' => 1],
            ],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_qty_pcs_harus_bilangan_bulat(): void
    {
        LayananUnit::factory()->create([
            'layanan_id'  => $this->layanan->id,
            'unit_satuan' => 'pcs',
            'harga'       => 5000,
        ]);

        $response = $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 10000,
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'pcs', 'qty' => 1.5],
            ],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_transaksi_item_disimpan_dengan_benar(): void
    {
        $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 30000,
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 3],
            ],
        ]);

        $this->assertDatabaseHas('transaksi_items', [
            'layanan_id'   => $this->layanan->id,
            'qty'          => 3,
            'harga_satuan' => 10000,
            'subtotal'     => 30000,
        ]);
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function test_dapat_update_pembayaran_transaksi(): void
    {
        $transaksi = Transaksi::factory()->dp()->create([
            'id_user'      => $this->admin->id,
            'id_pelanggan' => $this->pelanggan->id,
            'subtotal'     => 20000,
            'total_harga'  => 20000,
            'jumlah_bayar' => 10000,
            'sisa_bayar'   => 10000,
        ]);

        $response = $this->actingAs($this->admin)->put(route('transaksi.update', $transaksi), [
            'id_pelanggan'  => $this->pelanggan->id,
            'tanggal_order' => now()->format('Y-m-d'),
            'jumlah_bayar'  => 20000,
        ]);

        $response->assertRedirect();
        $transaksi->refresh();
        $this->assertEquals('Lunas', $transaksi->status_pembayaran);
        $this->assertEquals(0, $transaksi->sisa_bayar);
    }

    public function test_update_jumlah_bayar_melebihi_total_ditolak(): void
    {
        $transaksi = Transaksi::factory()->create([
            'id_user'      => $this->admin->id,
            'id_pelanggan' => $this->pelanggan->id,
            'total_harga'  => 20000,
            'jumlah_bayar' => 20000,
            'sisa_bayar'   => 0,
        ]);

        $response = $this->actingAs($this->admin)->put(route('transaksi.update', $transaksi), [
            'id_pelanggan'  => $this->pelanggan->id,
            'tanggal_order' => now()->format('Y-m-d'),
            'jumlah_bayar'  => 99999,
        ]);

        $response->assertSessionHasErrors(['jumlah_bayar']);
    }

    // ── Bayar Piutang ────────────────────────────────────────────────────────

    public function test_dapat_bayar_piutang(): void
    {
        $transaksi = Transaksi::factory()->create([
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $this->pelanggan->id,
            'total_harga'       => 20000,
            'jumlah_bayar'      => 10000,
            'sisa_bayar'        => 10000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('transaksi.bayar', $transaksi), [
                'bayar_sekarang' => 10000,
            ]);

        $response->assertRedirect(route('report.piutang'));
        $transaksi->refresh();
        $this->assertEquals('Lunas', $transaksi->status_pembayaran);
        $this->assertEquals(0, $transaksi->sisa_bayar);
    }

    public function test_bayar_piutang_melebihi_sisa_ditolak(): void
    {
        $transaksi = Transaksi::factory()->create([
            'id_user'           => $this->admin->id,
            'id_pelanggan'      => $this->pelanggan->id,
            'total_harga'       => 20000,
            'jumlah_bayar'      => 10000,
            'sisa_bayar'        => 10000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('transaksi.bayar', $transaksi), [
                'bayar_sekarang' => 99999,
            ]);

        $response->assertSessionHasErrors(['bayar_sekarang']);
    }

    // ── Relasi & Field ───────────────────────────────────────────────────────

    public function test_transaksi_menyimpan_created_by_username(): void
    {
        $this->actingAs($this->admin)->post(route('transaksi.store'), [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 20000,
            'items'        => [
                ['id_layanan' => $this->layanan->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
        ]);

        $transaksi = Transaksi::first();
        $this->assertEquals($this->admin->username, $transaksi->created_by);
    }
}
