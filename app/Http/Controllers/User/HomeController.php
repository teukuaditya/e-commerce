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
        // with('category') mencegah N+1: tiap kartu memakai $product->category.
        $products = Cache::rememberForever(
            'home.products',
            fn () => Product::with('category')->get()
        );

        return view('pages.user.home', compact('products'));
    }
}
