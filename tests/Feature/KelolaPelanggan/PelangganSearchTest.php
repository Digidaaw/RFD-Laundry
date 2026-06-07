<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganSearchTest extends TestCase
{
    use RefreshDatabase;

    // TC-CUST-14
    public function test_customer_can_be_searched_using_lowercase_keyword(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        Pelanggan::create([
            'name' => 'Faiz',
            'kontak' => '08111111111',
        ]);

        Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08222222222',
        ]);

        $response = $this->get(
            route('pelanggan.index', [
                'search' => 'faiz'
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('Faiz');

        $response->assertDontSee('Raihan');
    }

    // TC-CUST-15
    public function test_customer_can_be_searched_using_uppercase_keyword(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        Pelanggan::create([
            'name' => 'Faiz',
            'kontak' => '08111111111',
        ]);

        Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08222222222',
        ]);

        $response = $this->get(
            route('pelanggan.index', [
                'search' => 'FAIZ'
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('Faiz');

        $response->assertDontSee('Raihan');
    }

    // TC-CUST-18
    public function test_customer_can_be_searched_using_partial_name(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        Pelanggan::create([
            'name' => 'Raihan',
            'kontak' => '08111111111',
        ]);

        Pelanggan::create([
            'name' => 'Faiz',
            'kontak' => '08222222222',
        ]);

        $response = $this->get(
            route('pelanggan.index', [
                'search' => 'rai'
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('Raihan');

        $response->assertDontSee('Faiz');
    }
}
