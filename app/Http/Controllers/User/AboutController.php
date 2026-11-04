<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $company = [
            'name' => 'Drive Venture',
            'tagline' => 'all about essentials',
            'email' => 'driveventure719@gmail.com',
            'address' => 'Jl. Farida, Purwokerto, Jawa Tengah, Indonesia',
        ];

        // Statistik nyata dari database untuk band angka (bukan angka hard-code)
        $stats = [
            'products'    => Product::count(),
            'categories'  => Category::count(),
            'orders'      => Transaction::count(),
            // Rating rata-rata dari tabel reviews — fallback "?" bila belum ada
            'avg_rating'  => Review::count() > 0 ? (float) round(Review::avg('rating'), 1) : null,
        ];

        return view('pages.user.about.index', compact('company', 'stats'));
    }
}
