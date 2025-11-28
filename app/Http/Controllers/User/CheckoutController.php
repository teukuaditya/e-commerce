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
            // Ambil items sekali, pakai di semua tempat
            $items = $request->input('items', []);

            // Simpan data transaksi utama
            $transaction = new Transaction();
            $transaction->order_id           = $request->order_id;
            $transaction->user_id           = Auth::id();
            $transaction->gross_amount      = $request->gross_amount;
            $transaction->payment_type      = $request->payment_type;
            $transaction->transaction_status = $request->transaction_status ?? 'pending';
            $transaction->transaction_time  = now();
            $transaction->customer_name     = $request->customer_name;
            $transaction->courier           = $request->courier;
            $transaction->courier_service   = $request->courier_service;
            $transaction->snap_token        = $request->snap_token;
            $transaction->save();

            // Hapus item dari cart kalau ada items
            if (!empty($items) && is_array($items)) {
                $productIds = collect($items)->pluck('id')->toArray();

                Cart::where('user_id', Auth::id())
                    ->whereIn('product_id', $productIds)
                    ->delete();

                // Simpan detail transaksi
                foreach ($items as $item) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $item['id'],
                        'quantity'       => $item['quantity'],
                        'price'          => $item['price'],
                        'subtotal'       => $item['quantity'] * $item['price'],
                    ]);
                }
            }

            return response()->json([
                'success'  => true,
                'order_id' => $transaction->order_id,
                'message'  => 'Transaction created successfully',
            ]);
        } catch (\Throwable $e) {
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
