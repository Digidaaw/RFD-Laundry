<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganSearchTest extends TestCase
{
    use RefreshDatabase;

    // TC-CUST-15
    public function test_customer_can_be_searched_using_lowercase_keyword(): void
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

        Pelanggan::create([
            'name' => 'Faizah',
            'kontak' => '08222222222',
        ]);

        $response = $this->get(
            route('pelanggan.index', [
                'search' => 'david'
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('David');

        $response->assertDontSee('Faizah');
    }

    // TC-CUST-16
    public function test_customer_can_be_searched_using_uppercase_keyword(): void
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

        Pelanggan::create([
            'name' => 'Faizah',
            'kontak' => '08222222222',
        ]);

        $response = $this->get(
            route('pelanggan.index', [
                'search' => 'DAVID'
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('David');

        $response->assertDontSee('Faizah');
    }

    // TC-CUST-19
    public function test_customer_can_be_searched_using_partial_name(): void
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

        Pelanggan::create([
            'name' => 'Faizah',
            'kontak' => '08222222222',
        ]);

        $response = $this->get(
            route('pelanggan.index', [
                'search' => 'dav'
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('David');

        $response->assertDontSee('Faizah');
    }
}


