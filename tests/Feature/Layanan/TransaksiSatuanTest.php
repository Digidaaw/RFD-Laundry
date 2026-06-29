<?php

namespace Tests\Feature\Layanan;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\LayananUnit;

class TransaksiSatuanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Mengizinkan qty desimal untuk satuan kg dan meter.
     */
    public function test_store_mengizinkan_qty_desimal_untuk_kg_dan_meter()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pelanggan = Pelanggan::create(['name' => 'Pelanggan Test', 'kontak' => '08123456789']);

        $layanan1 = Layanan::factory()->create(['name' => 'Layanan KG']);
        $layanan2 = Layanan::factory()->create(['name' => 'Layanan Meter']);

        LayananUnit::create(['layanan_id' => $layanan1->id, 'unit_satuan' => 'kg', 'harga' => 10000]);
        LayananUnit::create(['layanan_id' => $layanan2->id, 'unit_satuan' => 'meter', 'harga' => 5000]);

        $payload = [
            'id_pelanggan' => $pelanggan->id,
            'jumlah_bayar' => 999999,
            'items' => [
                ['id_layanan' => $layanan1->id, 'unit_satuan' => 'kg', 'qty' => 1.25],
                ['id_layanan' => $layanan2->id, 'unit_satuan' => 'meter', 'qty' => 0.5],
            ],
        ];

        $response = $this->postJson(route('transaksi.store'), $payload);

        $response->assertStatus(302);
    }

    /**
     * Menolak qty desimal untuk satuan pcs.
     */
    public function test_store_menolak_qty_desimal_untuk_pcs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pelanggan = Pelanggan::create(['name' => 'Pelanggan Test', 'kontak' => '08123456789']);

        $layanan = Layanan::factory()->create(['name' => 'Layanan PCS']);
        LayananUnit::create(['layanan_id' => $layanan->id, 'unit_satuan' => 'pcs', 'harga' => 2000]);

        $payload = [
            'id_pelanggan' => $pelanggan->id,
            'jumlah_bayar' => 999999,
            'items' => [
                ['id_layanan' => $layanan->id, 'unit_satuan' => 'pcs', 'qty' => 1.5],
            ],
        ];

        $response = $this->postJson(route('transaksi.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items.0.qty']);
    }

    /**
     * Menerima qty integer untuk satuan pcs.
     */
    public function test_store_menerima_qty_integer_untuk_pcs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pelanggan = Pelanggan::create(['name' => 'Pelanggan Test', 'kontak' => '08123456789']);

        $layanan = Layanan::factory()->create(['name' => 'Layanan PCS']);
        LayananUnit::create(['layanan_id' => $layanan->id, 'unit_satuan' => 'pcs', 'harga' => 2000]);

        $payload = [
            'id_pelanggan' => $pelanggan->id,
            'jumlah_bayar' => 999999,
            'items' => [
                ['id_layanan' => $layanan->id, 'unit_satuan' => 'pcs', 'qty' => 2],
            ],
        ];

        $response = $this->postJson(route('transaksi.store'), $payload);

        $response->assertStatus(302);
    }
}


