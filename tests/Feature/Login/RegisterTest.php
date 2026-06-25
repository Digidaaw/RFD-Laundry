<?php

namespace Tests\Feature\Login;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // TC-LOG-10
    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'admin',
            'username' => 'admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'kasir',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'name' => 'admin',
            'username' => 'admin',
            'role' => 'kasir',
        ]);
    }
}