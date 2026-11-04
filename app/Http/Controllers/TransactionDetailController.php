<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionDetailController extends Controller
{
    public function index()
    {
        $transactions_details = TransactionDetail::with(['transaction', 'product'])->latest('created_at')->get();
        return view('pages.admin.transactions-details.index', compact('transactions_details'));
    }
}
