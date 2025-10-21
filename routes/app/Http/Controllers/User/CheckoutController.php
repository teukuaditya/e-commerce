<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // Menangani GET request untuk menampilkan halaman checkout
    public function index()
    {
        // Ambil data cart untuk user yang sedang login
        $selectedItemIds = request('cartItems') ? explode(',', request('cartItems')) : [];
        $cartItems = Cart::whereIn('id', $selectedItemIds)->where('user_id', Auth::id())->with('product')->get();

        // Menghitung total cart
        $cartTotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Panggil API RajaOngkir untuk mendapatkan daftar provinsi
        $client = new Client();
        $response = $client->request('GET', 'https://api.rajaongkir.com/starter/province', [
            'headers' => [
                'key' => env('RAJAONGKIR_API_KEY'),
            ],
        ]);

        $provinces = json_decode($response->getBody()->getContents(), true)['rajaongkir']['results'];

        // Ambil alamat pengguna yang sedang login dari kolom address di tabel users
        $user = Auth::user();
        $address = $user->address;

        // Pisahkan alamat menjadi provinsi dan kota
        $addressParts = explode(',', $address);
        $province = isset($addressParts[0]) ? trim($addressParts[0]) : null;
        $city = isset($addressParts[1]) ? trim($addressParts[1]) : null;

        // Menangkap City ID menggunakan regex (misalnya City ID: 41)
        $cityId = null;
        if (preg_match('/City ID: (\d+)/', $address, $matches)) {
            $cityId = $matches[1];
        }

        // Kirim data ke view
        return view('pages.user.checkout.index', compact('cartItems', 'cartTotal', 'provinces', 'cityId'));
    }


    // Menangani POST request untuk memproses data checkout
    public function store(Request $request)
    {
        // Validasi dan simpan data yang dikirim
        $data = $request->validate([
            'address' => 'required|string',
        ]);

        // Update alamat pengguna yang sedang login
        Auth::user()->update(['address' => $data['address']]);

        // Setelah proses selesai, redirect ke halaman checkout
        return redirect()->route('checkout.index');
    }


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

    public function createTransaction(Request $request)
    {
        // Simpan data transaksi
        $transaction = new Transaction();
        $transaction->order_id = $request->order_id;  // ID unik order
        $transaction->user_id = Auth::id();  // ID user yang login
        $transaction->gross_amount = $request->gross_amount;  // Total harga
        $transaction->payment_type = $request->payment_type;  // Payment type yang dipilih pengguna
        $transaction->transaction_status = $request->transaction_status;
        $transaction->transaction_time = now();  // Waktu transaksi
        $transaction->customer_name = $request->customer_name;  // Nama customer
        $transaction->courier = $request->courier; // Simpan kurir
        $transaction->courier_service = $request->courier_service; // Simpan layanan pengiriman
        $transaction->snap_token = $request->snap_token;  // Simpan Snap Token
        $transaction->save();

        if (isset($request->items) && is_array($request->items)) {
            $productIds = collect($request->items)->pluck('id')->toArray(); // Ambil semua product_id
            Cart::where('user_id', Auth::id())
                ->whereIn('product_id', $productIds)
                ->delete(); // Hapus produk yang telah di-checkout dari keranjang
        }

        // Simpan detail transaksi
        foreach ($request->items as $item) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price'],
            ]);
        }

        return response()->json(['order_id' => $transaction->order_id, 'message' => 'Transaction created successfully']);
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
