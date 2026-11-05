<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product; // Pastikan model Category digunakan

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get(); // Mengambil produk beserta kategori terkait
        $categories = Category::all(); // Mengambil semua kategori

        return view('pages.user.products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('pages.user.products.detail', compact('product'));
    }
}
