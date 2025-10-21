<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::all(); // Ambil semua data transaksi
        $products = Product::all(); // Mengambil data produk
        $users = User::all(); // Ambil semua data transaksi
        return view('pages.admin.carts.index', compact('carts','users', 'products'));
    }
}
