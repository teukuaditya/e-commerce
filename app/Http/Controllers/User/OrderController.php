<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        // Mengambil transaksi yang berhubungan dengan user yang sedang login
        $transactions = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with(['details.product'])
            ->get();

        return view('pages.user.order.index', compact('transactions'));
    }

    /**
     * Mengembalikan snap_token untuk melanjutkan pembayaran.
     *
     * Temuan audit K3: sebelumnya tidak ada pengecekan user_id sehingga
     * user mana pun bisa melakukan token transaksi & melakukannya kembali.
     */
    public function SnapToken(Request $request)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->where('order_id', $request->order_id)
            ->first();

        if (! $transaction || ! $transaction->snap_token) {
            return response()->json(['error' => 'Transaction or Snap Token not found.'], 404);
        }

        return response()->json(['snap_token' => $transaction->snap_token]);
    }

    /**
     * Update status pembayaran dari popup Midtrans di sisi client.
     *
     * Temuan audit K1 (KRITIS): sebelumnya method ini menuruti SEMUA
     * transaction_status yang diterima dari client. Cukup request POST
     * {"transaction_status":"settlement"} untuk menandai transaksi LUNAS
     * tanpa membayar.
     *
     * Aturan baru: status final (settlement/deny/dll.) hanya boleh berasal
     * dari Midtrans — kita verifikasi ulang ke Midtrans API sebelum
     * menyimpan. Bila verifikasi gagal (key belum diset / network), status
     * PENDING dipertahankan dan webhook Midtrans yang akan mengubahnya.
     */
    public function handlePaymentSuccess(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|string',
            'payment_type' => 'nullable|string|max:50',
        ]);

        // Hanya transaksi milik user sendiri (temuan audit K3) yang boleh disentuh
        $transaction = Transaction::where('user_id', Auth::id())
            ->where('order_id', $data['order_id'])
            ->first();

        if (! $transaction) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }

        // Transaksi sudah final tidak perlu di-update lagi dari client
        if (in_array($transaction->transaction_status, ['settlement', 'expire', 'cancel', 'failure', 'deny'], true)) {
            return response()->json(['success' => 'No changes needed.']);
        }

        // Coba verifikasi status ke Midtrans (sumber kebenaran sebenarnya)
        $verifiedStatus = $this->verifyWithMidtrans($data['order_id']);

        if ($verifiedStatus !== null) {
            // Verifikasi sukses: simpan status yang benar dari Midtrans
            $transaction->payment_type = $data['payment_type'] ?? $transaction->payment_type;
            $transaction->transaction_status = $verifiedStatus;
            $transaction->save();

            return response()->json([
                'success' => 'Transaction status updated successfully.',
                'status' => $verifiedStatus,
            ]);
        }

        // Verifikasi TIDAK tersedia (server key kosong / network error):
        // pertahankan status pending, jangan pernah percaya klaim client
        return response()->json([
            'success' => 'Payment received — waiting for Midtrans confirmation.',
            'status' => $transaction->transaction_status,
        ]);
    }

    /**
     * Meminta status terjelas difaktur Midtrans API.
     * Mengembalikan string status bila BERHASIL, atau null bila
     * verifikasi tidak tersedia.
     */
    private function verifyWithMidtrans(string $orderId): ?string
    {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            Log::warning('verifyWithMidtrans: server key kosong, skip verifikasi.');

            return null;
        }

        try {
            Config::$serverKey = $serverKey;
            Config::$isProduction = config('midtrans.is_production', false);

            $status = MidtransTransaction::status($orderId);
            $status = is_string($status) ? json_decode($status, true) : (array) $status;

            return $status['transaction_status'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('verifyWithMidtrans gagal: ' . $e->getMessage());

            return null;
        }
    }
}
