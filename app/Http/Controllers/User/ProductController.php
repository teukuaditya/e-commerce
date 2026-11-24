<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Dukung ?q= (format URL baru) dan ?query= (link lama tetap berfungsi)
        $query = trim((string) $request->input('q', $request->input('query', '')));
        $categoryFilter = $request->input('category');
        $categoryFilter = is_string($categoryFilter) ? trim($categoryFilter) : null;

        $productsQuery = Product::with('category')
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('brand', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                });
            })
            ->when($categoryFilter && $categoryFilter !== '*', function ($q) use ($categoryFilter) {
                $q->whereHas('category', function ($sub) use ($categoryFilter) {
                    // LIKE -> tidak peka huruf besar/kecil (?category=baju tetap cocok "Baju")
                    $sub->where('category_name', 'LIKE', $categoryFilter)
                        ->orWhere('id', $categoryFilter);
                });
            })
            ->orderByDesc('id');

        // 16 products per page (4 rows x 4 products per row)
        $products = $productsQuery->paginate(16)->withQueryString();
        $categories = Category::all();

        // AJAX request: return partial HTML for instant live search & seamless pagination
        if ($request->ajax()) {
            $gridHtml = view('pages.user.products._partials.grid-items', compact('products'))->render();
            $paginationHtml = $products->hasPages() ? (string) $products->links() : '';

            return response()->json([
                'count' => $products->total(),
                'query' => $query,
                'category' => $categoryFilter ?? '',
                'html' => $gridHtml,
                'pagination' => $paginationHtml,
            ]);
        }

        return view('pages.user.products.index', compact('products', 'categories', 'query'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'reviews.user'])->findOrFail($id);

        // Ringkasan rating (avg/count/stars) + review milik user sendiri (bila ada)
        $rating = $product->ratingSummary();
        $myReview = auth()->check()
            ? $product->reviews->firstWhere('user_id', auth()->id())
            : null;

        return view(
            'pages.user.products.detail',
            compact('product', 'rating', 'myReview')
        );
    }
}
