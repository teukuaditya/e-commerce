<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Menampilkan daftar produk
    public function index()
    {
        $categories = Category::all(); // Mengambil data kategori
        $products = Product::all(); // Mengambil data produk

        return view('pages.admin.products.index', compact('categories', 'products'));
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
        // Validasi inputan
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'size' => 'nullable|array',
            'size.*' => 'nullable|string',
            'stock' => 'required|integer',
            'weight' => 'required|integer|min:0',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Proses gambar
        $images = [];
        foreach (['image1', 'image2', 'image3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                $image = $request->file($imageField);
                $imageName = $this->generateImageName($image, $request->title, $index + 1);
                $image->storeAs('public/products', $imageName);
                $images[] = $imageName;
            }
        }

        // Simpan nama gambar sebagai JSON
        $validated['image'] = !empty($images) ? json_encode($images) : null;

        // Simpan ukuran (jika ada) dalam format JSON
        if ($request->has('size')) {
            $validated['size'] = json_encode($request->size);
        }

        // Simpan produk
        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    // Mengupdate produk
    public function update(Request $request, Product $product)
    {
        // Validasi inputan
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'size' => 'nullable|array',
            'size.*' => 'nullable|string',
            'stock' => 'required|integer',
            'weight' => 'required|integer|min:0',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Proses gambar baru
        $images = [];
        $oldImages = is_string($product->image) ? json_decode($product->image, true) : $product->image;

        foreach (['image1', 'image2', 'image3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                // Jika ada gambar baru pada form input, hapus gambar lama yang sesuai
                if (isset($oldImages[$index]) && Storage::exists('public/products/' . $oldImages[$index])) {
                    Storage::delete('public/products/' . $oldImages[$index]);
                }

                $image = $request->file($imageField);
                $imageName = $this->generateImageName($image, $request->title, $index + 1);
                $image->storeAs('public/products', $imageName);
                $images[] = $imageName;
            } elseif ($request->has('delete_image' . ($index + 1))) {
                // Hapus gambar lama jika field delete_imageX ada
                $imageToDelete = $request->input('delete_image' . ($index + 1));
                if ($imageToDelete && Storage::exists('public/products/' . $imageToDelete)) {
                    Storage::delete('public/products/' . $imageToDelete);
                }
            } else {
                // Jika tidak ada gambar baru dan tidak ada yang terhapus, simpan gambar lama
                if (isset($oldImages[$index])) {
                    $images[] = $oldImages[$index];
                }
            }
        }

        // Simpan nama gambar sebagai JSON
        $validated['image'] = !empty($images) ? json_encode($images) : null;

        // Simpan ukuran (jika ada) dalam format JSON
        if ($request->has('size')) {
            $validated['size'] = json_encode($request->size);
        }

        // Update produk
        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }


    // Menghapus produk
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Hapus gambar jika ada
        if ($product->image) {
            // Cek apakah $product->image sudah berupa array atau string JSON
            $oldImages = is_string($product->image) ? json_decode($product->image, true) : $product->image;

            // Loop untuk menghapus gambar-gambar lama
            foreach ($oldImages as $oldImage) {
                // Cek apakah file gambar ada dan dihapus
                if (Storage::exists('public/products/' . $oldImage)) {
                    Storage::delete('public/products/' . $oldImage);
                }
            }
        }

        // Hapus produk
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }


    // Fungsi untuk membuat nama file gambar unik
    private function generateImageName($image, $title, $index)
    {
        $titleSlug = str_replace(' ', '_', strtolower($title));
        return $titleSlug . '-' . $index . '.' . $image->getClientOriginalExtension();
    }
}
