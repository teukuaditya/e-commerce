<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {

        $transactions = Transaction::all(); // Ambil semua data transaksi
        return view('pages.admin.transactions.index', compact('transactions'));
    }
}
