<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    /**
     * Generate Snap Token dari Midtrans
     */
    public function getSnapToken(Request $request)
    {
        // ===========================
        // Konfigurasi Midtrans
        // ===========================
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey    = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = false; // true kalau sudah live/production
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

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
        //  Ambil items dengan aman
        // ===========================
        $items = $request->input('items', []);

        if (!is_array($items)) {
            return response()->json([
                'error' => 'Invalid items format (must be array)',
                'items' => $items,
            ], 422);
        }

        $itemDetails = [];
        foreach ($items as $item) {
            $itemDetails[] = [
                'id'       => $item['id'] ?? 'unknown',
                'quantity' => (int) ($item['quantity'] ?? 1),
                'name'     => trim(
                    ($item['product_brand'] ?? '') . ' - ' .
                        ($item['product_title'] ?? '') .
                        (!empty($item['product_size']) ? ' - ' . $item['product_size'] : '')
                ),
                'price'    => (int) ($item['price'] ?? 0),
            ];
        }

        // ===========================
        //  Tambah Ongkir (optional)
        // ===========================
        $shippingCost = (int) $request->input('shipping_cost', 0);

        if ($shippingCost > 0) {
            $courier = $request->input('courier', '');
            $service = $request->input('service', '');

            $itemDetails[] = [
                'id'       => 'shipping',
                'quantity' => 1,
                'name'     => 'Shipping (' . $courier . ' - ' . $service . ')',
                'price'    => $shippingCost,
            ];
        }

        // ===========================
        //  Build payload ke Midtrans
        // ===========================
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                // total dari frontend (sudah termasuk ongkir)
                'gross_amount' => $grossAmount,
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
                'error'  => $e->getMessage(),
                'params' => $params, // buat debug kalau perlu
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
        $serverKey    = env('MIDTRANS_SERVER_KEY');

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

        $transaction->gross_amount = $grossAmount;
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
