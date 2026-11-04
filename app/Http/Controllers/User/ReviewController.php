<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan / memperbarui review user untuk sebuah produk.
     * Satu user = satu review per produk (unique index), menyimpan ulang = update.
     */
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
            ],
            [
                'rating'  => $data['rating'],
                'comment' => $data['comment'],
            ]
        );

        // AJAX (popup rating) → JSON; normal → redirect balik ke halaman produk
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $review->wasRecentlyCreated
                    ? 'Review berhasil ditambahkan!'
                    : 'Review berhasil diperbarui!',
                'rating'  => $review->rating,
            ]);
        }

        $wasNew   = $review->wasRecentlyCreated;
        $status   = $wasNew ? 'success' : 'info';
        $message  = $wasNew ? 'Terima kasih! Review Anda berhasil ditambahkan.' : 'Review Anda berhasil diperbarui.';

        return redirect()
            ->route('user.products.show', $product->id)
            ->with($status, $message);
    }

    /**
     * Menghapus review milik user sendiri
     */
    public function destroy(Request $request, Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403, 'Bukan review milik Anda.');

        $review->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Review dihapus.']);
        }

        return redirect()
            ->route('user.products.show', $review->product_id)
            ->with('success', 'Review Anda dihapus.');
    }
}
