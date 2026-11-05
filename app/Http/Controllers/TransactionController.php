<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest('created_at')->get();

        return view('pages.admin.transactions.index', compact('transactions'));
    }
}
