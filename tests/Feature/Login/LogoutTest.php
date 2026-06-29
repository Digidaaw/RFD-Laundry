<?php

namespace Tests\Feature\Login;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    // TC-LOG-08
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'Administrator',
            'role' => 'admin',
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $this->assertGuest();

        $response->assertRedirect('/login');
    }
}

