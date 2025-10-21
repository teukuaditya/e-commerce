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

}
