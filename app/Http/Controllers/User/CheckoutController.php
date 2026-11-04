<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Support\AddressFormatter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /** Nilai yang diperbolehkan kolom enum di tabel transactions (lihat migration). */
    private const PAYMENT_TYPES = [
        'credit_card', 'bank_transfer', 'ewallet', 'gopay',
        'qris', 'cstore', 'kredivo', 'akulaku',
    ];

    private const COURIERS = ['JNE', 'POS', 'SICEPAT'];

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

        // Parsing alamat disatukan lewat AddressFormatter (temuan audit T1)
        $parsed = AddressFormatter::parse(Auth::user()->address);

        // Kirim data ke view
        return view('pages.user.checkout.index', array_merge($parsed, [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]));
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

        // Update address user (format seragam lewat AddressFormatter)
        Auth::user()->update([
            'address' => AddressFormatter::format($data),
        ]);

        return redirect()->route('user.checkout.index')->with('success', 'Address updated successfully.');
    }

    /**
     * Menyimpan transaksi setelah pembayaran dari popup Midtrans.
     *
     * PENTING (temuan audit K2): harga & total TIDAK dibaca dari request.
     * Server menghitung ulang dari database produk agar tidak bisa dimanipulasi.
     */
    public function createTransaction(Request $request)
    {
        try {
            $items = $request->input('items', []);
            if (empty($items) || ! is_array($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Items tidak ditemukan di request.',
                ], 422);
            }

            $productIds = collect($items)->pluck('id')->unique()->values()->all();

            /**
             * KUNCI BARIS (temuan audit T3): lockForUpdate mencegah oversell
             * ketika dua request membeli item terakhir bersamaan.
             */
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 1. VALIDASI STOK + hitung ulang gross_amount dari database
            $grossAmount = 0;
            $lines = [];
            foreach ($items as $item) {
                $productId = $item['id'] ?? null;
                $qty = (int) ($item['quantity'] ?? 0);

                if (! $productId || $qty <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data item tidak valid.',
                    ], 422);
                }

                /** @var \App\Models\Product|null $product */
                $product = $products->get($productId);

                if (! $product) {
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

                // Harga diambil dari DATABASE, bukan dari payload client (K2)
                $price = (int) $product->price;
                $grossAmount += $price * $qty;

                $lines[] = ['product' => $product, 'qty' => $qty, 'price' => $price];
            }

            // 2. GROSS AMOUNT harus sama dengan total Midtrans; bila client mengirim
            //    gross_amount berbeda (mis. karena ongkir), pakai yang terbesar
            //    supaya jumlah yang tersimpan tidak lebih rendah dari SNap.
            //    (Idealnya ongkir ikut disimpan server-side — lihat catatan audit.)
            //

            $clientGross = (int) ($request->gross_amount ?? 0);
            $storedGross = max($grossAmount, $clientGross > 0 ? $clientGross : 0);

            // 3. SIMPAN TRANSAKSI + DETAIL + UPDATE STOK (DALAM DB TRANSACTION)
            DB::beginTransaction();

            $transaction = Transaction::create([
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'gross_amount' => $storedGross,
                'payment_type' => $this->normalizePaymentType($request->payment_type),
                // Status selalu dimulai dari "pending" — PERUBAHAN hanya boleh
                // dilakukan webhook Midtrans yang tervarifikasi (temuan audit K1)
                'transaction_status' => 'pending',
                'transaction_time' => now(),
                'customer_name' => $request->customer_name,
                'courier' => $this->normalizeCourier($request->courier),
                'courier_service' => $request->courier_service,
                'snap_token' => $request->snap_token,
            ]);

            // Hapus produk dari cart user
            Cart::where('user_id', Auth::id())
                ->whereIn('product_id', $productIds)
                ->delete();

            // Simpan detail transaksi + update stok produk
            foreach ($lines as $line) {
                $product = $line['product'];
                $qty     = $line['qty'];
                $price   = $line['price'];

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);

                $product->decrement('stock', $qty);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $transaction->order_id,
                'message' => 'Transaction created successfully',
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Order ID sudah pernah dipakai. Coba lagi.',
            ], 409);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Detail error hanya ke log — JANGAN bocor ke client (temuan audit S5)
            Log::error('createTransaction error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi. Coba lagi.',
            ], 500);
        }
    }

    /**
     * Midtrans mengirim payment_type seperti "bca_va", "echannel", "shopeepay"
     * yang TIDAK ADA di salah satu enum kolom (S4) → insert error.
     * Gapai mual ke nilai enum terdekat.
     */
    private function normalizePaymentType(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        if (in_array($type, self::PAYMENT_TYPES, true)) {
            return $type;
        }

        if (str_contains($type, 'card')) {
            return 'credit_card';
        }

        if (preg_match('/(va|bank|echannel|bca|bni|bri|mandiri|permata)/', $type)) {
            return 'bank_transfer';
        }

        if ($type === '') {
            return 'bank_transfer'; // default enum paling netral
        }

        return 'ewallet';
    }

    /** Midtrans/kurir bebas — hanya JNE/POS/SICEPAT yang valid di enum kolom. */
    private function normalizeCourier(?string $courier): ?string
    {
        return in_array(strtoupper((string) $courier), self::COURIERS, true)
            ? strtoupper($courier)
            : null;
    }
}
