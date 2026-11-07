<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminTelemetryAndSearchTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@demo.com')->firstOrFail();
        $this->actingAs($this->admin);
    }

    public function test_admin_dashboard_telemetry_endpoint_returns_json(): void
    {
        $response = $this->get('/admin/dashboard/telemetry');
        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'data' => [
                'earningsThisMonth',
                'earningsThisYear',
                'pendingRequests',
                'monthlyTarget',
                'earningsPercentage',
                'weeklyTelemetry',
                'monthlyTelemetry',
                'totalItems',
                'categoryList',
                'activityFeed',
                'notifications',
            ],
            'timestamp',
        ]);
    }

    public function test_admin_quick_search_finds_products_orders_users(): void
    {
        // Short query returns empty
        $response = $this->get('/admin/quick-search?q=a');
        $response->assertOk();
        $response->assertJson([
            'products' => [],
            'transactions' => [],
            'users' => [],
        ]);

        // Real query
        $response = $this->get('/admin/quick-search?q=demo');
        $response->assertOk();
        $data = $response->json();
        $this->assertIsArray($data['users']);
    }

    public function test_admin_export_downloads_csv(): void
    {
        $response = $this->get('/admin/dashboard/export');
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_login_displays_error_message_on_invalid_credentials(): void
    {
        // Must be unauthenticated guest to test login
        auth()->logout();

        $this->post('/login', [
            'email' => 'wrong@email.com',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('email');

        $view = $this->withSession(['errors' => session('errors')])->get('/login');
        $view->assertOk();
    }

    public function test_register_displays_error_message_on_validation_failure(): void
    {
        // Must be unauthenticated guest to test register
        auth()->logout();

        $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $page = $this->withSession(['errors' => session('errors')])->get('/register');
        $page->assertOk();
    }
}
