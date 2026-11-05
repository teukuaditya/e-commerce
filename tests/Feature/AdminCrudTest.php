<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * CRUD admin (modal berbasis index page): kategori & produk.
 * Update/destroy memakai _method spoofing seperti form di Blade.
 */
class AdminCrudTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@demo.com')->firstOrFail();
        $this->actingAs($this->admin);
    }

    public function test_semua_halaman_admin_200(): void
    {
        foreach ([
            '/admin/dashboard',
            '/admin/categories',
            '/admin/products',
            '/admin/users',
            '/admin/carts',
            '/admin/transactions',
            '/admin/transactions-details',
        ] as $u) {
            $this->get($u)->assertOk();
        }
    }

    public function test_crud_kategori_store_update_destroy(): void
    {
        $name = 'TESTKRUD '.uniqid();

        $this->post('/admin/categories', ['category_name' => $name])
            ->assertStatus(302);
        $cat = Category::where('category_name', $name)->firstOrFail();

        $this->put("/admin/categories/{$cat->id}", ['category_name' => $name.'-2'])
            ->assertStatus(302);
        $this->assertDatabaseHas('categories', ['id' => $cat->id, 'category_name' => $name.'-2']);

        $this->delete("/admin/categories/{$cat->id}")
            ->assertStatus(302);
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_store_produk_membutuhkan_kategori_existing(): void
    {
        $cat = Category::firstOrFail();

        $payload = [
            'category_id' => $cat->id,
            'brand' => 'TEST',
            'title' => 'TESTPRODUK',
            'description' => 'test refactor',
            'price' => 150000,
            'stock' => 10,
            'weight' => 100,
        ];

        $this->post('/admin/products', $payload)->assertStatus(302);
        $this->assertDatabaseHas('products', ['title' => 'TESTPRODUK']);

        $product = Product::where('title', 'TESTPRODUK')->firstOrFail();
        $this->delete("/admin/products/{$product->id}")->assertStatus(302);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
