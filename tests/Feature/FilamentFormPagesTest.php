<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentFormPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);
    }

    public function test_can_render_stock_request_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/stock-requests/create');

        $response->assertSuccessful();
        $response->assertSee('Requisition Slip (RIS) No.');
    }

    public function test_can_render_delivery_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/deliveries/create');

        $response->assertSuccessful();
        $response->assertSee('Delivery Receipt (DR) Number');
    }

    public function test_can_render_item_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/items/create');

        $response->assertSuccessful();
        $response->assertSee('Item Code');
    }

    public function test_can_render_supplier_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/suppliers/create');

        $response->assertSuccessful();
        $response->assertSee('Company / Supplier Name');
    }

    public function test_can_render_statement_of_consumed_goods_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/statement-of-consumed-goods');

        $response->assertSuccessful();
        $response->assertSee('Statement of Consumed Goods (StCG)');
    }
}
