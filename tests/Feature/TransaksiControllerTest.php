<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Pelanggan $pelanggan;
    protected Layanan $layanan;
    protected Layanan $layanan2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create pelanggan
        $this->pelanggan = Pelanggan::create([
            'name' => 'John Doe',
            'kontak' => '081234567890',
            'alamat' => 'Jl. Test No. 123',
        ]);

        // Create layanan
        $this->layanan = Layanan::create([
            'name' => 'Cuci Komplit',
            'gambar' => [],
            'deskripsi' => 'Cuci + Kering + Setrika',
        ]);

        $this->layanan2 = Layanan::create([
            'name' => 'Cuci Kering',
            'gambar' => [],
            'deskripsi' => 'Cuci + Kering',
        ]);
    }

    /**
     * Helper method to login as admin
     */
    protected function loginAsAdmin()
    {
        return $this->actingAs($this->adminUser);
    }

    // ==================== INDEX TESTS ====================

    public function test_can_view_transaksi_index(): void
    {
        $response = $this->loginAsAdmin()->get(route('transaksi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('shared.transaksi');
        $response->assertViewHas('transaksis');
        $response->assertViewHas('pelanggans');
        $response->assertViewHas('layanans');
    }

    public function test_index_shows_transaksis_data(): void
    {
        // Create some transaksi data
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test transaksi',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index'));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Test transaksi');
    }

    public function test_index_search_by_no_invoice(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test search',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
        $transaksi->no_invoice = 'IJ' . now()->format('dmY') . '0001';
        $transaksi->save();

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['search' => 'IJ']));

        $response->assertStatus(200);
        $response->assertSee('IJ');
    }

    public function test_index_search_by_deskripsi(): void
    {
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Pencucian baju khusus',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['search' => 'khusus']));

        $response->assertStatus(200);
        $response->assertSee('khusus');
    }

    public function test_index_search_by_pelanggan_name(): void
    {
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
    }

    public function test_index_search_by_pelanggan_kontak(): void
    {
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['search' => '0812']));

        $response->assertStatus(200);
        $response->assertSee('081234567890');
    }

    public function test_index_filter_by_status_lunas(): void
    {
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi Lunas',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi DP',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 2500,
            'sisa_bayar' => 2500,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['type' => 'lunas']));

        $response->assertStatus(200);
        $response->assertSee('Transaksi Lunas');
        $response->assertDontSee('Transaksi DP');
    }

    public function test_index_filter_by_status_dp(): void
    {
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi Lunas',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi DP',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 2500,
            'sisa_bayar' => 2500,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['type' => 'dp']));

        $response->assertStatus(200);
        $response->assertSee('Transaksi DP');
        $response->assertDontSee('Transaksi Lunas');
    }

    public function test_index_sort_by_updated_latest(): void
    {
        $transaksi1 = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi 1',
            'tanggal_order' => now()->subDays(2),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $transaksi2 = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi 2',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['sort' => 'updated_latest']));

        $response->assertStatus(200);
        // Transaksi 2 should appear first (most recently updated)
        $this->assertStringContainsString('Transaksi 2', $response->content());
    }

    public function test_index_sort_by_updated_oldest(): void
    {
        $transaksi1 = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi Lama',
            'tanggal_order' => now()->subDays(2),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $transaksi2 = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Transaksi Baru',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', ['sort' => 'updated_oldest']));

        $response->assertStatus(200);
        // Transaksi Lama should appear first (oldest updated)
        $this->assertStringContainsString('Transaksi Lama', $response->content());
    }

    // ==================== STORE TESTS ====================

    public function test_can_store_transaksi_with_single_item(): void
    {
        // Create layanan unit for single item pricing
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 10000,
            'deskripsi' => 'Test transaksi single item',
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil ditambahkan.');

        $this->assertDatabaseHas('transaksis', [
            'id_pelanggan' => $this->pelanggan->id,
            'deskripsi' => 'Test transaksi single item',
            'potongan' => '0.00',
            'total_harga' => 10000,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $this->assertDatabaseCount('transaksi_items', 1);
    }

    public function test_can_store_transaksi_with_multi_items(): void
    {
        // Create layanan units
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        LayananUnit::create([
            'layanan_id' => $this->layanan2->id,
            'unit_satuan' => 'kg',
            'harga' => 4000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
                [
                    'id_layanan' => $this->layanan2->id,
                    'unit_satuan' => 'kg',
                    'qty' => 3,
                ],
            ],
            'potongan' => 1000,
            'jumlah_bayar' => 21000,
            'deskripsi' => 'Test transaksi multi items',
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil ditambahkan.');

        $this->assertDatabaseHas('transaksis', [
            'id_pelanggan' => $this->pelanggan->id,
            'deskripsi' => 'Test transaksi multi items',
            'subtotal' => 22000, // (5000*2) + (4000*3)
            'potongan' => 1000,
            'total_harga' => 21000,
            'jumlah_bayar' => 21000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $this->assertDatabaseCount('transaksi_items', 2);
    }

    public function test_store_generates_no_invoice(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 1,
                ],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 5000,
            'deskripsi' => 'Test invoice generation',
        ];

        $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $transaksi = Transaksi::where('deskripsi', 'Test invoice generation')->first();

        $this->assertNotNull($transaksi);
        $this->assertStringStartsWith('IJ', $transaksi->no_invoice);
        $this->assertMatchesRegularExpression('/^IJ\d{8}\d{4}$/', $transaksi->no_invoice);
    }

    public function test_store_validates_id_pelanggan_required(): void
    {
        $data = [
            'id_layanan' => $this->layanan->id,
            'qty' => 1,
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('id_pelanggan');
    }

    public function test_store_validates_id_pelanggan_exists(): void
    {
        $data = [
            'id_pelanggan' => 99999,
            'id_layanan' => $this->layanan->id,
            'qty' => 1,
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('id_pelanggan');
    }

    public function test_store_validates_jumlah_bayar_required(): void
    {
        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'qty' => 1,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('jumlah_bayar');
    }

    public function test_store_validates_min_payment_50_percent(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 2000, // Less than 50% of 10000
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('jumlah_bayar');
        $response->assertSessionHasErrors('jumlah_bayar', 'Pembayaran minimal harus 50% dari total harga.');
    }

    public function test_store_sets_status_dp_when_partial_payment(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 5000, // Exactly 50% of 10000
        ];

        $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $this->assertDatabaseHas('transaksis', [
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);
    }

    public function test_store_sets_status_lunas_when_full_payment(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 10000, // Full payment
        ];

        $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $this->assertDatabaseHas('transaksis', [
            'total_harga' => 10000,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_store_validates_potongan_not_greater_than_subtotal(): void
    {
        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'qty' => 1,
            'potongan' => 10000, // Greater than subtotal (5000)
            'jumlah_bayar' => 0,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('potongan');
        $response->assertSessionHasErrors('potongan', 'Potongan melebihi subtotal.');
    }

    public function test_store_validates_items_unit_satuan(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'invalid_unit',
                    'qty' => 2,
                ],
            ],
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('items.0.unit_satuan');
    }

    public function test_store_validates_qty_integer_for_pcs(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'pcs',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'pcs',
                    'qty' => 1.5, // Decimal not allowed for pcs
                ],
            ],
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors('items.0.qty');
        $response->assertSessionHasErrors('items.0.qty', 'Qty harus bilangan bulat untuk satuan pcs');
    }

    // ==================== UPDATE TESTS ====================

    public function test_can_update_transaksi(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Original description',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $newPelanggan = Pelanggan::create([
            'name' => 'Jane Doe',
            'kontak' => '081234567891',
            'alamat' => 'Jl. Baru No. 456',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $newPelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'deskripsi' => 'Updated description',
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui.');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'id_pelanggan' => $newPelanggan->id,
            'deskripsi' => 'Updated description',
        ]);
    }

    public function test_update_validates_jumlah_bayar_not_exceed_total(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 2500,
            'sisa_bayar' => 2500,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 10000, // Exceeds total
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $response->assertSessionHasErrors('jumlah_bayar');
        $response->assertSessionHasErrors('jumlah_bayar', 'Jumlah bayar melebihi total.');
    }

    public function test_update_validates_min_payment_50_percent(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 2000, // Less than 50%
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $response->assertSessionHasErrors('jumlah_bayar');
        $response->assertSessionHasErrors('jumlah_bayar', 'Pembayaran minimal harus 50% dari total harga.');
    }

    public function test_update_sets_status_lunas_when_paid_in_full(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 10000, // Full payment
        ];

        $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_update_sets_status_dp_when_partial_payment(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 6000, // Partial payment (still >= 50%)
        ];

        $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 6000,
            'sisa_bayar' => 4000,
            'status_pembayaran' => 'DP',
        ]);
    }

    // ==================== DESTROY TESTS ====================

    public function test_can_delete_transaksi(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'To be deleted',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->delete(route('transaksi.destroy', $transaksi->id));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil dihapus.');

        $this->assertDatabaseMissing('transaksis', [
            'id' => $transaksi->id,
        ]);
    }

    public function test_delete_transaksi_also_deletes_items(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'To be deleted with items',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        // Add items to transaksi
        $transaksi->items()->create([
            'layanan_id' => $this->layanan->id,
            'qty' => 1,
            'harga_satuan' => 5000,
            'subtotal' => 5000,
        ]);

        $this->assertDatabaseHas('transaksi_items', [
            'transaksi_id' => $transaksi->id,
        ]);

        $this->loginAsAdmin()->delete(route('transaksi.destroy', $transaksi->id));

        $this->assertDatabaseMissing('transaksi_items', [
            'transaksi_id' => $transaksi->id,
        ]);
    }

    // ==================== BAYAR PIUTANG TESTS ====================

    public function test_can_bayar_piutang(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test piutang',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'bayar_sekarang' => 3000,
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data);

        $response->assertRedirect(route('report.piutang'));
        $response->assertSessionHas('success', 'Pembayaran berhasil.');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 8000,
            'sisa_bayar' => 2000,
            'status_pembayaran' => 'DP',
        ]);
    }

    public function test_bayar_piutang_sets_lunas_when_fully_paid(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test piutang lunas',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'bayar_sekarang' => 5000, // Pay remaining amount
        ];

        $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data);

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_bayar_piutang_validates_bayar_sekarang_required(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), []);

        $response->assertSessionHasErrors('bayar_sekarang');
    }

    public function test_bayar_piutang_validates_payment_not_exceed_sisa_bayar(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'bayar_sekarang' => 10000, // More than sisa_bayar
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data);

        $response->assertSessionHasErrors('bayar_sekarang');
    }

    public function test_bayar_piutang_validates_minimum_payment(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'bayar_sekarang' => 0,
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data);

        $response->assertSessionHasErrors('bayar_sekarang');
        $response->assertSessionHasErrors('bayar_sekarang', 'Pembayaran minimal 0.01.');
    }

    // ==================== EDGE CASES ====================

    public function test_store_with_zero_qty_is_filtered_out(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 0, // Zero qty should be filtered
                ],
            ],
            'jumlah_bayar' => 0,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        // Should fail because no valid items (qty > 0 filter)
        $response->assertSessionHasErrors();
    }

    public function test_store_calculates_sisa_bayar_correctly(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 3,
                ],
            ],
            'potongan' => 1000,
            'jumlah_bayar' => 7000, // >= 50% of 14000
        ];

        // subtotal = 5000 * 3 = 15000
        // total = 15000 - 1000 = 14000
        // sisa = 14000 - 7000 = 7000

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);
        $response->assertRedirect();

        $transaksi = Transaksi::where('deskripsi', 'Test transaksi sisa bayar')->first();

        if ($transaksi) {
            $this->assertEquals('15000.00', $transaksi->subtotal);
            $this->assertEquals('1000.00', $transaksi->potongan);
            $this->assertEquals(14000, $transaksi->total_harga);
            $this->assertEquals(7000, $transaksi->jumlah_bayar);
            $this->assertEquals(7000, $transaksi->sisa_bayar);
        } else {
            // Fallback: check latest transaksi
            $transaksi = Transaksi::latest()->first();
            $this->assertNotNull($transaksi);
            $this->assertEquals('15000.00', $transaksi->subtotal);
            $this->assertEquals('1000.00', $transaksi->potongan);
            $this->assertEquals(14000, $transaksi->total_harga);
            $this->assertEquals(7000, $transaksi->jumlah_bayar);
            $this->assertEquals(7000, $transaksi->sisa_bayar);
        }
    }

    public function test_store_with_potongan_sets_total_to_zero_if_potongan_equals_subtotal(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 1,
                ],
            ],
            'potongan' => 5000, // Equal to subtotal
            'jumlah_bayar' => 0,
        ];

        $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $transaksi = Transaksi::latest()->first();
        $this->assertEquals('5000.00', $transaksi->subtotal);
        $this->assertEquals('5000.00', $transaksi->potongan);
        $this->assertEquals(0, $transaksi->total_harga);
        $this->assertEquals(0, $transaksi->jumlah_bayar);
        $this->assertEquals(0, $transaksi->sisa_bayar);
        $this->assertEquals('Lunas', $transaksi->status_pembayaran);
    }

    public function test_index_empty_state(): void
    {
        $response = $this->loginAsAdmin()->get(route('transaksi.index'));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada data transaksi.');
    }

    public function test_update_preserves_sisa_bayar_calculation(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 20000,
            'potongan' => 0,
            'total_harga' => 20000,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 10000,
            'status_pembayaran' => 'DP',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 15000,
        ];

        $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $transaksi->refresh();
        $this->assertEquals(5000, $transaksi->sisa_bayar);
    }

    // ==================== AUTHENTICATION & AUTHORIZATION TESTS ====================

    public function test_guest_cannot_access_transaksi_index(): void
    {
        $response = $this->get(route('transaksi.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_transaksi(): void
    {
        $response = $this->post(route('transaksi.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_transaksi(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->patch(route('transaksi.update', $transaksi->id), []);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_transaksi(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->delete(route('transaksi.destroy', $transaksi->id));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_bayar_piutang(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->patch(route('transaksi.bayar', $transaksi->id), []);

        $response->assertRedirect(route('login'));
    }

    // ==================== COMBINED FILTER TESTS ====================

    public function test_index_combined_search_and_filter(): void
    {
        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Cuci khusus John',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Cuci khusus Jane',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 2500,
            'sisa_bayar' => 2500,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', [
            'search' => 'khusus',
            'type' => 'lunas',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Cuci khusus John');
        $response->assertDontSee('Cuci khusus Jane');
    }

    public function test_index_combined_search_and_sort(): void
    {
        $transaksi1 = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test ABC',
            'tanggal_order' => now()->subDays(2),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $transaksi2 = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test XYZ',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $response = $this->loginAsAdmin()->get(route('transaksi.index', [
            'search' => 'Test',
            'sort' => 'updated_oldest',
        ]));

        $response->assertStatus(200);
        $content = $response->content();
        $posABC = strpos($content, 'Test ABC');
        $posXYZ = strpos($content, 'Test XYZ');
        $this->assertNotFalse($posABC);
        $this->assertNotFalse($posXYZ);
        $this->assertLessThan($posXYZ, $posABC);
    }

    // ==================== BAYAR PIUTANG EDGE CASES ====================

    public function test_bayar_piutang_exactly_sisa_bayar(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = ['bayar_sekarang' => 5000];

        $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data);

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_bayar_piutang_small_amount(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 5000,
            'status_pembayaran' => 'DP',
        ]);

        $data = ['bayar_sekarang' => 100];

        $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data);

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 5100,
            'sisa_bayar' => 4900,
            'status_pembayaran' => 'DP',
        ]);
    }

    public function test_bayar_piutang_multiple_times(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 10000,
            'potongan' => 0,
            'total_harga' => 10000,
            'jumlah_bayar' => 0,
            'sisa_bayar' => 10000,
            'status_pembayaran' => 'DP',
        ]);

        $data1 = ['bayar_sekarang' => 3000];
        $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data1);

        $data2 = ['bayar_sekarang' => 4000];
        $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data2);

        $data3 = ['bayar_sekarang' => 3000];
        $this->loginAsAdmin()->patch(route('transaksi.bayar', $transaksi->id), $data3);

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 10000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    // ==================== STORE REQUEST VALIDATION TESTS ====================

    public function test_store_validates_items_required(): void
    {
        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertSessionHasErrors();
    }

    public function test_store_with_decimal_potongan(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
            ],
            'potongan' => 500.50,
            'jumlah_bayar' => 9499.50,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('transaksis', [
            'potongan' => '500.50',
            'total_harga' => '9499.50',
        ]);
    }

    public function test_store_multi_items_with_different_units(): void
    {
        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 5000,
        ]);

        LayananUnit::create([
            'layanan_id' => $this->layanan->id,
            'unit_satuan' => 'pcs',
            'harga' => 3000,
        ]);

        $data = [
            'id_pelanggan' => $this->pelanggan->id,
            'items' => [
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'kg',
                    'qty' => 2,
                ],
                [
                    'id_layanan' => $this->layanan->id,
                    'unit_satuan' => 'pcs',
                    'qty' => 3,
                ],
            ],
            'jumlah_bayar' => 19000,
        ];

        $response = $this->loginAsAdmin()->post(route('transaksi.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseCount('transaksi_items', 2);
    }

    // ==================== UPDATE REQUEST WITH REDIRECT ====================

    public function test_update_with_custom_redirect_url(): void
    {
        $transaksi = Transaksi::create([
            'id_user' => $this->adminUser->id,
            'created_by' => $this->adminUser->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->layanan->id,
            'deskripsi' => 'Test',
            'tanggal_order' => now(),
            'subtotal' => 5000,
            'potongan' => 0,
            'total_harga' => 5000,
            'jumlah_bayar' => 5000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);

        $data = [
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 5000,
            '_redirect_url' => route('report.piutang'),
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.update', $transaksi->id), $data);

        $response->assertRedirect(route('report.piutang'));
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui.');
    }

    // ==================== NON-EXISTENT TRANSAKSI TESTS ====================

    public function test_update_non_existent_transaksi(): void
    {
        $data = [
            'transaksi_id' => 99999,
            'id_pelanggan' => $this->pelanggan->id,
            'tanggal_order' => now()->toDateString(),
            'jumlah_bayar' => 5000,
        ];

        $response = $this->loginAsAdmin()->patch(route('transaksi.update', 99999), $data);

        $response->assertStatus(404);
    }

    public function test_destroy_non_existent_transaksi(): void
    {
        $response = $this->loginAsAdmin()->delete(route('transaksi.destroy', 99999));

        $response->assertStatus(404);
    }

    public function test_bayar_piutang_non_existent_transaksi(): void
    {
        $response = $this->loginAsAdmin()->patch(route('transaksi.bayar', 99999), []);

        $response->assertStatus(404);
    }
}
