<?php

namespace Tests\Feature\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    // TC-LOG-09
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}