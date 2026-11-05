<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // Data produk home ter-cache, sirna saat ada produk yang di-CRUD
        // (lihat invalidasi model event di AppServiceProvider).
        $products = Cache::rememberForever(
            'home.products',
            fn () => Product::all()
        );

        return view('pages.user.home', compact('products'));
    }
}
