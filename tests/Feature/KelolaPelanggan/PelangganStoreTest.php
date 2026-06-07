<?php

namespace Tests\Feature\Pelanggan;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pelanggan;

class PelangganStoreTest extends TestCase
{
    use RefreshDatabase;

    // TC-CUST-01
    public function test_customer_cannot_be_created_with_empty_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => '',
            'kontak' => '',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'kontak',
        ]);
    }

    // TC-CUST-02
    public function test_customer_cannot_be_created_with_non_numeric_contact(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Test',
            'kontak' => 'abc',
        ]);

        $response->assertSessionHasErrors('kontak');

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'Test',
        ]);
    }

    // TC-CUST-03
    public function test_customer_cannot_be_created_with_contact_less_than_10_digits(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Test',
            'kontak' => '08',
        ]);

        $response->assertSessionHasErrors('kontak');

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'Test',
            'kontak' => '08',
        ]);
    }

    // TC-CUST-04
    public function test_customer_can_be_created_with_valid_data(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Test',
            'kontak' => '08111111111',
        ]);

        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'name' => 'Test',
            'kontak' => '08111111111',
        ]);
    }

    // TC-CUST-05
    public function test_customer_cannot_be_created_with_contact_more_than_13_digits(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Testing',
            'kontak' => '12345678901234567890',
        ]);

        $response->assertSessionHasErrors('kontak');

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'Testing',
        ]);
    }

    // TC-CUST-06
    public function test_customer_cannot_be_created_with_contact_containing_spaces(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Testing',
            'kontak' => '08123 456789',
        ]);

        $response->assertSessionHasErrors('kontak');

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'Testing',
        ]);
    }

    // TC-CUST-07
    public function test_customer_cannot_be_created_with_contact_containing_symbols(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Testing',
            'kontak' => '08123-456789',
        ]);

        $response->assertSessionHasErrors('kontak');

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'Testing',
        ]);
    }

    // TC-CUST-08
    public function test_customer_cannot_be_created_with_duplicate_contact(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08996755432',
        ]);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Faiz',
            'kontak' => '08996755432',
        ]);

        $response->assertSessionHasErrors('kontak');

        $this->assertDatabaseCount('pelanggans', 1);
    }
}
