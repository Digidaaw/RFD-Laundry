<?php

namespace Tests\Unit;

use App\Http\Controllers\TransaksiController;
use App\Http\Requests\TransaksiStoreRequest;
use App\Models\Layanan;
use App\Models\LayananUnit;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pelanggan $pelanggan;
    private Layanan $cuci;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Admin Test Unit',
            'role' => 'admin',
            'username' => 'admin_unit_' . uniqid(),
            'password' => bcrypt('password'),
        ]);

        $this->pelanggan = Pelanggan::create([
            'name' => 'Unit Pelanggan',
            'kontak' => '0899' . random_int(10000000, 99999999),
        ]);

        $this->cuci = Layanan::create([
            'name' => 'Cuci Unit',
            'gambar' => [],
            'deskripsi' => 'Layanan Unit',
        ]);

        LayananUnit::create([
            'layanan_id' => $this->cuci->id,
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->actingAs($this->user);
    }

    private function createRequest(array $data): TransaksiStoreRequest
    {
        $request = TransaksiStoreRequest::create(route('transaksi.store'), 'POST', $data);
        $request->setContainer($this->app);
        
        // Bind redirector
        $request->setRedirector($this->app->make(\Illuminate\Routing\Redirector::class));
        
        // Bind session to prevent redirect/back helper failures
        $session = $this->app['session']->driver('array');
        $request->setLaravelSession($session);
        
        return $request;
    }

    /**
     * Test direct controller call: Valid & Lunas
     */
    public function test_controller_store_valid_dan_lunas(): void
    {
        $request = $this->createRequest([
            'id_pelanggan' => $this->pelanggan->id,
            'deskripsi' => 'Test Unit Lunas',
            'items' => [
                ['id_layanan' => $this->cuci->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 20000,
        ]);

        $request->validateResolved();

        $controller = new TransaksiController();
        $response = $controller->store($request);

        // Assert redirect response returned by controller
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);

        // Assert database records
        $this->assertDatabaseHas('transaksis', [
            'id_pelanggan' => $this->pelanggan->id,
            'subtotal' => 20000,
            'potongan' => 0,
            'total_harga' => 20000,
            'jumlah_bayar' => 20000,
            'sisa_bayar' => 0,
            'status_pembayaran' => 'Lunas',
        ]);
    }

    /**
     * Test direct controller call: Valid & DP
     */
    public function test_controller_store_valid_dan_dp(): void
    {
        $request = $this->createRequest([
            'id_pelanggan' => $this->pelanggan->id,
            'deskripsi' => 'Test Unit DP',
            'items' => [
                ['id_layanan' => $this->cuci->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 12000,
        ]);

        $request->validateResolved();

        $controller = new TransaksiController();
        $response = $controller->store($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);

        $this->assertDatabaseHas('transaksis', [
            'id_pelanggan' => $this->pelanggan->id,
            'subtotal' => 20000,
            'total_harga' => 20000,
            'jumlah_bayar' => 12000,
            'sisa_bayar' => 8000,
            'status_pembayaran' => 'DP',
        ]);
    }

    /**
     * Test direct validation: Pelanggan kosong
     */
    public function test_validation_fails_when_pelanggan_kosong(): void
    {
        $request = $this->createRequest([
            'id_pelanggan' => '',
            'deskripsi' => 'Test Unit Error',
            'items' => [
                ['id_layanan' => $this->cuci->id, 'unit_satuan' => 'kg', 'qty' => 2],
            ],
            'potongan' => 0,
            'jumlah_bayar' => 20000,
        ]);

        $this->expectException(ValidationException::class);

        $request->validateResolved();
    }
}
