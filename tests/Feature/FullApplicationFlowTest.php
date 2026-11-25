<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Comprehensive single-file automation test covering major features and flows.
 * Run: php artisan test --filter=FullApplicationFlowTest
 */
class FullApplicationFlowTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@demo.com')->firstOrFail();
        $this->user = User::where('email', 'user@demo.com')->firstOrFail();
        Cache::flush();
    }

    // ==================== AUTH / ACCESS ====================

    public function test_guest_can_view_public_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get('/products')->assertOk();
        $this->get('/categories')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/user/profile/edit')->assertRedirect('/login');
        $this->get('/user/orders/order-history')->assertRedirect('/login');
        $this->get('/user/checkout')->assertRedirect('/login');
    }

    public function test_user_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->user)->get('/admin/dashboard')->assertRedirect('/');
        $this->actingAs($this->user)->get('/admin/products')->assertRedirect('/');
        $this->actingAs($this->user)->get('/admin/settings')->assertRedirect('/');
    }

    public function test_register_login_logout_flow(): void
    {
        $email = 'testflow_' . uniqid() . '@mail.test';
        $this->post('/register', [
            'name' => 'Flow User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'user']);

        $this->post('/login', ['email' => $email, 'password' => 'password123'])->assertRedirect();
        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/');
    }

    // ==================== SETTINGS ====================

    public function test_admin_can_view_and_update_settings(): void
    {
        $this->actingAs($this->admin)->get('/admin/settings')->assertOk();

        $payload = [
            'store_name' => 'DRVN Test',
            'store_tagline' => 'test tagline',
            'store_email' => 'store@test.com',
            'store_phone' => '08123456789',
            'store_whatsapp' => '628123456789',
            'store_address' => 'Jl. Test No. 1',
            'operating_hours' => 'Senin-Jumat 09:00-17:00',
            'about_text' => 'Tentang toko test',
            'footer_text' => 'Footer test',
            'social_instagram' => 'https://instagram.com/test',
            'social_facebook' => 'https://facebook.com/test',
            'social_tiktok' => 'https://tiktok.com/@test',
            'currency_symbol' => 'Rp',
            'monthly_revenue_goal' => 75000000,
            'min_order_amount' => 10000,
            'default_shipping_fallback' => 15000,
            'order_id_prefix' => 'TEST',
            'show_out_of_stock' => '1',
        ];

        $this->actingAs($this->admin)
            ->put('/admin/settings', $payload)
            ->assertRedirect(route('admin.settings.index'));

        $this->assertDatabaseHas('settings', ['key' => 'store_name', 'value' => 'DRVN Test']);
        $this->assertDatabaseHas('settings', ['key' => 'monthly_revenue_goal', 'value' => '75000000']);
        $this->assertSame('DRVN Test', Setting::getValue('store_name'));
    }

    public function test_settings_reflect_on_about_and_footer(): void
    {
        Setting::setValue('store_email', 'about@mail.test', 'general', 'text');
        Setting::setValue('store_address', 'Alamat Baru Test', 'general', 'textarea');
        Cache::forget('shares.company');

        $resp = $this->get('/about')->assertOk();
        $resp->assertSee('about@mail.test');
        $resp->assertSee('Alamat Baru Test');

        $home = $this->get('/')->assertOk();
        $home->assertSee('about@mail.test');
    }

    // ==================== ADMIN CRUD ====================

    public function test_admin_can_crud_category(): void
    {
        $this->actingAs($this->admin);
        $name = 'CAT-' . uniqid();

        $this->post('/admin/categories', ['category_name' => $name, 'description' => 'desc'])->assertRedirect();
        $cat = Category::where('category_name', $name)->firstOrFail();

        $this->put("/admin/categories/{$cat->id}", ['category_name' => $name . '-UPD', 'description' => 'upd'])->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $cat->id, 'category_name' => $name . '-UPD']);

        $this->delete("/admin/categories/{$cat->id}")->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_admin_can_crud_product(): void
    {
        $this->actingAs($this->admin);
        $cat = Category::firstOrFail();

        $payload = [
            'category_id' => $cat->id,
            'brand' => 'TestBrand',
            'title' => 'Test Product ' . uniqid(),
            'description' => 'Desc',
            'price' => 100000,
            'stock' => 10,
            'weight' => 500,
            'size' => ['M', 'L'],
        ];

        $this->post('/admin/products', $payload)->assertRedirect();
        $product = Product::where('title', $payload['title'])->firstOrFail();

        $payload['title'] = $payload['title'] . ' UPD';
        $payload['stock'] = 5;
        $this->put("/admin/products/{$product->id}", $payload)->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);

        $this->delete("/admin/products/{$product->id}")->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_pages_load(): void
    {
        $this->actingAs($this->admin);
        foreach (['/admin/dashboard', '/admin/categories', '/admin/products', '/admin/users', '/admin/carts', '/admin/transactions', '/admin/transactions-details', '/admin/settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    // ==================== USER PROFILE / ADDRESS ====================

    public function test_user_can_update_profile(): void
    {
        $this->actingAs($this->user);
        $this->get('/user/profile/edit')->assertOk();
        $this->put('/user/profile/update', [
            'name' => 'Updated Name',
            'email' => $this->user->email,
            'phone' => '0811111111',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Updated Name']);
    }

    public function test_user_can_save_and_delete_address(): void
    {
        $this->actingAs($this->user);
        $this->get('/user/address/edit')->assertOk();
        $this->post('/user/address/update', [
            'recipient_name' => 'Tester',
            'recipient_phone' => '08123456789',
            'address' => 'Jl. Test 123',
            'destination_id' => '25986',
            'destination_name' => 'DEPOK, PANCORAN MAS, DEPOK, JAWA BARAT, 16431',
        ])->assertRedirect();

        $this->user->refresh();
        $this->assertNotEmpty($this->user->address);

        $this->delete('/user/address/delete')->assertRedirect();
        $this->user->refresh();
        $this->assertEmpty($this->user->address);
    }

    // ==================== WISHLIST ====================

    public function test_user_can_toggle_wishlist(): void
    {
        $product = Product::firstOrFail();
        $this->actingAs($this->user);

        $this->postJson(route('user.wishlist.toggle', $product->id))->assertOk();
        $this->assertDatabaseHas('wishlists', ['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->postJson(route('user.wishlist.toggle', $product->id))->assertOk();
        $this->assertDatabaseMissing('wishlists', ['user_id' => $this->user->id, 'product_id' => $product->id]);
    }

    public function test_wishlist_page_loads(): void
    {
        $this->actingAs($this->user)->get('/user/wishlist')->assertOk();
    }

    // ==================== CART ====================

    public function test_user_can_add_update_remove_cart_item(): void
    {
        $product = Product::where('stock', '>', 0)->firstOrFail();
        $this->actingAs($this->user);

        $this->post(route('user.cart.add', $product->id), ['quantity' => 1])->assertRedirect();
        $cart = Cart::where('user_id', $this->user->id)->where('product_id', $product->id)->first();
        $this->assertNotNull($cart);

        $this->patch(route('user.cart.update', $cart->id), ['quantity' => 2])->assertStatus(200);
        $this->assertDatabaseHas('carts', ['id' => $cart->id, 'quantity' => 2]);

        $this->delete(route('user.cart.destroy', $cart->id))->assertRedirect();
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    // ==================== PRODUCT / SEARCH ====================

    public function test_product_detail_and_search_work(): void
    {
        $product = Product::firstOrFail();
        $this->get(route('user.products.show', $product->id))->assertOk();

        $this->get('/products?q=' . urlencode(substr($product->title, 0, 3)))->assertOk();
        $ajax = $this->getJson('/products?q=' . urlencode(substr($product->title, 0, 3)), ['X-Requested-With' => 'XMLHttpRequest']);
        $ajax->assertOk()->assertJsonStructure(['count', 'query', 'html', 'pagination']);
    }

    public function test_admin_quick_search_returns_json(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/admin/quick-search?q=a')
            ->assertOk();
    }

    // ==================== CHECKOUT / ORDER ====================

    public function test_checkout_page_loads_with_selected_cart_items(): void
    {
        $product = Product::where('stock', '>', 0)->firstOrFail();
        $this->actingAs($this->user);
        $cart = Cart::create(['user_id' => $this->user->id, 'product_id' => $product->id, 'quantity' => 1, 'size' => 'M']);

        $this->get('/user/checkout?cartItems=' . $cart->id)->assertOk();
    }

    public function test_min_order_setting_blocks_checkout_transaction(): void
    {
        Setting::setValue('min_order_amount', '999999999', 'commerce', 'number');
        Cache::flush();
        $product = Product::where('stock', '>', 0)->firstOrFail();
        $this->actingAs($this->user);

        $resp = $this->postJson('/user/checkout/createTransaction', [
            'order_id' => 'TEST-MIN-' . uniqid(),
            'gross_amount' => 1000,
            'payment_type' => 'qris',
            'transaction_status' => 'pending',
            'transaction_id' => 'trx-' . uniqid(),
            'customer_name' => $this->user->name,
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'snap_token' => 'dummy',
            'items' => [['id' => $product->id, 'quantity' => 1]],
        ]);
        $resp->assertStatus(422);
    }

    public function test_order_history_page_loads(): void
    {
        $this->actingAs($this->user)->get('/user/orders/order-history')->assertOk();
    }

    // ==================== MIDTRANS CALLBACK ====================

    public function test_midtrans_callback_rejects_invalid_signature(): void
    {
        $payload = [
            'order_id' => 'ORDER-TEST-1',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'invalid-signature',
            'transaction_status' => 'settlement',
        ];

        $this->postJson('/api/midtrans/callback', $payload)->assertStatus(403);
    }

    public function test_midtrans_callback_accepts_valid_signature_and_updates_transaction(): void
    {
        $orderId = 'ORDER-CB-' . uniqid();
        $gross = 150000;
        $statusCode = '200';
        $serverKey = config('midtrans.server_key') ?: 'dummy-server-key';
        config(['midtrans.server_key' => $serverKey]);
        $signature = hash('sha512', $orderId . $statusCode . $gross . '.00' . $serverKey);

        $tx = Transaction::create([
            'order_id' => $orderId,
            'user_id' => $this->user->id,
            'gross_amount' => $gross,
            'payment_type' => 'qris',
            'transaction_status' => 'pending',
            'transaction_time' => now(),
            'customer_name' => $this->user->name,
        ]);

        $this->postJson('/api/midtrans/callback', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $gross . '.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ])->assertOk();

        $tx->refresh();
        $this->assertEquals('settlement', $tx->transaction_status);
    }

    // ==================== ADMIN GUARDS ====================

    public function test_admin_cannot_delete_self(): void
    {
        $this->actingAs($this->admin);
        $resp = $this->delete('/admin/users/' . $this->admin->id);
        $resp->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    // ==================== SETTINGS BEHAVIOR ====================

    public function test_show_out_of_stock_setting_filters_products(): void
    {
        $cat = Category::firstOrFail();
        $inStock = Product::create([
            'category_id' => $cat->id, 'brand' => 'B', 'title' => 'InStock ' . uniqid(),
            'description' => 'd', 'price' => 1000, 'stock' => 5, 'weight' => 100,
        ]);
        $outStock = Product::create([
            'category_id' => $cat->id, 'brand' => 'B', 'title' => 'OutStock ' . uniqid(),
            'description' => 'd', 'price' => 1000, 'stock' => 0, 'weight' => 100,
        ]);

        Setting::setValue('show_out_of_stock', '0', 'commerce', 'boolean');
        Cache::flush();

        $resp = $this->get('/products');
        $resp->assertOk();
        $resp->assertSee($inStock->title);
        $resp->assertDontSee($outStock->title);
    }

    public function test_admin_settings_upload_logo(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $payload = [
            'store_name' => 'DRVN', 'store_tagline' => '', 'store_email' => 'a@b.com',
            'store_phone' => '', 'store_whatsapp' => '', 'store_address' => '', 'operating_hours' => '',
            'about_text' => '', 'footer_text' => '', 'social_instagram' => '', 'social_facebook' => '', 'social_tiktok' => '',
            'currency_symbol' => 'Rp', 'monthly_revenue_goal' => 50000000, 'min_order_amount' => 0,
            'default_shipping_fallback' => 0, 'order_id_prefix' => 'DRV', 'show_out_of_stock' => '1',
            'store_logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ];

        $this->put('/admin/settings', $payload)->assertRedirect(route('admin.settings.index'));
        $this->assertNotEmpty(Setting::getValue('store_logo'));
    }
}
