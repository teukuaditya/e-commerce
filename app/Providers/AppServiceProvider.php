<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
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
                $notifications = Transaction::latest('updated_at')->latest('id')->limit(8)->get()->map(function ($nt) {
                    $isPaid = $nt->transaction_status === 'settlement';
                    $isPending = $nt->transaction_status === 'pending' || $nt->transaction_status === null;
                    $time = $nt->updated_at ?? $nt->transaction_time ?? $nt->created_at;
                    $statusLabel = $isPaid ? 'Settlement' : ($isPending ? 'Pending' : ucfirst((string) $nt->transaction_status));
                    return [
                        'id' => $nt->id,
                        'status' => $nt->transaction_status ?? 'pending',
                        'title' => $isPaid ? 'Pembayaran #' . $nt->order_id : 'Pesanan #' . $nt->order_id . ' (' . $statusLabel . ')',
                        'desc' => 'Rp ' . number_format($nt->gross_amount, 0, ',', '.') . ' • ' . ($nt->customer_name ?? 'Customer'),
                        'time_ago' => $time ? \Carbon\Carbon::parse($time)->diffForHumans() : 'baru saja',
                        'icon' => $isPaid ? 'lucide:check-circle-2' : ($isPending ? 'lucide:clock-3' : 'lucide:shopping-bag'),
                        'icon_bg' => $isPaid ? '#6366F1' : ($isPending ? '#F59E0B' : '#10B981'),
                        'url' => route('admin.transactions.index'),
                    ];
                })->values();
                $view->with('notifications', $notifications);
            }

            if (! $view->offsetExists('notificationSignature')) {
                $notifications = $view->offsetExists('notifications') ? $view->getData()['notifications'] : collect();
                $signature = collect($notifications)->map(function ($n) {
                    return ($n['id'] ?? '') . ':' . ($n['status'] ?? '');
                })->implode('|');
                $view->with('notificationSignature', $signature);
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

        // Company/store settings shared to all views (About, footer, etc.)
        view()->composer('*', function ($view) {
            if ($view->offsetExists('company')) return;
            $company = Cache::rememberForever('shares.company', function () {
                if (! Schema::hasTable('settings')) {
                    return $this->defaultCompany();
                }
                $s = Setting::allCached();
                return [
                    'name' => $s['store_name'] ?? 'DRVN',
                    'tagline' => $s['store_tagline'] ?? 'all about essentials',
                    'email' => $s['store_email'] ?? '',
                    'phone' => $s['store_phone'] ?? '',
                    'whatsapp' => $s['store_whatsapp'] ?? '',
                    'address' => $s['store_address'] ?? '',
                    'operating_hours' => $s['operating_hours'] ?? '',
                    'about_text' => $s['about_text'] ?? '',
                    'footer_text' => $s['footer_text'] ?? '',
                    'instagram' => $s['social_instagram'] ?? '',
                    'facebook' => $s['social_facebook'] ?? '',
                    'tiktok' => $s['social_tiktok'] ?? '',
                    'logo' => $s['store_logo'] ?? '',
                    'favicon' => $s['store_favicon'] ?? '',
                ];
            });
            $view->with('company', $company);
        });

        $forgetHome = function () {
            foreach (['home.products', 'home.products.v2', 'home.products.v2.1', 'home.products.v2.0', 'home.best_sellers', 'home.best_sellers.1', 'home.best_sellers.0'] as $k) {
                Cache::forget($k);
            }
        };
        Setting::saved(function () use ($forgetHome) {
            Cache::forget('shares.company');
            $forgetHome();
        });
        Setting::deleted(function () use ($forgetHome) {
            Cache::forget('shares.company');
            $forgetHome();
        });

        /*
         * `$products` global-share (dipakai halaman home) dihapus — sekarang
         * query hanya dari HomeController, ter-cache sampai ada produk yang
         * berubah. Membuang load full table products dari setiap request
         * (termasuk admin).
         */
        $forgetHomeProducts = function () {
            foreach (['home.products', 'home.products.v2', 'home.products.v2.1', 'home.products.v2.0', 'home.best_sellers', 'home.best_sellers.1', 'home.best_sellers.0'] as $k) {
                Cache::forget($k);
            }
        };
        Product::saved($forgetHomeProducts);
        Product::deleted($forgetHomeProducts);
    }

    private function defaultCompany(): array
    {
        return [
            'name' => 'DRVN',
            'tagline' => 'all about essentials',
            'email' => 'driveventure719@gmail.com',
            'phone' => '',
            'whatsapp' => '',
            'address' => 'Jl. Farida, Purwokerto, Jawa Tengah, Indonesia',
            'operating_hours' => '',
            'about_text' => '',
            'footer_text' => 'Pakaian esensial yang bertahan. Produksi lokal, bahan pilihan, harga jujur — semuanya untuk lemari harian Anda.',
            'instagram' => '',
            'facebook' => '',
            'tiktok' => '',
            'logo' => '',
            'favicon' => '',
        ];
    }
}
