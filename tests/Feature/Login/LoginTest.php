<?php

namespace Tests\Feature\Login;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'name' => 'Administrator',
            'role' => 'admin',
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response = $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('dashboard'));
    }
}


