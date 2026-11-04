<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Temuan audit K5: kredensial dibaca lewat config (cacheable),
        // BUKAN env() langsung yang mengembalikan null setelah config:cache.
        // Nilai aktual diambil dari .env — dijadikan aman karena config sudah
        // meng-merge via config/midtrans.php.
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized  = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds        = (bool) config('midtrans.is_3ds', true);
    }

    /**
     * Generate Snap Token dari Midtrans
     */
    public function getSnapToken(Request $request)
    {
        // Cek server key
        if (empty(Config::$serverKey)) {
            return response()->json([
                'error' => 'ServerKey is missing or invalid.',
            ], 500);
        }

        // ===========================
        // Ambil data dasar dari request
        // ===========================
        $orderId     = $request->input('order_id');
        $grossAmount = (int) $request->input('gross_amount');
        $customer    = $request->input('customer_details', []);

        if (!$orderId || !$grossAmount || empty($customer)) {
            return response()->json([
                'error' => 'Missing required parameters (order_id / gross_amount / customer_details).',
                'debug' => [
                    'order_id'         => $orderId,
                    'gross_amount'     => $grossAmount,
                    'customer_details' => $customer,
                ],
            ], 400);
        }

        // ===========================
        //  Ambil items dengan aman & hitung ulang harga dari database (K2)
        // ===========================
        $items = $request->input('items', []);

        if (!is_array($items) || empty($items)) {
            return response()->json([
                'error' => 'Invalid items format (must be non-empty array)',
            ], 422);
        }

        $productIds = collect($items)->pluck('id')->filter()->unique()->values()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $itemDetails = [];
        $calculatedItemsTotal = 0;

        foreach ($items as $item) {
            $productId = $item['id'] ?? null;
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $product = $products->get($productId);

            // Gunakan harga asli dari database untuk mencegah price tampering (K2)
            $price = $product ? (int) $product->price : (int) ($item['price'] ?? 0);
            $name = $product ? trim($product->brand . ' - ' . $product->title . (!empty($item['product_size']) ? ' - ' . $item['product_size'] : ''))
                             : trim(($item['product_brand'] ?? '') . ' - ' . ($item['product_title'] ?? ''));

            $itemDetails[] = [
                'id'       => (string) ($productId ?? 'unknown'),
                'quantity' => $qty,
                'name'     => mb_substr($name, 0, 50),
                'price'    => $price,
            ];

            $calculatedItemsTotal += ($price * $qty);
        }

        // ===========================
        //  Tambah Ongkir (optional)
        // ===========================
        $shippingCost = max(0, (int) $request->input('shipping_cost', 0));

        if ($shippingCost > 0) {
            $courier = $request->input('courier', '');
            $service = $request->input('service', '');

            $itemDetails[] = [
                'id'       => 'shipping',
                'quantity' => 1,
                'name'     => mb_substr('Shipping (' . $courier . ' - ' . $service . ')', 0, 50),
                'price'    => $shippingCost,
            ];
        }

        // Total gross amount yang diverifikasi di server
        $verifiedGrossAmount = $calculatedItemsTotal + $shippingCost;

        // ===========================
        //  Build payload ke Midtrans
        // ===========================
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $verifiedGrossAmount,
            ],
            'customer_details' => [
                'first_name' => $customer['first_name'] ?? '',
                'email'      => $customer['email']      ?? '',
                'phone'      => $customer['phone']      ?? '',
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapToken error', [
                'message' => $e->getMessage(),
                'params'  => $params,
            ]);

            return response()->json([
                'error' => 'Gagal membuat Snap Token. Cek konfigurasi Midtrans / payload order.',
            ], 500);
        }
    }

    /**
     * Callback dari Midtrans (notifikasi server-to-server)
     */
    public function handleMidtransCallback(Request $request)
    {
        Log::info('Midtrans Callback Received:', ['data' => $request->all()]);

        $orderId      = $request->input('order_id');
        $statusCode   = $request->input('status_code');
        $grossAmount  = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        // Temuan audit K5: baca lewat config, BUKAN env() (rusak setelah config:cache)
        $serverKey    = config('midtrans.server_key');

        // ===========================
        //  Validasi signature
        // ===========================
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Invalid Signature Key:', ['order_id' => $orderId]);

            return response()->json([
                'error' => 'Invalid Signature Key',
            ], 403);
        }

        // ===========================
        //  Ambil transaksi + details + product
        // ===========================
        $transaction = Transaction::with(['details.product'])
            ->where('order_id', $orderId)
            ->first();

        if (!$transaction) {
            Log::error('Transaction not found', ['order_id' => $orderId]);

            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }

        $oldStatus = $transaction->transaction_status;

        // Kalau sudah settlement, tidak usah di-update lagi
        if ($oldStatus === 'settlement') {
            Log::info('Transaction already settled, skipping update.', [
                'order_id' => $orderId,
            ]);

            return response()->json([
                'success' => 'Transaction is already settled',
            ]);
        }

        // ===========================
        //  Update status
        // ===========================
        $newStatus = $request->input('transaction_status');

        // Pending: hanya update status kalau belum settlement
        if ($newStatus === 'pending' && $oldStatus !== 'settlement') {
            $transaction->transaction_status = $newStatus;
        }

        // Jika status baru adalah gagal / expired / cancel → balikin stock (sekali saja)
        if (in_array($newStatus, ['expire', 'cancel', 'failure'])) {
            // Cegah double-restock:
            // cuma restock kalau sebelumnya BUKAN expire/cancel/failure/settlement
            if (!in_array($oldStatus, ['expire', 'cancel', 'failure', 'settlement'])) {
                foreach ($transaction->details as $detail) {
                    if ($detail->product) {
                        $detail->product->stock += $detail->quantity;
                        $detail->product->save();
                    }
                }
                Log::info('Stock restored because payment failed/expired.', [
                    'order_id' => $orderId,
                ]);
            }

            $transaction->transaction_status = $newStatus;
        }

        // Jika status baru settlement → tandai lunas
        if ($newStatus === 'settlement') {
            $transaction->transaction_status = 'settlement';
        }

        // Temuan audit T4: jangan timpa gross_amount tanpa dicocokkan
        if ((int) $transaction->gross_amount !== (int) $grossAmount) {
            Log::warning('Midtrans callback gross_amount mismatch', [
                'order_id' => $orderId,
                'expected' => $transaction->gross_amount,
                'received' => $grossAmount,
            ]);
            // Hanya update jika sebelumnya belum ada nilai
            if (empty($transaction->gross_amount)) {
                $transaction->gross_amount = $grossAmount;
            }
        }
        $transaction->save();

        Log::info('Transaction updated successfully', [
            'order_id'   => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $transaction->transaction_status,
        ]);

        return response()->json([
            'success' => 'Transaction status updated successfully',
        ]);
    }
}
