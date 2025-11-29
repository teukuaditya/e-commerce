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
use App\Models\Product;
use Illuminate\Support\Facades\DB;



class CheckoutController extends Controller
{
    // Menangani GET request untuk menampilkan halaman checkout
    public function index()
    {
        // Ambil data cart untuk user yang sedang login
        $selectedItemIds = request('cartItems') ? explode(',', request('cartItems')) : [];
        $cartItems = Cart::whereIn('id', $selectedItemIds)
            ->where('user_id', Auth::id())
            ->with('product')
            ->get();

        // Hitung total cart
        $cartTotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Ambil alamat pengguna yang sedang login dari kolom address di tabel users
        $user = Auth::user();
        $address = $user->address;

        // Default variabel untuk di view
        $recipientName = null;
        $recipientPhone = null;
        $streetAddress = null;
        $destinationName = null;
        $destinationId = null;

        // Jika user punya alamat tersimpan, parse datanya
        if ($address) {
            preg_match(
                '/Recipient Name: (.*?), Phone: (.*?), Address: (.*?), Destination: (.*?), Destination ID: (.*?)(,|$)/',
                $address,
                $matches
            );

            $recipientName = $matches[1] ?? null;
            $recipientPhone = $matches[2] ?? null;
            $streetAddress = $matches[3] ?? null;
            $destinationName = $matches[4] ?? null;
            $destinationId = $matches[5] ?? null;
        }

        // Kirim data ke view
        return view('pages.user.checkout.index', compact(
            'cartItems',
            'cartTotal',
            'recipientName',
            'recipientPhone',
            'streetAddress',
            'destinationName',
            'destinationId'
        ));
    }

    // Menangani POST request untuk memproses data checkout
    public function store(Request $request)
    {
        // Validasi data
        $data = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'destination_name' => 'required|string|max:255',
            'destination_id' => 'required|string|max:50',
        ]);

        // Format address string untuk disimpan
        $formattedAddress = sprintf(
            'Recipient Name: %s, Phone: %s, Address: %s, Destination: %s, Destination ID: %s',
            $data['recipient_name'],
            $data['recipient_phone'],
            $data['address'],
            $data['destination_name'],
            $data['destination_id']
        );

        // Update address user
        Auth::user()->update([
            'address' => $formattedAddress
        ]);

        return redirect()->route('checkout.index')->with('success', 'Address updated successfully.');
    }

    public function createTransaction(Request $request)
    {
        try {
            // Ambil items dari request
            $items = $request->input('items', []);

            if (empty($items) || !is_array($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Items tidak ditemukan di request.',
                ], 422);
            }

            // Ambil semua product_id yang terlibat
            $productIds = collect($items)->pluck('id')->toArray();

            // Ambil data produk sekaligus, lalu keyBy id biar gampang diakses
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // ============================
            // 1. VALIDASI STOK SEBELUM BIKIN TRANSAKSI
            // ============================
            foreach ($items as $item) {
                $productId = $item['id'] ?? null;
                $qty       = (int) ($item['quantity'] ?? 0);

                if (!$productId || $qty <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data item tidak valid.',
                    ], 422);
                }

                /** @var \App\Models\Product|null $product */
                $product = $products->get($productId);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Produk dengan ID {$productId} tidak ditemukan.",
                    ], 404);
                }

                if ($product->stock < $qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok untuk produk '{$product->title}' tidak mencukupi. Sisa stok: {$product->stock}",
                    ], 422);
                }
            }

            // ============================
            // 2. SIMPAN TRANSAKSI + DETAIL + UPDATE STOK (DALAM DB TRANSACTION)
            // ============================
            DB::beginTransaction();

            // Simpan data transaksi utama
            $transaction = new Transaction();
            $transaction->order_id            = $request->order_id;
            $transaction->user_id             = Auth::id();
            $transaction->gross_amount        = $request->gross_amount;
            $transaction->payment_type        = $request->payment_type;
            // kalau dari JS kadang belum kirim status, default ke "pending"
            $transaction->transaction_status  = $request->transaction_status ?? 'pending';
            $transaction->transaction_time    = now();
            $transaction->customer_name       = $request->customer_name;
            $transaction->courier             = $request->courier;
            $transaction->courier_service     = $request->courier_service;
            $transaction->snap_token          = $request->snap_token;
            $transaction->save();

            // Hapus produk dari cart user
            Cart::where('user_id', Auth::id())
                ->whereIn('product_id', $productIds)
                ->delete();

            // Simpan detail transaksi + update stok produk
            foreach ($items as $item) {
                $productId = $item['id'];
                $qty       = (int) $item['quantity'];
                $price     = (int) $item['price'];

                /** @var \App\Models\Product $product */
                $product = $products->get($productId);

                // Detail transaksi
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $productId,
                    'quantity'       => $qty,
                    'price'          => $price,
                    'subtotal'       => $qty * $price,
                ]);

                // Kurangi stok produk
                $product->decrement('stock', $qty);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $transaction->order_id,
                'message'  => 'Transaction created successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('createTransaction error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
