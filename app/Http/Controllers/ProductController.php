<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        // with('category') + withCount('transactionDetails') mencegah N+1:
        // tiap baris memakai $product->category & hitungan riwayat transaksi (badge proteksi delete).
        $products = Product::with('category')->withCount('transactionDetails')->get();

        return view('pages.admin.products.index', compact('categories', 'products'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $images = $this->handleUploadedImages($request, $validated['title']);

        $validated['image'] = ! empty($images) ? json_encode($images) : null;
        $validated['size'] = $request->has('size') ? json_encode($request->size) : null;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $images = $this->handleUploadedImages($request, $validated['title'], $product);

        $validated['image'] = ! empty($images) ? json_encode(array_values($images)) : null;
        $validated['size'] = $request->has('size') ? json_encode($request->size) : null;

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($this->existingImages($product) as $oldImage) {
            if ($oldImage && Storage::exists('public/products/'.$oldImage)) {
                Storage::delete('public/products/'.$oldImage);
            }
        }

        // Hapus data terkait untuk menjaga integritas referensi
        $product->reviews()->delete();
        \App\Models\Cart::where('product_id', $product->id)->delete();
        $product->transactionDetails()->delete();

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Search products (public) — full page & mode AJAX live search.
     */
    public function search(Request $request): JsonResponse|View|RedirectResponse
    {
        $query = trim((string) $request->input('query', ''));

        $productsQuery = Product::where(function ($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
                ->orWhere('brand', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%");
        })
            ->when($this->schemaHasColumn('products', 'status'), function ($q) {
                $q->where('status', 'active');
            });

        // 🔹 AJAX (live search): render ulang partial grid agar markup kartu
        //    tetap satu sumber kebenaran (tidak diduplikasi di JS)
        if ($request->ajax()) {
            $products = $productsQuery
                ->with('category')
                ->when($query === '', fn ($q) => $q->orderByDesc('id'), fn ($q) => $q->orderBy('title'))
                ->paginate(16)
                ->withQueryString();

            $html = view('pages.user.products._partials.grid-items', compact('products'))->render();
            $pagination = $products->hasPages() ? (string) $products->links() : '';

            return response()->json([
                'count' => $products->total(),
                'query' => $query,
                'html' => $html,
                'pagination' => $pagination,
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
            'query' => $query,
            'total' => $products->total(),
        ]);
    }

    /**
     * Upload foto produk baru (maks 3), hapus foto lama yang diganti/deleted-flagged,
     * kembalikan daftar nama file akhir. Digrantisasi untuk store & update.
     */
    private function handleUploadedImages(ProductRequest $request, string $title, ?Product $existing = null): array
    {
        $images = [];
        $oldImages = $existing ? $this->existingImages($existing) : [];

        foreach (['image1', 'image2', 'image3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                if (isset($oldImages[$index]) && Storage::exists('public/products/'.$oldImages[$index])) {
                    Storage::delete('public/products/'.$oldImages[$index]);
                }

                $image = $request->file($imageField);
                $imageName = $this->generateImageName($image, $title, $index + 1);
                $image->storeAs('public/products', $imageName);
                $images[] = $imageName;
            } elseif ($request->filled('delete_image'.($index + 1))) {
                // Permintaan hapus foto tertentu: buang dari disk, tidak selit ke hasil
                $imageToDelete = $request->input('delete_image'.($index + 1));
                if ($imageToDelete && Storage::exists('public/products/'.$imageToDelete)) {
                    Storage::delete('public/products/'.$imageToDelete);
                }
            } elseif (isset($oldImages[$index])) {
                // Foto lama tetap dipakai
                $images[] = $oldImages[$index];
            }
        }

        return $images;
    }

    /**
     * Decode kolom image produk (JSON string / array) ke daftar nama file.
     */
    private function existingImages(Product $product): array
    {
        if (! $product->image) {
            return [];
        }

        return is_string($product->image)
            ? (array) (json_decode($product->image, true) ?: [])
            : (array) $product->image;
    }

    /**
     * Nama file unik: {slug-title}-{random}-{index}.{ext}
     */
    private function generateImageName($image, string $title, int $index): string
    {
        $titleSlug = Str::slug($title);
        $random = Str::random(8);

        return $titleSlug.'-'.$random.'-'.$index.'.'.$image->getClientOriginalExtension();
    }

    /**
     * Cek kolom tabel dengan aman (schema panjang padat).
     */
    private function schemaHasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
