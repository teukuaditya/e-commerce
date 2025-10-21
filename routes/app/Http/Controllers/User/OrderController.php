<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Mengambil transaksi yang berhubungan dengan user yang sedang login
        $transactions = Transaction::where('user_id', Auth::id())->orderBy('created_at', 'desc')->with(['details.product'])->get();

        return view('pages.user.order.index', compact('transactions'));
    }

    public function SnapToken(Request $request)
{
    // Ambil transaksi berdasarkan order_id
    $transaction = Transaction::where('order_id', $request->order_id)->first();

    // Jika transaksi tidak ditemukan atau snap_token tidak ada
    if (!$transaction || !$transaction->snap_token) {
        return response()->json(['error' => 'Transaction or Snap Token not found.'], 404);
    }

    // Kembalikan snap_token yang ditemukan
    return response()->json(['snap_token' => $transaction->snap_token]);
}

public function handlePaymentSuccess(Request $request)
    {
        $transaction = Transaction::where('order_id', $request->order_id)->first();

        // Jika transaksi sudah ada, hanya update statusnya
        $transaction->payment_type = $request->payment_type;
        $transaction->transaction_status = $request->transaction_status;
        $transaction->save();

        return response()->json(['success' => 'Transaction status updated successfully.']);
}

}
