<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {

        $transactions = Transaction::all(); // Ambil semua data transaksi

        return view('pages.admin.transactions.index', compact('transactions'));
    }
}
