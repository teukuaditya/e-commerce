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
        $products = Product::all(); // Mengambil data produk
        $transactions = Transaction::all(); // Ambil semua data transaksi
        $transactions_details = TransactionDetail::all(); // Ambil semua data transaksi
        return view('pages.admin.transactions-details.index', compact('transactions','transactions_details', 'products'));
    }
}
