<?php

namespace Tests\Feature\Pelanggan;

use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganUpdateTest extends TestCase
{
    use RefreshDatabase;

    // TC-CUST-10
    public function test_customer_cannot_be_updated_with_empty_name(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $pelanggan = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08996755432',
        ]);

        $this->actingAs($user);

        $response = $this->put(
            route('pelanggan.update', $pelanggan),
            [
                'name' => '',
                'kontak' => '08996755432',
            ]
        );

        $response->assertSessionHasErrorsIn('update', ['name']);

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'name' => 'David',
            'kontak' => '08996755432',
        ]);
    }

    // TC-CUST-11
    public function test_customer_can_be_updated(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $pelanggan = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08996755432',
        ]);

        $this->actingAs($user);

        $response = $this->put(
            route('pelanggan.update', $pelanggan),
            [
                'name' => 'Faizah',
                'kontak' => '0851111111',
            ]
        );

        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'name' => 'Faizah',
            'kontak' => '0851111111',
        ]);
    }

    // TC-CUST-12
    public function test_customer_can_be_updated_without_changing_data(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $pelanggan = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08996755432',
        ]);

        $this->actingAs($user);

        $response = $this->put(
            route('pelanggan.update', $pelanggan),
            [
                'name' => 'David',
                'kontak' => '08996755432',
            ]
        );

        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'name' => 'David',
            'kontak' => '08996755432',
        ]);
    }

    // TC-CUST-13
    public function test_customer_can_be_updated_with_existing_name(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $pelanggan1 = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08111111111',
        ]);

        $pelanggan2 = Pelanggan::create([
            'name' => 'Faizah',
            'kontak' => '08222222222',
        ]);

        $response = $this->put(
            route('pelanggan.update', $pelanggan2),
            [
                'name' => 'David',
                'kontak' => '08222222222',
            ]
        );

        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan2->id,
            'name' => 'David',
            'kontak' => '08222222222',
        ]);
    }

    // TC-CUST-14
    public function test_customer_cannot_be_updated_with_duplicate_contact(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $pelanggan1 = Pelanggan::create([
            'name' => 'David',
            'kontak' => '08111111111',
        ]);

        $pelanggan2 = Pelanggan::create([
            'name' => 'Faizah',
            'kontak' => '08222222222',
        ]);

        $response = $this->put(
            route('pelanggan.update', $pelanggan2),
            [
                'name' => 'Faizah',
                'kontak' => '08111111111', 
            ]
        );

        $response->assertSessionHasErrorsIn('update', ['kontak']);

        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan2->id,
            'name' => 'Faizah',
            'kontak' => '08222222222',
        ]);
    }
}
