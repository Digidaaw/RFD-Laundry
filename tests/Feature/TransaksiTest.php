<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    private User $user;
    private Pelanggan $pelanggan;
    private Layanan $cuci;
    private Layanan $setrika;
    private bool $usesSqliteInMemory = false;
    private bool $startedTransaction = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->useSqliteInMemoryDatabase();
            $this->createSchema();
            $this->usesSqliteInMemory = true;
        } else {
            DB::beginTransaction();
            $this->startedTransaction = true;
        }

        $this->user = User::create([
            'name' => 'Admin Transaksi',
            'role' => 'admin',
            'username' => 'admin_trx_' . uniqid(),
            'password' => 'password',
        ]);

        $this->pelanggan = Pelanggan::create([
            'name' => 'Budi Laundry',
            'kontak' => '0812' . random_int(10000000, 99999999),
        ]);

        $this->cuci = $this->createLayanan('Cuci Kering', [
            'kg' => 10000,
            'pcs' => 5000,
        ]);

        $this->setrika = $this->createLayanan('Setrika', [
            'kg' => 8000,
            'pcs' => 3000,
        ]);

        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        if ($this->startedTransaction) {
            DB::rollBack();
            $this->startedTransaction = false;
        }

        parent::tearDown();
    }

    private function useSqliteInMemoryDatabase(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
    }

    private function createSchema(): void
    {
        $schema = DB::connection()->getSchemaBuilder();

        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('role', ['admin', 'kasir']);
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $schema->create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kontak')->unique();
            $table->timestamps();
        });

        $schema->create('layanans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('gambar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('layanan_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('layanan_id');
            $table->enum('unit_satuan', ['kg', 'pcs', 'meter']);
            $table->double('harga', 10, 2);
            $table->timestamps();
        });

        $schema->create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique()->nullable();
            $table->string('deskripsi')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_layanan')->nullable();
            $table->date('tanggal_order');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('potongan', 10, 2)->default(0);
            $table->double('total_harga');
            $table->double('jumlah_bayar');
            $table->double('sisa_bayar');
            $table->enum('status_pembayaran', ['DP', 'Lunas']);
            $table->timestamps();
        });

        $schema->create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('layanan_id');
            $table->string('unit_satuan', 20)->nullable();
            $table->decimal('qty', 10, 2);
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function test_tc_trx_01_tambah_transaksi_dengan_berat_desimal(): void
    {
        $response = $this->post(route('transaksi.store'), $this->payload([
            'items' => [
                $this->item($this->cuci, 'kg', 2.5),
            ],
            'jumlah_bayar' => 25000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transaksis', [
            'id_pelanggan' => $this->pelanggan->id,
            'subtotal' => 25000,
            'total_harga' => 25000,
            'jumlah_bayar' => 25000,
            'status_pembayaran' => 'Lunas',
        ]);

        $this->assertDatabaseHas('transaksi_items', [
            'layanan_id' => $this->cuci->id,
            'unit_satuan' => 'kg',
            'qty' => 2.5,
            'subtotal' => 25000,
        ]);
    }

    public function test_tc_trx_02_tambah_transaksi_tanpa_jumlah_bayar(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload(['jumlah_bayar' => null]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('jumlah_bayar');
        $this->assertSame($transaksiCount, Transaksi::count());
    }

    public function test_tc_trx_03_tambah_transaksi_tanpa_layanan(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'items' => [
                    ['id_layanan' => '', 'unit_satuan' => 'kg', 'qty' => 2],
                ],
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('items.0.id_layanan');
        $this->assertSame($transaksiCount, Transaksi::count());
    }

    public function test_tc_trx_04_tambah_transaksi_tanpa_unit(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'items' => [
                    ['id_layanan' => $this->cuci->id, 'unit_satuan' => '', 'qty' => 2],
                ],
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('items.0.unit_satuan');
        $this->assertSame($transaksiCount, Transaksi::count());
    }

    public function test_tc_trx_05_tambah_transaksi_tanpa_berat(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'items' => [
                    ['id_layanan' => $this->cuci->id, 'unit_satuan' => 'kg', 'qty' => null],
                ],
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('items.0.qty');
        $this->assertSame($transaksiCount, Transaksi::count());
    }

    public function test_tc_trx_06_tambah_transaksi_tanpa_memilih_pelanggan(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload(['id_pelanggan' => null]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('id_pelanggan');
        $this->assertSame($transaksiCount, Transaksi::count());
    }

    public function test_tc_trx_07_tambah_transaksi_dengan_potongan_dan_jumlah_bayar_menggunakan_huruf(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), $this->payload([
                'potongan' => 'seribu',
                'jumlah_bayar' => 'lima puluh ribu',
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors(['potongan', 'jumlah_bayar']);
        $this->assertSame($transaksiCount, Transaksi::count());
    }

    public function test_tc_trx_08_tambah_transaksi_dengan_data_lengkap_dan_valid(): void
    {
        $response = $this->post(route('transaksi.store'), $this->payload([
            'deskripsi' => 'Kemeja dan celana',
            'items' => [
                $this->item($this->cuci, 'kg', 3),
            ],
            'potongan' => 5000,
            'jumlah_bayar' => 25000,
        ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil ditambahkan.');
        $response->assertSessionHas('show_print_modal');

        $this->assertDatabaseHas('transaksis', [
            'id_user' => $this->user->id,
            'created_by' => $this->user->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->cuci->id,
            'deskripsi' => 'Kemeja dan celana',
            'subtotal' => 30000,
            'potongan' => 5000,
            'total_harga' => 25000,
            'jumlah_bayar' => 25000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_tc_trx_09_search_transaksi_dengan_nama_pelanggan_yang_tidak_ada_di_database(): void
    {
        $this->createTransaksi(['id_pelanggan' => $this->pelanggan->id]);

        $response = $this->get(route('transaksi.index', ['search' => 'Pelanggan Tidak Ada']));

        $response->assertOk();
        $response->assertSee('Tidak ada data transaksi.');
    }

    public function test_tc_trx_10_search_bar_mencari_data_nama_pelanggan_yang_ada_di_database(): void
    {
        $this->createTransaksi(['id_pelanggan' => $this->pelanggan->id]);

        $response = $this->get(route('transaksi.index', ['search' => 'Budi']));

        $response->assertOk();
        $response->assertSee('Budi Laundry');
        $response->assertSee('Kelola Transaksi');
    }

    public function test_tc_trx_11_update_transaksi_mengubah_jumlah_bayar_dengan_maksimal_total_bayarnya(): void
    {
        $transaksi = $this->createTransaksi([
            'total_harga' => 30000,
            'jumlah_bayar' => 15000,
            'sisa_bayar' => 15000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->put(route('transaksi.update', $transaksi), $this->updatePayload($transaksi, [
            'jumlah_bayar' => 30000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 30000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_tc_trx_12_update_jumlah_transaksi_dengan_nominal_dibawah_jumlah_bayar_sebelumnya(): void
    {
        $transaksi = $this->createTransaksi([
            'total_harga' => 30000,
            'jumlah_bayar' => 20000,
            'sisa_bayar' => 10000,
            'status_pembayaran' => 'DP',
        ]);

        $response = $this->from(route('transaksi.index'))
            ->put(route('transaksi.update', $transaksi), $this->updatePayload($transaksi, [
                'jumlah_bayar' => 10000,
            ]));

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors('jumlah_bayar');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'jumlah_bayar' => 20000,
        ]);
    }

    public function test_tc_trx_13_tombol_batal_atau_tanda_silang_pada_modal_tambah_transaksi(): void
    {
        $response = $this->get(route('transaksi.index'));

        $response->assertOk();
        $response->assertSee('Batal');
        $response->assertSee('openAddModal = false', false);
        $response->assertSee('&times;', false);
    }

    public function test_tc_trx_14_cetak_struk_transaksi(): void
    {
        $transaksi = $this->createTransaksi([
            'no_invoice' => 'IJTEST0001',
        ]);

        $response = $this->get(route('transaksi.cetak-struk', $transaksi));

        $response->assertOk();
        $response->assertDownload('Struk-Transaksi-IJTEST0001.pdf');
    }

    public function test_tc_trx_15_tambah_layanan_lebih_dari_1_pada_fitur_tambah_transaksi(): void
    {
        $response = $this->post(route('transaksi.store'), $this->payload([
            'items' => [
                $this->item($this->cuci, 'kg', 2),
                $this->item($this->setrika, 'pcs', 3),
            ],
            'jumlah_bayar' => 29000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $transaksi = Transaksi::latest('id')->first();

        $this->assertSame(2, $transaksi->items()->count());
        $this->assertEquals(29000, (float) $transaksi->subtotal);
        $this->assertEquals(29000, (float) $transaksi->total_harga);
    }

    public function test_tc_trx_16_hapus_layanan_yang_lebih_dari_1_pada_fitur_tambah_transaksi(): void
    {
        $response = $this->get(route('transaksi.index'));

        $response->assertOk();
        $response->assertSee('removeItem(index)', false);
        $response->assertSee("if (this.items.length <= 1) return", false);
        $response->assertSee('Tambah Layanan');
    }

    public function test_tc_trx_17_tambah_layanan_lebih_dari_1_pada_fitur_update_transaksi(): void
    {
        $transaksi = $this->createTransaksi();

        $response = $this->put(route('transaksi.update', $transaksi), $this->updatePayload($transaksi, [
            'items' => [
                $this->item($this->cuci, 'kg', 2),
                $this->item($this->setrika, 'pcs', 2),
            ],
            'jumlah_bayar' => 26000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $this->assertSame(2, $transaksi->fresh()->items()->count());
        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'subtotal' => 26000,
            'total_harga' => 26000,
            'jumlah_bayar' => 26000,
        ]);
    }

    public function test_tc_trx_18_delete_layanan_yang_lebih_dari_1_pada_fitur_update_transaksi(): void
    {
        $transaksi = $this->createTransaksiWithItems([
            $this->item($this->cuci, 'kg', 2),
            $this->item($this->setrika, 'pcs', 2),
        ]);

        $response = $this->put(route('transaksi.update', $transaksi), $this->updatePayload($transaksi, [
            'items' => [
                $this->item($this->cuci, 'kg', 2),
            ],
            'jumlah_bayar' => 20000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $this->assertSame(1, $transaksi->fresh()->items()->count());
        $this->assertDatabaseHas('transaksi_items', [
            'transaksi_id' => $transaksi->id,
            'layanan_id' => $this->cuci->id,
            'subtotal' => 20000,
        ]);
    }

    public function test_tc_trx_19_update_tanggal_order_pelanggan_deskripsi_layanan_dengan_mengkosongkan_kolomnya(): void
    {
        $transaksi = $this->createTransaksi();

        $response = $this->from(route('transaksi.index'))
            ->put(route('transaksi.update', $transaksi), [
                'transaksi_id' => $transaksi->id,
                'id_pelanggan' => '',
                'tanggal_order' => '',
                'deskripsi' => '',
                'potongan' => 0,
                'jumlah_bayar' => 20000,
                'items' => [
                    ['id_layanan' => '', 'unit_satuan' => '', 'qty' => ''],
                ],
            ]);

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors([
            'tanggal_order',
            'id_pelanggan',
            'items.0.id_layanan',
            'items.0.unit_satuan',
            'items.0.qty',
        ]);
    }

    public function test_tc_trx_20_update_tanggal_order_pelanggan_deskripsi_layanan_jumlah_bayar_sesuai_dengan_variabel_kolomnya(): void
    {
        $transaksi = $this->createTransaksi();
        $pelangganBaru = Pelanggan::create([
            'name' => 'Siti Laundry',
            'kontak' => '0821' . random_int(10000000, 99999999),
        ]);

        $response = $this->put(route('transaksi.update', $transaksi), $this->updatePayload($transaksi, [
            'id_pelanggan' => $pelangganBaru->id,
            'tanggal_order' => '2026-05-20',
            'deskripsi' => 'Bed cover besar',
            'items' => [
                $this->item($this->setrika, 'kg', 2),
            ],
            'jumlah_bayar' => 16000,
        ]));

        $response->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'id_pelanggan' => $pelangganBaru->id,
            'tanggal_order' => '2026-05-20',
            'deskripsi' => 'Bed cover besar',
            'subtotal' => 16000,
            'total_harga' => 16000,
            'jumlah_bayar' => 16000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    public function test_tc_trx_21_tambah_transaksi_tanpa_mengisi_kolom_nama_pelanggan_deskripsi_layanan_unit_qty_berat_potongan_jumlah_bayar(): void
    {
        $transaksiCount = Transaksi::count();

        $response = $this->from(route('transaksi.index'))
            ->post(route('transaksi.store'), [
                'id_pelanggan' => '',
                'deskripsi' => '',
                'items' => [
                    ['id_layanan' => '', 'unit_satuan' => '', 'qty' => ''],
                ],
                'potongan' => '',
                'jumlah_bayar' => '',
            ]);

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHasErrors([
            'id_pelanggan',
            'items.0.id_layanan',
            'items.0.unit_satuan',
            'items.0.qty',
            'jumlah_bayar',
        ]);

        $this->assertSame($transaksiCount, Transaksi::count());
    }

    private function payload(array $override = []): array
    {
        return array_replace_recursive([
            'id_pelanggan' => $this->pelanggan->id,
            'deskripsi' => 'Laundry reguler',
            'items' => [
                $this->item($this->cuci, 'kg', 2),
            ],
            'potongan' => 0,
            'jumlah_bayar' => 20000,
        ], $override);
    }

    private function updatePayload(Transaksi $transaksi, array $override = []): array
    {
        return array_replace_recursive([
            'transaksi_id' => $transaksi->id,
            'id_pelanggan' => $transaksi->id_pelanggan,
            'tanggal_order' => $transaksi->tanggal_order,
            'deskripsi' => $transaksi->deskripsi,
            'potongan' => 0,
            'jumlah_bayar' => $transaksi->jumlah_bayar,
        ], $override);
    }

    private function item(Layanan $layanan, string $unit, float|int|string|null $qty): array
    {
        return [
            'id_layanan' => $layanan->id,
            'unit_satuan' => $unit,
            'qty' => $qty,
        ];
    }

    private function createLayanan(string $name, array $units): Layanan
    {
        $layanan = Layanan::create([
            'name' => $name,
            'gambar' => [],
            'deskripsi' => 'Layanan ' . $name,
        ]);

        foreach ($units as $unit => $harga) {
            LayananUnit::create([
                'layanan_id' => $layanan->id,
                'unit_satuan' => $unit,
                'harga' => $harga,
            ]);
        }

        return $layanan;
    }

    private function createTransaksi(array $override = []): Transaksi
    {
        $transaksi = Transaksi::create(array_merge([
            'no_invoice' => 'IJTEST' . uniqid(),
            'id_user' => $this->user->id,
            'created_by' => $this->user->username,
            'id_pelanggan' => $this->pelanggan->id,
            'id_layanan' => $this->cuci->id,
            'tanggal_order' => now()->toDateString(),
            'deskripsi' => 'Laundry reguler',
            'subtotal' => 20000,
            'potongan' => 0,
            'total_harga' => 20000,
            'jumlah_bayar' => 20000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ], $override));

        TransaksiItem::create([
            'transaksi_id' => $transaksi->id,
            'layanan_id' => $this->cuci->id,
            'unit_satuan' => 'kg',
            'qty' => 2,
            'harga_satuan' => 10000,
            'subtotal' => 20000,
        ]);

        return $transaksi;
    }

    private function createTransaksiWithItems(array $items): Transaksi
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $this->harga($item['id_layanan'], $item['unit_satuan']) * (float) $item['qty'];
        }

        $transaksi = $this->createTransaksi([
            'subtotal' => $subtotal,
            'total_harga' => $subtotal,
            'jumlah_bayar' => $subtotal,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
        $transaksi->items()->delete();

        foreach ($items as $item) {
            $harga = $this->harga($item['id_layanan'], $item['unit_satuan']);
            TransaksiItem::create([
                'transaksi_id' => $transaksi->id,
                'layanan_id' => $item['id_layanan'],
                'unit_satuan' => $item['unit_satuan'],
                'qty' => $item['qty'],
                'harga_satuan' => $harga,
                'subtotal' => $harga * (float) $item['qty'],
            ]);
        }

        return $transaksi;
    }

    private function harga(int $layananId, string $unit): float
    {
        return (float) LayananUnit::where('layanan_id', $layananId)
            ->where('unit_satuan', $unit)
            ->value('harga');
    }
}


