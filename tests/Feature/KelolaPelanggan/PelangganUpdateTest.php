<?php

namespace Tests\Feature\Pelanggan;

use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganUpdateTest extends TestCase
{
    use RefreshDatabase;

    // TC-CUST-09
    public function test_customer_cannot_be_updated_with_empty_name(): void
    {
        // Arrange
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $pelanggan = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08996755432',
        ]);

        $this->actingAs($user);

        // Act
        $response = $this->put(
            route('pelanggan.update', $pelanggan),
            [
                'name' => '',
                'kontak' => '08996755432',
            ]
        );

        // Assert
        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'name' => 'Raihan',
            'kontak' => '08996755432',
        ]);
    }

    // TC-CUST-10
    public function test_customer_can_be_updated(): void
    {
        // Arrange
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $pelanggan = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08996755432',
        ]);

        $this->actingAs($user);

        // Act
        $response = $this->put(
            route('pelanggan.update', $pelanggan),
            [
                'name' => 'Coba',
                'kontak' => '0851111111',
            ]
        );

        // Assert redirect
        $response->assertRedirect(route('pelanggan.index'));

        // Assert flash message
        $response->assertSessionHas('success');

        // Assert database updated
        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'name' => 'Coba',
            'kontak' => '0851111111',
        ]);
    }

    // TC-CUST-11
    public function test_customer_can_be_updated_without_changing_data(): void
    {
        // Arrange
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $pelanggan = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08996755432',
        ]);

        $this->actingAs($user);

        // Act
        $response = $this->put(
            route('pelanggan.update', $pelanggan),
            [
                'name' => 'Raihan',
                'kontak' => '08996755432',
            ]
        );

        // Assert
        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'name' => 'Raihan',
            'kontak' => '08996755432',
        ]);
    }

    // TC-CUST-12
    public function test_customer_can_be_updated_with_existing_name(): void
    {
        // Arrange
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        // Pelanggan pertama
        $pelanggan1 = Pelanggan::create([
            'name' => 'Faiz',
            'kontak' => '08111111111',
        ]);

        // Pelanggan kedua
        $pelanggan2 = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08222222222',
        ]);

        // Act
        $response = $this->put(
            route('pelanggan.update', $pelanggan2),
            [
                'name' => 'Faiz',
                'kontak' => '08222222222',
            ]
        );

        // Assert
        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan2->id,
            'name' => 'Faiz',
            'kontak' => '08222222222',
        ]);
    }

    // TC-CUST-13
    public function test_customer_cannot_be_updated_with_duplicate_contact(): void
    {
        // Arrange
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        // Pelanggan pertama
        $pelanggan1 = Pelanggan::create([
            'name' => 'Faiz',
            'kontak' => '08111111111',
        ]);

        // Pelanggan kedua
        $pelanggan2 = Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08222222222',
        ]);

        // Act
        $response = $this->put(
            route('pelanggan.update', $pelanggan2),
            [
                'name' => 'Raihan',
                'kontak' => '08111111111', // kontak milik pelanggan1
            ]
        );

        // Assert
        $response->assertSessionHasErrors('kontak');

        // Pastikan data lama tidak berubah
        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan2->id,
            'name' => 'Raihan',
            'kontak' => '08222222222',
        ]);
    }
}
