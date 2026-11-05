<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_dashboard_renders_with_filament_styles(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('filament');
        $response->assertSee('RMPH Consignment System');
        $response->assertSee('Dashboard');
    }
}
