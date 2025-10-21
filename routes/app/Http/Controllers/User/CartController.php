<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request, $productId)
    {
        $validated = $request->validate([
            'size' => 'nullable|string',  // size nullable
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($productId);

        // Cek stok produk
        if ($product && $product->stock >= $validated['quantity']) {
            $user = Auth::user();
            $size = $validated['size'] ?? null;  // Default null jika tidak ada size
            $quantity = $validated['quantity'];

            // Cek apakah item dengan size dan quantity yang sama sudah ada
            $cart = Cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('size', $size)
                ->first();

            if ($cart) {
                // Jika ada, update quantity-nya
                $cart->quantity += $quantity;
                $cart->save();
            } else {
                // Jika belum ada, buat item baru di keranjang
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'size' => $size,
                    'quantity' => $quantity,
                ]);
            }

            return redirect()->route('user.cart.index')->with('success', 'Product added to cart!');
        }

        return redirect()->back()->with('error', 'Not enough stock available.');
    }



    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
        $cartCount = $cartItems->sum('quantity'); // Menjumlahkan kuantitas produk di keranjang

        return view('pages.user.cart.index', compact('cartItems', 'cartCount'));
    }

    public function updateCart(Request $request, $cartId)
{
    // Validasi input kuantitas
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    // Cari cart item berdasarkan ID
    $cart = Cart::find($cartId);

    if ($cart) {
        // Ambil produk yang terkait dengan cart item
        $product = $cart->product;

        // Cek jika kuantitas yang diminta tidak melebihi stok produk
        if ($request->quantity > $product->stock) {
            return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
        }

        // Update kuantitas dan simpan ke database
        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Cart item not found.']);
}



    public function removeFromCart($cartId)
    {
        $cart = Cart::find($cartId);

        if ($cart) {
            $cart->delete();
        }

        return redirect()->route('user.cart.index');
    }

    public function removeSelectedItems(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            Cart::whereIn('id', $ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

}
