<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Transaction;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class MidtransController extends Controller
{

    public function getSnapToken(Request $request)
    {
        // Trim dan set kunci API
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = false; // Set ke true jika sudah di produksi
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Debugging jika serverKey kosong
        if (empty(Config::$serverKey)) {
            return response()->json(['error' => 'ServerKey is missing or invalid.'], 500);
        }

        // Pastikan data ada di request
        if (!$request->order_id || !$request->gross_amount || !$request->customer_details) {
            return response()->json(['error' => 'Missing required parameters.'], 400);
        }

        // Menyiapkan parameter transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $request->order_id,
                'gross_amount' => $request->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $request->customer_details['first_name'],
                'email' => $request->customer_details['email'],
                'phone' => $request->customer_details['phone'],
            ],
            'item_details' => array_map(function ($item) {
                return [
                    'id' => $item['id'], // ID produk, pastikan ini ada di frontend
                    'quantity' => $item['quantity'], // Jumlah produk
                    'name' => $item['product_brand'] . ' - ' . $item['product_title'] . ' - ' . $item['product_size'], // Nama produk, sesuai dengan yang dikirim dari frontend
                    'price' => $item['price'], // Harga per unit produk
                ];
            }, $request->items), // Pastikan Anda mengirimkan data `items` dalam request
            'customer_name' => $request->customer_details['first_name'], // Pastikan customer_name ada
        ];

        // Menambahkan Ongkos Kirim sebagai item baru
        if (isset($request->shipping_cost) && $request->shipping_cost > 0) {
            $params['item_details'][] = [
                'id' => 'shipping', // ID untuk ongkos kirim
                'quantity' => 1, // Jumlah hanya 1
                'name' => 'Shipping (' . $request->courier . ' - ' . $request->service . ')', // Nama layanan pengiriman
                'price' => $request->shipping_cost, // Biaya pengiriman
            ];
        }

        try {
            // Mendapatkan snap token
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            // Menangani kesalahan jika terjadi
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleMidtransCallback(Request $request)
    {
        \Log::info('Midtrans Callback Received:', ['data' => $request->all()]);

        // Ambil data dari request
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code'); // Status code dari Midtrans
        $grossAmount = $request->input('gross_amount'); // Total jumlah transaksi
        $signatureKey = $request->input('signature_key'); // Signature key dari Midtrans
        $serverKey = env('MIDTRANS_SERVER_KEY'); // Ambil Server Key Midtrans dari .env

        // Validasi Signature Key dengan urutan yang benar: order_id . status_code . gross_amount . server_key
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // Periksa apakah signature yang dikirimkan cocok dengan yang diharapkan
        if ($signatureKey !== $expectedSignature) {
            \Log::warning('Invalid Signature Key:', ['order_id' => $orderId]);
            return response()->json(['error' => 'Invalid Signature Key'], 403);
        }

        // Cari transaksi berdasarkan order_id
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            \Log::error('Transaction not found', ['order_id' => $orderId]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Cek apakah status transaksi di database sudah settlement
        if ($transaction->transaction_status === 'settlement') {
            // Jika status transaksi sudah settlement, kita hanya log dan tidak update status lagi
            \Log::info('Transaction already settled, skipping update.', ['order_id' => $orderId]);
            return response()->json(['success' => 'Transaction is already settled']);
        }

        // Update status transaksi hanya jika status yang diterima lebih baru
        $newStatus = $request->input('transaction_status');

        // Jika status baru adalah pending dan transaksi sudah settlement, jangan update status
        if ($newStatus === 'pending' && $transaction->transaction_status !== 'settlement') {
            $transaction->transaction_status = $newStatus;
        }

        if (in_array($newStatus, ['settlement', 'expire', 'cancel', 'failure'])) {
            // Update status sesuai dengan status terbaru yang diterima
            $transaction->transaction_status = $newStatus;
        }

        // Update gross_amount
        $transaction->gross_amount = $grossAmount;
        $transaction->save();

        // Log the updated status
        \Log::info('Transaction updated successfully', ['transaction' => $transaction]);

        return response()->json(['success' => 'Transaction status updated successfully']);
    }
}
