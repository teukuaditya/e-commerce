<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // Data produk home ter-cache, sirna saat ada produk yang di-CRUD
        // (lihat invalidasi model event di AppServiceProvider).
        // with('category') mencegah N+1: tiap kartu memakai $product->category.
        $showOos = Setting::getValue('show_out_of_stock', '1') === '1';
        $products = Cache::remember('home.products.v2.' . ($showOos ? '1' : '0'), 300, function () use ($showOos) {
            return Product::with('category')
                ->when(! $showOos, fn ($q) => $q->where('stock', '>', 0))
                ->withSum(['transactionDetails as sold_count' => function ($q) {
                    $q->whereHas('transaction', fn ($t) => $t->where('transaction_status', 'settlement'));
                }], 'quantity')
                ->latest()
                ->take(4)
                ->get();
        });

        $bestSellers = Cache::remember('home.best_sellers.' . ($showOos ? '1' : '0'), 300, function () use ($showOos) {
            return Product::with('category')
                ->when(! $showOos, fn ($q) => $q->where('stock', '>', 0))
                ->withSum(['transactionDetails as sold_count' => function ($q) {
                    $q->whereHas('transaction', fn ($t) => $t->where('transaction_status', 'settlement'));
                }], 'quantity')
                ->orderByDesc('sold_count')
                ->orderByDesc('id')
                ->take(4)
                ->get();
        });

        $wishlistedIds = [];
        if (Auth::check()) {
            $allProductIds = $products->pluck('id')->merge($bestSellers->pluck('id'))->unique()->values();
            $wishlistedIds = Wishlist::where('user_id', Auth::id())
                ->whereIn('product_id', $allProductIds)
                ->pluck('product_id')
                ->all();
        }

        return view('pages.user.home', compact('products', 'bestSellers', 'wishlistedIds'));
    }
}
