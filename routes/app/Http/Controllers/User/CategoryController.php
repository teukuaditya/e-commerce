<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        $categories = Category::all();
        return view('pages.user.categories.index', compact('categories'));
    }

    // Menampilkan produk berdasarkan kategori
    // Di CategoryController.php
public function show($categoryId)
{
    // Ambil kategori berdasarkan ID
    $category = Category::findOrFail($categoryId);

    // Ambil produk berdasarkan kategori yang dipilih
    $products = Product::where('category_id', $categoryId)->get();

    // Ambil semua kategori untuk filter
    $categories = Category::all();

    // Kirim data ke view
    return view('pages.user.categories.show', compact('category', 'products', 'categories'));
}
}
