<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageCashierTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_cashier_status()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'password' => 'password123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $cashier = User::create([
            'name' => 'Cashier Test',
            'username' => 'cashier_test',
            'password' => 'password123',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // Deactivate cashier
        $response = $this->patch(route('users.toggle-status', $cashier->id));
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $cashier->id,
            'is_active' => false,
        ]);

        // Activate cashier again
        $response = $this->patch(route('users.toggle-status', $cashier->id));
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $cashier->id,
            'is_active' => true,
        ]);
    }

    public function test_cashier_cannot_toggle_status()
    {
        $cashier1 = User::create([
            'name' => 'Cashier 1',
            'username' => 'cashier1',
            'password' => 'password123',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $cashier2 = User::create([
            'name' => 'Cashier 2',
            'username' => 'cashier2',
            'password' => 'password123',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $this->actingAs($cashier1);

        $response = $this->patch(route('users.toggle-status', $cashier2->id));
        $response->assertStatus(403);
    }

    public function test_inactive_cashier_cannot_login()
    {
        User::create([
            'name' => 'Inactive Cashier',
            'username' => 'inactive_cashier',
            'password' => 'password123',
            'role' => 'kasir',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'username' => 'inactive_cashier',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertFalse(\Auth::check());
    }

    public function test_active_cashier_can_login()
    {
        User::create([
            'name' => 'Active Cashier',
            'username' => 'active_cashier',
            'password' => 'password123',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'username' => 'active_cashier',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(\Auth::check());
    }

    public function test_deactivated_cashier_is_logged_out_on_next_request()
    {
        $cashier = User::create([
            'name' => 'Cashier',
            'username' => 'cashier',
            'password' => 'password123',
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $this->actingAs($cashier);

        // Deactivate them in the DB
        $cashier->update(['is_active' => false]);

        // Attempt a request (e.g. dashboard)
        $response = $this->get(route('dashboard'));

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertFalse(\Auth::check());
    }
}


