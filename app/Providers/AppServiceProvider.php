<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
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

        /*
         * Jumlah produk di keranjang yang tanggal ditampilkan di navbar.
         * PERFORMA: closure di-memoize (variable by-reference) sehingga
         * query SUM() hanya dijalankan SATU KALI per request — bukan
         * untuk setiap view yang di-render (layout + tiap partial).
         */
        $cartCount = null;
        view()->composer('*', function ($view) use (&$cartCount) {
            if ($cartCount === null) {
                $cartCount = Auth::check()
                    ? (int) Cart::where('user_id', Auth::id())->sum('quantity')
                    : 0;
            }

            $view->with('cartCount', $cartCount);
        });

        /*
         * Kategori dipakai di navbar, mobile-nav, dan footer pada SEMUA halaman.
         * Di-cache agar tidak query tabel kategori setiap request.
         * Dibersihkan otomatis lewat model events di bawah setiap encode/list.
         */
        if (Schema::hasTable('categories')) {
            view()->share('categories', Cache::rememberForever(
                'shares.categories',
                fn () => Category::all()
            ));
        }

        /*
         * Invalidasi cache shares setiap categoryId/produk berubah
         * (dari admin CRUD, seed, dsb).
         */
        Category::saved(fn () => Cache::forget('shares.categories'));
        Category::deleted(fn () => Cache::forget('shares.categories'));

        /*
         * `$products` global-share (dipakai halaman home) dihapus — sekarang
         * query hanya dari HomeController, ter-cache sampai ada produk yang
         * berubah. Membuang load full table products dari setiap request
         * (termasuk admin).
         */
        Product::saved(fn () => Cache::forget('home.products'));
        Product::deleted(fn () => Cache::forget('home.products'));
    }
}
