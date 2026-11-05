<?php

namespace App\Http\Controllers;

use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        // Eager loading mematikan N+1 (view memakai $cart->user & $cart->product).
        // Dulu controller memuat full-tabel products & users — tidak dipakai
        // oleh view, kini hanya relasi yang dibutuhkan.
        $carts = Cart::with(['user', 'product'])->latest('created_at')->get();

        return view('pages.admin.carts.index', compact('carts'));
    }
}
