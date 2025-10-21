<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // Ambil semua kategori
        return view('pages.admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validasi gambar
        ]);

        // Menyimpan kategori baru
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Menyimpan gambar dan hanya menyimpan nama file di database
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        // Simpan kategori ke database
        Category::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
            'image' => $imagePath ? basename($imagePath) : null, // Simpan hanya nama file
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validasi gambar
        ]);

        // Menemukan kategori berdasarkan ID
        $category = Category::findOrFail($id);

        // Menyimpan gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($category->image && Storage::exists('public/categories/' . $category->image)) {
                Storage::delete('public/categories/' . $category->image);
            }

            // Simpan gambar baru dan hanya simpan nama file
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = basename($imagePath); // Simpan hanya nama file
        }

        // Update kategori
        $category->update([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        // Menemukan kategori berdasarkan ID
        $category = Category::findOrFail($id);

        // Hapus gambar kategori jika ada
        if ($category->image && Storage::exists('public/categories/' . $category->image)) {
            Storage::delete('public/categories/' . $category->image);
        }

        // Hapus kategori
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
