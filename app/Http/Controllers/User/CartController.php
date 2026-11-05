<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(CartRequest $request, $productId)
    {
        $validated = $request->validated();

        $product = Product::find($productId);

        // Cek stok produk
        if ($product && $product->stock >= $validated['quantity']) {
            $user = Auth::user();
            $size = $validated['size'] ?? null;  // Default null jika tidak ada size
            $quantity = $validated['quantity'];

            // Cari cart item user + produk; SEMUA query di-scope user_id (temuan audit K3)
            if ($size !== null) {
                $query = Cart::where('user_id', $user->id)->where('product_id', $product->id)->where('size', $size);
            } else {
                // (temuan audit S1) size=NULL harus pakai whereNull —
                // where('size', null) menghasilkan "size = NULL" yang TIDAK PERNAH match
                $query = Cart::where('user_id', $user->id)->where('product_id', $product->id)->whereNull('size');
            }
            $cart = $query->first();

            if ($cart) {
                // Gaung plus kuantitas baru tidak boleh melebihi stok (temuan audit S7)
                if ($cart->quantity + $quantity > $product->stock) {
                    return redirect()->back()->with('error', 'Not enough stock available.');
                }
                $cart->quantity += $quantity;
                $cart->save();
            } else {
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
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        $cartCount = $cartItems->sum('quantity'); // Menjumlahkan kuantitas produk di keranjang

        return view('pages.user.cart.index', compact('cartItems', 'cartCount'));
    }

    public function updateCart(CartRequest $request, $cartId)
    {
        $validated = $request->validated();

        /**
         * Temuan audit K3 (IDOR): sebelumnya Cart::find($cartId) tanpa filter,
         * siapapun bisa mengubah kuantitas keranjang user lain.
         */
        $cart = Cart::where('user_id', Auth::id())->find($cartId);

        if (! $cart || ! $cart->product) {
            return response()->json(['success' => false, 'message' => 'Cart item not found.'], 404);
        }

        // Cek jika kuantitas yang diminta tidak melebihi stok produk
        if ($validated['quantity'] > $cart->product->stock) {
            return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
        }

        $cart->quantity = $validated['quantity'];
        $cart->save();

        return response()->json(['success' => true]);
    }

    public function removeFromCart($cartId)
    {
        // Hanya boleh menghapus item keranjang milik sendiri (temuan audit K3)
        Cart::where('user_id', Auth::id())->where('id', $cartId)->delete();

        return redirect()->route('user.cart.index');
    }

    public function removeSelectedItems(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false], 422);
        }

        // Temuan audit K3: scope user_id supaya tidak bisa menghapus cart orang lain
        Cart::where('user_id', Auth::id())->whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }
}
