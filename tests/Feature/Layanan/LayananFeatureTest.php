<?php

namespace Tests\Feature\Layanan;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LayananFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $imageDir = public_path('images/layanan');
        if (! is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $this->user = User::create([
            'name' => 'Admin User',
            'username' => 'admin_layanan_' . uniqid(),
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    /**
     * Test Case 1: Menambahkan data layanan dengan data valid
     * Expected: Sistem berhasil menyimpan data layanan dan menampilkan data pada tabel layanan
     */
    public function testCanStoreLayananWithValidData()
    {
        $validData = [
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa reguler dengan kualitas terjamin dan harga terjangkau',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 5000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('laundry1.jpg', 500, 500),
                UploadedFile::fake()->image('laundry2.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $validData);

        // Assert redirect ke halaman layanan
        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil ditambahkan.');

        $this->assertDatabaseHas('layanans', [
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa reguler dengan kualitas terjamin dan harga terjangkau',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'unit_satuan' => 'pcs',
            'harga' => 5000,
        ]);

        $layanan = Layanan::where('name', 'Cuci Jasa Reguler')->first();
        $this->assertNotNull($layanan);
        $this->assertCount(2, $layanan->units);
        $this->assertTrue($layanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($layanan->units->contains('unit_satuan', 'pcs'));

        // Assert gambar tersimpan
        $this->assertIsArray($layanan->gambar);
        $this->assertCount(2, $layanan->gambar);
    }

    /**
     * Test Case 2: Menambahkan data layanan dengan field kosong
     * Expected: Sistem menampilkan pesan validasi bahwa field wajib harus diisi
     */
    public function testCannotStoreLayananWithEmptyField()
    {
        // Test dengan name kosong
        $dataWithoutName = [
            'name' => '',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap minimal 5 karakter',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutName);

        $response->assertSessionHasErrors('name');
        $response->assertSessionHas('errors');

        // Test dengan deskripsi kosong
        $dataWithoutDeskripsi = [
            'name' => 'Layanan Valid',
            'deskripsi' => '',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutDeskripsi);

        $response->assertSessionHasErrors('deskripsi');

        // Test tanpa units (satuan dan harga)
        $dataWithoutUnits = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutUnits);

        $response->assertSessionHasErrors('units');

        // Test tanpa gambar
        $dataWithoutImage = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithoutImage);

        $response->assertSessionHasErrors('gambar');

        // Assert tidak ada data yang disimpan
        $this->assertDatabaseMissing('layanans', ['name' => 'Layanan Valid']);
    }

    /**
     * Test Case 3: Menambahkan data layanan dengan format harga tidak valid
     * Expected: Sistem hanya bisa menambahkan harga dengan angka
     */
    public function testCannotStoreLayananWithInvalidPrice()
    {
        // Test dengan harga tidak berupa angka
        $dataWithInvalidPrice = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 'tidak_angka',
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithInvalidPrice);

        $response->assertSessionHasErrors('units.0.harga');

        // Test dengan harga kosong
        $dataWithEmptyPrice = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => '',
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithEmptyPrice);

        $response->assertSessionHasErrors('units.0.harga');

        // Test dengan harga kurang dari 1
        $dataWithZeroPrice = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 0,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('test.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithZeroPrice);

        $response->assertSessionHasErrors('units.0.harga');

        // Assert tidak ada data yang disimpan
        $this->assertDatabaseMissing('layanans', ['name' => 'Layanan Valid']);
    }

    /**
     * Test Case 4: Mengupload gambar layanan dengan format file tidak valid
     * Expected: Sistem hanya menerima format gambar yang didukung
     */
    public function testCannotStoreLayananWithInvalidImageFormat()
    {
        // Test dengan file yang bukan gambar (pdf)
        $dataWithPdfFile = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('document.pdf', 512, 'application/pdf'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithPdfFile);

        $response->assertSessionHasErrors('gambar.0');

        // Test dengan file text
        $dataWithTextFile = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('document.txt', 512, 'text/plain'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithTextFile);

        $response->assertSessionHasErrors('gambar.0');

        // Test dengan ukuran gambar lebih dari 2MB
        $dataWithLargeImage = [
            'name' => 'Layanan Valid',
            'deskripsi' => 'Deskripsi layanan yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('large.jpg')->size(3000),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithLargeImage);

        $response->assertSessionHasErrors('gambar.0');

        // Assert tidak ada data yang disimpan
        $this->assertDatabaseMissing('layanans', ['name' => 'Layanan Valid']);
    }

    /**
     * Test Case 5: Menampilkan dan menutup form tambah layanan
     * Expected: Form tambah layanan berhasil ditampilkan dan ditutup
     */
    public function testCanDisplayAndCloseLayananForm()
    {
        // Test menampilkan halaman index (menampilkan form tambah)
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        // Verify halaman berhasil dimuat dengan method index
        $response->assertViewHasAll(['layanans', 'search', 'sort']);

        // Test menampilkan data yang sudah ada sebelumnya
        $existingLayanan = Layanan::create([
            'name' => 'Cuci Jasa Express',
            'deskripsi' => 'Layanan cuci jasa express dengan hasil cepat',
            'gambar' => ['image1.jpg', 'image2.jpg'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        // Verify data tertampil dalam view
        $layanans = $response->viewData('layanans');
        $this->assertTrue($layanans->contains($existingLayanan));

        // Test redirect kembali ke index setelah submit form (close form)
        $validData = [
            'name' => 'Layanan Setrika',
            'deskripsi' => 'Layanan setrika dengan hasil rapi dan profesional',
            'units' => [
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 8000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('setrika.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $validData);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Test Case 6: Menambahkan layanan dengan lebih dari satu unit layanan
     * Expected: Sistem berhasil menyimpan layanan dengan lebih dari satu unit
     */
    public function testCanStoreLayananWithMultipleUnits()
    {
        $dataWithMultipleUnits = [
            'name' => 'Cuci Kering Plus',
            'deskripsi' => 'Layanan cuci kering dengan pengharum premium dan hasil sempurna',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 15000,
                ],
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 7500,
                ],
                [
                    'unit_satuan' => 'meter',
                    'harga' => 12000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('cuci_kering.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.store'), $dataWithMultipleUnits);

        // Assert redirect dan success
        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil ditambahkan.');

        // Assert layanan tersimpan di database
        $this->assertDatabaseHas('layanans', [
            'name' => 'Cuci Kering Plus',
            'deskripsi' => 'Layanan cuci kering dengan pengharum premium dan hasil sempurna',
        ]);

        // Assert semua 3 units tersimpan
        $layanan = Layanan::where('name', 'Cuci Kering Plus')->first();
        $this->assertNotNull($layanan);
        $this->assertCount(3, $layanan->units);

        // Verify setiap unit tersimpan dengan benar
        $this->assertTrue($layanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($layanan->units->contains('unit_satuan', 'pcs'));
        $this->assertTrue($layanan->units->contains('unit_satuan', 'meter'));

        // Verify harga setiap unit
        $kgUnit = $layanan->units->where('unit_satuan', 'kg')->first();
        $pcsUnit = $layanan->units->where('unit_satuan', 'pcs')->first();
        $meterUnit = $layanan->units->where('unit_satuan', 'meter')->first();

        $this->assertEquals(15000, $kgUnit->harga);
        $this->assertEquals(7500, $pcsUnit->harga);
        $this->assertEquals(12000, $meterUnit->harga);
    }

    /**
     * Test Case 7: Membatalkan penambahan layanan setelah form diisi
     * Expected: Form tertutup dan data tidak tersimpan ke tabel layanan
     */
    public function testCanCancelAddingLayananForm()
    {
        $existingCount = Layanan::count();

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        // Verify: Form ditampilkan tanpa error
        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        // Verify: Data tidak tersimpan (count tetap sama)
        $finalCount = Layanan::count();
        $this->assertEquals($existingCount, $finalCount);

        $this->assertDatabaseMissing('layanans', [
            'name' => 'Layanan Yang Dibatalkan',
        ]);

        // Verify: Data persisten dan tidak berubah
        $response->assertViewHasAll(['layanans', 'search', 'sort']);
    }

    /**
     * Test Case 8: Mencari data layanan yang tersedia
     * Expected: Sistem menampilkan data layanan sesuai keyword pencarian
     */
    public function testCanSearchExistingLayanan()
    {
        // Setup: Create beberapa layanan untuk testing
        $layanan1 = Layanan::create([
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa standar',
            'gambar' => ['image1.jpg'],
        ]);

        $layanan2 = Layanan::create([
            'name' => 'Setrika Profesional',
            'deskripsi' => 'Layanan setrika dengan hasil sempurna',
            'gambar' => ['image2.jpg'],
        ]);

        $layanan3 = Layanan::create([
            'name' => 'Cuci Kering Express',
            'deskripsi' => 'Layanan cuci kering cepat',
            'gambar' => ['image3.jpg'],
        ]);

        // Test search dengan keyword 'Cuci'
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Cuci']));

        $response->assertStatus(200);

        $layanans = $response->viewData('layanans');
        $names = $layanans->pluck('name')->all();
        $this->assertContains($layanan1->name, $names);
        $this->assertContains($layanan3->name, $names);
        $this->assertNotContains($layanan2->name, $names);

        $response->assertSee($layanan1->name);
        $response->assertSee($layanan3->name);

        // Assert data yang tidak sesuai tidak ditampilkan
        $response->assertDontSee($layanan2->name);

        // Test search dengan keyword 'Setrika'
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Setrika']));

        $response->assertStatus(200);
        $response->assertSee($layanan2->name);
        $response->assertDontSee($layanan1->name);
        $response->assertDontSee($layanan3->name);

        // Test search dengan keyword 'Express'
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Express']));

        $response->assertStatus(200);
        $response->assertSee($layanan3->name);
        $response->assertDontSee($layanan1->name);
        $response->assertDontSee($layanan2->name);

        // Verify view memiliki data search
        $response->assertViewHas('search', 'Express');
    }

    /**
     * Test Case 9: Mencari data layanan yang tidak tersedia
     * Expected: Sistem menampilkan pesan bahwa data layanan tidak ditemukan
     */
    public function testCanSearchNonExistingLayanan()
    {
        // Setup: Create beberapa layanan
        Layanan::create([
            'name' => 'Cuci Jasa Reguler',
            'deskripsi' => 'Layanan cuci jasa standar',
            'gambar' => ['image1.jpg'],
        ]);

        Layanan::create([
            'name' => 'Setrika Profesional',
            'deskripsi' => 'Layanan setrika dengan hasil sempurna',
            'gambar' => ['image2.jpg'],
        ]);

        // Test search dengan keyword yang tidak ada
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'Jahit']));

        $response->assertStatus(200);

        $layanans = $response->viewData('layanans');
        $this->assertCount(0, $layanans);
        $this->assertEquals('Jahit', $response->viewData('search'));

        // Test search dengan keyword partial yang tidak cocok
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['search' => 'xyz']));

        $response->assertStatus(200);
        $layanans = $response->viewData('layanans');
        $this->assertCount(0, $layanans);
    }

    /**
     * Test Case 10: Mengurutkan data layanan
     * Expected: Sistem menampilkan data sesuai urutan yang dipilih
     */
    public function testCanSortLayananData()
    {
        $layanan1 = Layanan::create([
            'name' => 'Layanan A',
            'deskripsi' => 'Deskripsi layanan A',
            'gambar' => ['image1.jpg'],
        ]);
        $layanan1->forceFill([
            'created_at' => '2026-01-01 10:00:00',
            'updated_at' => '2026-01-01 10:00:00',
        ])->save();

        $layanan2 = Layanan::create([
            'name' => 'Layanan B',
            'deskripsi' => 'Deskripsi layanan B',
            'gambar' => ['image2.jpg'],
        ]);
        $layanan2->forceFill([
            'created_at' => '2026-01-01 10:01:00',
            'updated_at' => '2026-01-01 10:01:00',
        ])->save();

        $layanan3 = Layanan::create([
            'name' => 'Layanan C',
            'deskripsi' => 'Deskripsi layanan C',
            'gambar' => ['image3.jpg'],
        ]);
        $layanan3->forceFill([
            'created_at' => '2026-01-01 10:02:00',
            'updated_at' => '2026-01-01 10:02:00',
        ])->save();

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['sort' => 'updated_latest']));

        $response->assertStatus(200);
        $layanans = $response->viewData('layanans');

        // Verify sort value dalam view
        $response->assertViewHas('sort', 'updated_latest');

        // Get first item (should be latest)
        $firstItem = $layanans->first();
        $this->assertEquals('Layanan C', $firstItem->name);

        // Test sort: oldest first
        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', ['sort' => 'updated_oldest']));

        $response->assertStatus(200);
        $layanans = $response->viewData('layanans');

        // Verify sort value dalam view
        $response->assertViewHas('sort', 'updated_oldest');

        // Get first item (should be oldest)
        $firstItem = $layanans->first();
        $this->assertEquals('Layanan A', $firstItem->name);

        // Test kombinasi search + sort
        $layanan4 = Layanan::create([
            'name' => 'Cuci A',
            'deskripsi' => 'Deskripsi cuci',
            'gambar' => ['image4.jpg'],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index', [
                'search' => 'Layanan',
                'sort' => 'updated_oldest'
            ]));

        $response->assertStatus(200);

        // Verify both parameters passed to view
        $response->assertViewHas('search', 'Layanan');
        $response->assertViewHas('sort', 'updated_oldest');

        // Verify hasil search sesuai
        $response->assertSee('Layanan A');
        $response->assertSee('Layanan B');
        $response->assertSee('Layanan C');
        $response->assertDontSee('Cuci A'); // Not matching search
    }

    /**
     * Test Case 11: Mengupdate data layanan dengan data valid
     * Expected: Sistem berhasil memperbarui data layanan
     */
    public function testCanUpdateLayananWithValidData()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi awal cuci reguler',
            'gambar' => ['old_image.jpg'],
        ]);

        // Add initial units
        $layanan->units()->createMany([
            [
                'unit_satuan' => 'kg',
                'harga' => 10000,
            ],
            [
                'unit_satuan' => 'pcs',
                'harga' => 5000,
            ],
        ]);

        // Prepare updated data
        $updatedData = [
            'name' => 'Cuci Jasa Premium',
            'deskripsi' => 'Layanan cuci jasa premium dengan kualitas terbaik dan hasil maksimal',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 15000,
                ],
                [
                    'unit_satuan' => 'meter',
                    'harga' => 8000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('new_image1.jpg', 500, 500),
                UploadedFile::fake()->image('new_image2.jpg', 500, 500),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($updatedData, ['_method' => 'PUT']));

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil diperbarui.');

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa Premium',
            'deskripsi' => 'Layanan cuci jasa premium dengan kualitas terbaik dan hasil maksimal',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 15000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'meter',
            'harga' => 8000,
        ]);

        $this->assertDatabaseMissing('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'pcs',
        ]);

        $updatedLayanan = Layanan::find($layanan->id);
        $this->assertEquals('Cuci Jasa Premium', $updatedLayanan->name);
        $this->assertCount(2, $updatedLayanan->units);
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'meter'));
    }

    /**
     * Test Case 12: Mengupdate data layanan dengan field wajib kosong
     * Expected: Sistem menampilkan pesan validasi field wajib
     */
    public function testCannotUpdateLayananWithEmptyField()
    {
        // Create initial layanan
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        // Test update dengan name kosong
        $dataWithoutName = [
            'name' => '',
            'deskripsi' => 'Deskripsi yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutName);

        $response->assertSessionHasErrors('name');
        $response->assertRedirect();

        // Test update dengan deskripsi kosong
        $dataWithoutDeskripsi = [
            'name' => 'Cuci Jasa',
            'deskripsi' => '',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutDeskripsi);

        $response->assertSessionHasErrors('deskripsi');
        $response->assertRedirect();

        // Test update dengan units kosong
        $dataWithoutUnits = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid dan lengkap',
            'units' => [],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutUnits);

        $response->assertSessionHasErrors('units');
        $response->assertRedirect();

        $dataWithoutGambar = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid dan lengkap',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'images_to_delete' => ['image.jpg'],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithoutGambar);

        $response->assertSessionHasErrors('gambar');
        $response->assertRedirect();

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
        ]);

        $this->assertDatabaseMissing('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa',
        ]);
    }

    /**
     * Test Case 13: Mengupdate data layanan dengan format harga tidak valid
     * Expected: Sistem menampilkan validasi bahwa harga harus angka
     */
    public function testCannotUpdateLayananWithInvalidPrice()
    {
        // Create initial layanan
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        // Test update dengan harga bukan angka
        $dataWithNonNumericPrice = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 'bukan angka',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithNonNumericPrice);

        $response->assertSessionHasErrors('units.0.harga');
        $response->assertRedirect();

        // Test update dengan harga 0
        $dataWithZeroPrice = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithZeroPrice);

        $response->assertSessionHasErrors('units.0.harga');
        $response->assertRedirect();

        // Test update dengan harga negatif
        $dataWithNegativePrice = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => -5000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $dataWithNegativePrice);

        $response->assertSessionHasErrors('units.0.harga');
        $response->assertRedirect();

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertDatabaseMissing('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa',
        ]);
    }

    /**
     * Test Case 14: Mengupdate gambar layanan dengan format file tidak valid
     * Expected: Sistem menampilkan validasi format file tidak didukung
     */
    public function testCannotUpdateLayananWithInvalidImageFormat()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        // Test update dengan format PDF
        $dataWithPdfFile = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($dataWithPdfFile, ['_method' => 'PUT']));

        $response->assertSessionHasErrors('gambar.0');
        $response->assertRedirect();

        $dataWithTxtFile = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->create('text.txt', 100, 'text/plain'),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($dataWithTxtFile, ['_method' => 'PUT']));

        $response->assertSessionHasErrors('gambar.0');
        $response->assertRedirect();

        $dataWithLargeFile = [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 10000,
                ],
            ],
            'gambar' => [
                UploadedFile::fake()->image('large.jpg')->size(3000), // 3000 KB = ~3MB
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('layanan.update', $layanan->id), array_merge($dataWithLargeFile, ['_method' => 'PUT']));

        $response->assertSessionHasErrors('gambar.0');
        $response->assertRedirect();

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
        ]);
    }

    /**
     * Test Case 15: Mengupdate data layanan dengan menambahkan unit layanan
     * Expected: Sistem berhasil menyimpan layanan dengan lebih dari satu unit
     */
    public function testCanUpdateLayananWithAddingMoreUnits()
    {
        // Create initial layanan dengan 1 unit
        $layanan = Layanan::create([
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Layanan cuci jasa standar',
            'gambar' => ['image.jpg'],
        ]);

        // Add 1 initial unit
        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertCount(1, $layanan->fresh()->units);

        $updatedData = [
            'name' => 'Cuci Jasa Lengkap',
            'deskripsi' => 'Layanan cuci jasa dengan banyak pilihan unit',
            'units' => [
                [
                    'unit_satuan' => 'kg',
                    'harga' => 12000,
                ],
                [
                    'unit_satuan' => 'pcs',
                    'harga' => 6000,
                ],
                [
                    'unit_satuan' => 'meter',
                    'harga' => 8000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('layanan.update', $layanan->id), $updatedData);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success', 'Layanan berhasil diperbarui.');

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Jasa Lengkap',
            'deskripsi' => 'Layanan cuci jasa dengan banyak pilihan unit',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 12000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'pcs',
            'harga' => 6000,
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'meter',
            'harga' => 8000,
        ]);

        $updatedLayanan = Layanan::find($layanan->id);
        $this->assertCount(3, $updatedLayanan->units);
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'kg'));
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'pcs'));
        $this->assertTrue($updatedLayanan->units->contains('unit_satuan', 'meter'));

        $kgUnit = $updatedLayanan->units()->where('unit_satuan', 'kg')->first();
        $pcsUnit = $updatedLayanan->units()->where('unit_satuan', 'pcs')->first();
        $meterUnit = $updatedLayanan->units()->where('unit_satuan', 'meter')->first();

        $this->assertEquals(12000, $kgUnit->harga);
        $this->assertEquals(6000, $pcsUnit->harga);
        $this->assertEquals(8000, $meterUnit->harga);
        $this->assertEquals('Cuci Jasa Lengkap', $updatedLayanan->name);
        $this->assertEquals('Layanan cuci jasa dengan banyak pilihan unit', $updatedLayanan->deskripsi);
    }

    /**
     * Test Case 16: Membatalkan perubahan saat update data layanan
     * Expected: Form edit tertutup dan data lama tetap tersimpan tanpa perubahan
     */
    public function testCanCancelUpdatingLayananForm()
    {
        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan awal yang tidak berubah',
            'gambar' => ['image.jpg'],
        ]);

        $layanan->units()->create([
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('layanan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layanan');

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan awal yang tidak berubah',
        ]);

        $this->assertDatabaseMissing('layanans', [
            'name' => 'Cuci Premium Dibatalkan',
        ]);

        $this->assertDatabaseHas('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'kg',
            'harga' => 10000,
        ]);

        $this->assertDatabaseMissing('layanan_units', [
            'layanan_id' => $layanan->id,
            'unit_satuan' => 'pcs',
        ]);
    }

    /**
     * Test Case 17: Mengakses halaman layanan tanpa login
     * Expected: Sistem mengarahkan user ke halaman login
     */
    public function testGuestCannotAccessLayananPage()
    {
        $response = $this->get(route('layanan.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('layanan.store'), []);
        $response->assertRedirect(route('login'));

        $layanan = Layanan::create([
            'name' => 'Cuci Reguler',
            'deskripsi' => 'Deskripsi layanan',
            'gambar' => ['image.jpg'],
        ]);

        $response = $this->put(route('layanan.update', $layanan->id), [
            'name' => 'Cuci Jasa',
            'deskripsi' => 'Deskripsi yang valid',
            'units' => [
                ['unit_satuan' => 'kg', 'harga' => 10000],
            ],
        ]);
        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('layanans', [
            'id' => $layanan->id,
            'name' => 'Cuci Reguler',
        ]);
    }
}
