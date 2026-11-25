<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Transaction;

class AboutController extends Controller
{
    public function index()
    {
        $s = Setting::allCached();
        $company = [
            'name' => $s['store_name'] ?? 'DRVN',
            'tagline' => $s['store_tagline'] ?? 'all about essentials',
            'email' => $s['store_email'] ?? '',
            'phone' => $s['store_phone'] ?? '',
            'whatsapp' => $s['store_whatsapp'] ?? '',
            'address' => $s['store_address'] ?? '',
            'operating_hours' => $s['operating_hours'] ?? '',
            'about_text' => $s['about_text'] ?? '',
        ];

        // Statistik nyata dari database untuk band angka (bukan angka hard-code)
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'orders' => Transaction::count(),
            // Rating rata-rata dari tabel reviews — fallback "?" bila belum ada
            'avg_rating' => Review::count() > 0 ? (float) round(Review::avg('rating'), 1) : null,
        ];

        return view('pages.user.about.index', compact('company', 'stats'));
    }
}
