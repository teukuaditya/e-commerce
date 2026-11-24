<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MidtransIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_snap_token_endpoint_returns_token(): void
    {
        // Skip jika dijalankan di environment tanpa akses internet (seperti local test sandbox)
        $connected = @fsockopen('app.sandbox.midtrans.com', 443, $errno, $errstr, 2);
        if (! $connected) {
            $this->markTestSkipped('Koneksi internet ke Midtrans Sandbox API tidak tersedia di runner ini.');
        } else {
            fclose($connected);
        }

        $category = Category::first() ?? Category::create(['name' => 'Testing', 'slug' => 'testing']);
        $product = Product::first() ?? Product::create([
            'category_id' => $category->id,
            'title' => 'Test Item',
            'slug' => 'test-item-' . uniqid(),
            'price' => 25000,
            'stock' => 10,
            'description' => 'Test Description',
        ]);

        $orderId = 'TEST-' . uniqid();
        $response = $this->postJson('/api/midtrans/snap', [
            'order_id' => $orderId,
            'gross_amount' => (int) $product->price,
            'customer_details' => [
                'first_name' => 'Pembeli Test',
                'email' => 'buyer@example.com',
                'phone' => '081234567890',
            ],
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => (int) $product->price,
                    'product_title' => $product->title,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_callback_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => 'INVALID-ORDER-123',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'invalid_hash_signature',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Invalid Signature Key']);
    }

    public function test_callback_validates_signature_correctly(): void
    {
        $orderId = 'ORDER-TEST-' . uniqid();
        $statusCode = '200';
        $grossAmount = '50000.00';

        // Signature generated using signature key or server key
        $key = config('midtrans.signature_key') ?: config('midtrans.server_key');
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $key);

        // When signature is valid, it looks for transaction.
        // If transaction doesn't exist, it should return 404 (not 403 invalid signature),
        // proving signature verification succeeded!
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Transaction not found']);
    }
}
