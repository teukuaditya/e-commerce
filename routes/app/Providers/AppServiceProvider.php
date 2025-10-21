<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Midtrans\Config;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Menghitung jumlah produk dalam keranjang dan mengirimkan ke semua view
        view()->composer('*', function ($view) {
            // Mengecek apakah user sudah login
            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())->sum('quantity'); // Menjumlahkan jumlah produk di keranjang
            } else {
                $cartCount = 0; // Jika user belum login, set jumlah keranjang 0
            }

            // Mengirimkan variabel cartCount ke semua view
            $view->with('cartCount', $cartCount);
        });

        // Validasi apakah tabel 'categories' sudah ada
        if (Schema::hasTable('categories')) {
            view()->share('categories', Category::all());
        }

        // Validasi apakah tabel 'products' sudah ada
        if (Schema::hasTable('products')) {
            view()->share('products', Product::all());
        }
    }
}
