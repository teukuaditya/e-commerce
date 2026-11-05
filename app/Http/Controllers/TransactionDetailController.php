<?php

namespace App\Http\Controllers;

use App\Models\TransactionDetail;

class TransactionDetailController extends Controller
{
    public function index()
    {
        $transactions_details = TransactionDetail::with(['transaction', 'product'])->latest('created_at')->get();

        return view('pages.admin.transactions-details.index', compact('transactions_details'));
    }
}
