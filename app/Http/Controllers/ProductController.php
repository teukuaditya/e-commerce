<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Menampilkan daftar produk (admin)
    public function index()
    {
        $categories = Category::all();
        $products = Product::all();

        return view('pages.admin.products.index', compact('categories', 'products'));
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
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

        $images = [];
        foreach (['image1', 'image2', 'image3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                $image = $request->file($imageField);
                $imageName = $this->generateImageName($image, $request->title, $index + 1);
                $image->storeAs('public/products', $imageName);
                $images[] = $imageName;
            }
        }

        $validated['image'] = !empty($images) ? json_encode($images) : null;
        $validated['size'] = $request->has('size') ? json_encode($request->size) : null;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    // Mengupdate produk
    public function update(Request $request, Product $product)
    {
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

        $images = [];
        $oldImages = $product->image
            ? (is_string($product->image) ? json_decode($product->image, true) : (array) $product->image)
            : [];

        foreach (['image1', 'image2', 'image3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                if (isset($oldImages[$index]) && Storage::exists('public/products/' . $oldImages[$index])) {
                    Storage::delete('public/products/' . $oldImages[$index]);
                }

                $image = $request->file($imageField);
                $imageName = $this->generateImageName($image, $request->title, $index + 1);
                $image->storeAs('public/products', $imageName);
                $images[] = $imageName;
            } elseif ($request->filled('delete_image' . ($index + 1))) {
                // Jika ada permintaan hapus gambar spesifik
                $imageToDelete = $request->input('delete_image' . ($index + 1));
                if ($imageToDelete && Storage::exists('public/products/' . $imageToDelete)) {
                    Storage::delete('public/products/' . $imageToDelete);
                }
                // jangan masukkan ke $images (dihapus)
            } else {
                // Simpan gambar lama jika ada
                if (isset($oldImages[$index])) {
                    $images[] = $oldImages[$index];
                }
            }
        }

        $validated['image'] = !empty($images) ? json_encode(array_values($images)) : null;
        $validated['size'] = $request->has('size') ? json_encode($request->size) : null;

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    // Menghapus produk
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            $oldImages = is_string($product->image) ? json_decode($product->image, true) : (array) $product->image;
            foreach ($oldImages as $oldImage) {
                if ($oldImage && Storage::exists('public/products/' . $oldImage)) {
                    Storage::delete('public/products/' . $oldImage);
                }
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    // Fungsi untuk membuat nama file gambar unik
    private function generateImageName($image, $title, $index)
    {
        $titleSlug = str_replace(' ', '_', strtolower($title));
        return $titleSlug . '-' . $index . '.' . $image->getClientOriginalExtension();
    }

    /**
     * Search products (public)
     */
    public function search(Request $request)
    {
        $query = trim($request->input('query', ''));

        $productsQuery = Product::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                ->orWhere('brand', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->when(schemaHasColumn('products', 'status'), function ($q) {
                $q->where('status', 'active');
            });

        // 🔹 Kalau request dari AJAX (live search)
        if ($request->ajax()) {
            $products = $productsQuery
                ->limit(10)
                ->get(['id', 'title', 'brand', 'price', 'slug']); // sesuaikan kolom & slug punyamu

            return response()->json([
                'items' => $products,
            ]);
        }
        

        // 🔹 Mode normal (full page hasil search)
        if ($query === '') {
            return redirect()->route('user.home')
                ->with('error', 'Please enter search keyword.');
        }

        $products = $productsQuery->paginate(12);

        return view('pages.user.products.search', [
            'products' => $products,
            'query'    => $query,
            'total'    => $products->total(),
        ]);
    }


    }

/**
 * Helper kecil: periksa apakah kolom ada (opsional, menghindari error jika migrasi berbeda)
 * Jika tidak ingin helper ini, hapus penggunaan when(...) di atas dan gunakan ->where('status','active') langsung.
 */
if (! function_exists('schemaHasColumn')) {
    function schemaHasColumn($table, $column)
    {
        try {
            return \Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}