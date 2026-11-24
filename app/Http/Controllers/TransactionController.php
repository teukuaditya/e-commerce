<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest('created_at')->get();

        // Auto-sinkronisasi status transaksi 'pending' dengan Midtrans API
        // agar status selalu akurat meskipun webhook belum diterima
        foreach ($transactions as $transaction) {
            if ($transaction->transaction_status === 'pending') {
                try {
                    Config::$serverKey = config('midtrans.server_key');
                    Config::$isProduction = (bool) config('midtrans.is_production', false);
                    $status = MidtransTransaction::status($transaction->order_id);
                    $status = is_string($status) ? json_decode($status, true) : (array) $status;
                    $verifiedStatus = $status['transaction_status'] ?? null;
                    if ($verifiedStatus && $verifiedStatus !== 'pending') {
                        $transaction->transaction_status = $verifiedStatus;
                        $transaction->save();
                    }
                } catch (\Throwable $e) {
                    // Abaikan jika order_id tidak terdaftar di Midtrans (seperti dummy seed)
                }
            }
        }

        return view('pages.admin.transactions.index', compact('transactions'));
    }
}
