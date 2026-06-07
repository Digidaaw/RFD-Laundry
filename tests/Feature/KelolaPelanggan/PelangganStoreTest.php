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

        $response->assertSessionHasErrorsIn(
            'store',
            [
                'name',
                'kontak',
            ]
        );
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
            'name' => 'David',
            'kontak' => 'abc',
        ]);

        $response->assertSessionHasErrorsIn('store', ['kontak']);

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'David',
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
            'name' => 'David',
            'kontak' => '08',
        ]);

        $response->assertSessionHasErrorsIn('store', ['kontak']);

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'David',
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
            'name' => 'David',
            'kontak' => '08111111111',
        ]);

        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'name' => 'David',
            'kontak' => '08111111111',
        ]);
    }

    // TC-CUST-05
    public function test_customer_can_be_created_with_same_name_but_different_contact(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        Pelanggan::create([
            'name' => 'David',
            'kontak' => '08111111111',
        ]);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'David',
            'kontak' => '08222222222',
        ]);

        $response->assertRedirect(route('pelanggan.index'));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggans', [
            'name' => 'David',
            'kontak' => '08222222222',
        ]);
    }


    // TC-CUST-06
    public function test_customer_cannot_be_created_with_contact_more_than_13_digits(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'David',
            'kontak' => '12345678901234567890',
        ]);

        $response->assertSessionHasErrorsIn('store', ['kontak']);

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'David',
            'kontak' => '12345678901234567890',
        ]);
    }

    // TC-CUST-07
    public function test_customer_cannot_be_created_with_contact_containing_spaces(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'David',
            'kontak' => '08123 456789',
        ]);

        $response->assertSessionHasErrorsIn('store', ['kontak']);

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'David',
            'kontak' => '08123 456789',
        ]);
    }

    // TC-CUST-08
    public function test_customer_cannot_be_created_with_contact_containing_symbols(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'David',
            'kontak' => '08123-456789',
        ]);

        $response->assertSessionHasErrorsIn('store', ['kontak']);

        $this->assertDatabaseMissing('pelanggans', [
            'name' => 'David',
            'kontak' => '08123-456789',
        ]);
    }

    // TC-CUST-09
    public function test_customer_cannot_be_created_with_duplicate_contact(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        Pelanggan::create([
            'name' => 'Faizah',
            'kontak' => '08111111111',
        ]);

        $response = $this->post(route('pelanggan.store'), [
            'name' => 'Faizah',
            'kontak' => '08111111111',
        ]);

        $response->assertSessionHasErrorsIn('store', ['kontak']);

        $this->assertDatabaseCount('pelanggans', 1);
    }
}
