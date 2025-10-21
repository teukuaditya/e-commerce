<?php

use App\Http\Controllers\User\HomeController as UserHomeController;
use App\Http\Controllers\User\CategoryController as UserCategoryController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\CheckoutController as UserCheckoutController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\UserController as UserUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route untuk halaman utama pengguna
Route::get('/', [UserHomeController::class, 'index'])->name('user.home');

// Route untuk logout pengguna
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route untuk admin dengan middleware 'admin'
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // Dashboard admin
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Route untuk resource categories, products, transactions, dan carts
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('transactions-details', TransactionDetailController::class);
    Route::resource('carts', CartController::class);
    Route::resource('users', UserController::class);
});

// Route untuk pengguna dengan middleware yang tidak terautentikasi
Route::prefix('user')->name('user.')->group(function () {
    // Menampilkan semua produk
    Route::get('/products', [UserProductController::class, 'index'])->name('products.index');

    // Menampilkan detail produk
    Route::get('/products/{id}', [UserProductController::class, 'show'])->name('products.show');

    // Menampilkan kategori produk
    Route::get('/categories', [UserCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{id}', [UserCategoryController::class, 'show'])->name('categories.products');

    // Menampilkan keranjang belanja (harus login)
    Route::get('/cart', [UserCartController::class, 'index'])->name('cart.index')->middleware('auth');

    // Menambahkan produk ke keranjang belanja (harus login)
    Route::post('/cart/add/{productId}', [UserCartController::class, 'add'])->name('cart.add')->middleware('auth');

    // Mengupdate jumlah produk dalam keranjang belanja (harus login)
    Route::put('/cart/{cartId}', [UserCartController::class, 'updateCart'])->name('cart.update')->middleware('auth');

    // Menghapus produk dari keranjang belanja (harus login)
    Route::delete('/cart/{cartId}', [UserCartController::class, 'removeFromCart'])->name('cart.remove')->middleware('auth');

    // Menghapus beberapa produk dari keranjang belanja (harus login)
    Route::post('/cart/bulk-remove', [UserCartController::class, 'removeSelectedItems'])->name('cart.bulkRemove')->middleware('auth');

    // Route untuk checkout (harus login)
    Route::get('/checkout', [UserCheckoutController::class, 'index'])->name('checkout.index')->middleware('auth');
    Route::post('/checkout', [UserCheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');
    // Route::post('/midtrans/snap', [UserCheckoutController::class, 'getSnapToken'])->name('api.midtrans.snap')->middleware('auth');
    Route::post('/createTransaction', [UserCheckoutController::class, 'createTransaction'])->middleware('auth');
    // Route::get('/midtrans/snap-token', [UserCheckoutController::class, 'SnapToken'])->middleware('auth');
    // Route::post('/midtrans/notification', [UserCheckoutController::class, 'handleMidtransNotification']);
    // Riwayat pesanan pengguna (harus login)
    Route::get('/order-history', [UserOrderController::class, 'index'])->middleware('auth')->name('order.index');
    Route::get('/snap-token', [UserOrderController::class, 'SnapToken'])->middleware('auth');
    Route::post('/payment-success', [UserOrderController::class, 'handlePaymentSuccess'])->middleware('auth');

    // Edit profil pengguna (harus login)
    Route::get('/edit-profile', [UserUserController::class, 'showEditForm'])->name('edit')->middleware('auth');
    Route::put('/update-profile', [UserUserController::class, 'editUser'])->name('profile')->middleware('auth');

    // Menyimpan alamat pengguna (harus login)
    Route::post('/save-address', [UserUserController::class, 'saveAddress'])->middleware('auth');
    Route::get('/search-provinces', [UserUserController::class, 'searchProvinces'])->name('searchProvinces')->middleware('auth');
    Route::get('/search-cities', [UserUserController::class, 'searchCities'])->name('searchCities')->middleware('auth');

    // Edit alamat pengguna (harus login)
    Route::get('/edit-address', [UserUserController::class, 'showEditAddress'])->name('address.edit')->middleware('auth');
    Route::post('/update-address', [UserUserController::class, 'updateAddress'])->name('address.update')->middleware('auth');

    // Hapus alamat pengguna (harus login)
    Route::delete('/delete-address', [UserUserController::class, 'deleteAddress'])->middleware('auth');
});


// Route untuk autentikasi (login, register, logout)
Auth::routes();
