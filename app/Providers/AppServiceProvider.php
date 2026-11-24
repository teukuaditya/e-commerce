<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Midtrans\Config;

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
        $wishlistCount = null;
        view()->composer('*', function ($view) use (&$cartCount, &$wishlistCount) {
            if ($cartCount === null) {
                $cartCount = Auth::check()
                    ? (int) Cart::where('user_id', Auth::id())->sum('quantity')
                    : 0;
            }
            if ($wishlistCount === null) {
                $wishlistCount = Auth::check()
                    ? (int) Wishlist::where('user_id', Auth::id())->count()
                    : 0;
            }

            $view->with('cartCount', $cartCount);
            $view->with('wishlistCount', $wishlistCount);
        });

        /*
         * Notifikasi toko di topbar admin (hanya query jika view topbar di-render).
         */
        view()->composer('layouts.partials.admin-topbar', function ($view) {
            if (! $view->offsetExists('notifications')) {
                $notifications = Transaction::latest('id')->limit(5)->get()->map(function ($nt) {
                    $isPaid = $nt->transaction_status === 'settlement';
                    $time = $nt->transaction_time ?? $nt->created_at;
                    return [
                        'title' => $isPaid ? 'Pembayaran #' . $nt->order_id : 'Pesanan Baru #' . $nt->order_id,
                        'desc' => 'Rp ' . number_format($nt->gross_amount, 0, ',', '.') . ' • ' . ($nt->customer_name ?? 'Customer'),
                        'time_ago' => $time ? \Carbon\Carbon::parse($time)->diffForHumans() : 'baru saja',
                        'icon' => $isPaid ? 'lucide:check-circle-2' : 'lucide:shopping-bag',
                        'icon_bg' => $isPaid ? '#6366F1' : '#10B981',
                        'url' => route('admin.transactions.index'),
                    ];
                });
                $view->with('notifications', $notifications);
            }
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
