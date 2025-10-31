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
use App\Http\Controllers\User\AboutController;

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
    // Public routes - pindahkan semua route public ke sini
    Route::get('/', [UserHomeController::class, 'index'])->name('home');
    Route::get('/about', [AboutController::class, 'index'])->name('about.index');
    Route::get('/products', [UserProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [UserProductController::class, 'show'])->name('products.show');
    Route::get('/categories', [UserCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{id}', [UserCategoryController::class, 'show'])->name('categories.products');

    // Group routes yang membutuhkan autentikasi
    Route::middleware('auth')->group(function () {
        // Cart routes
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [UserCartController::class, 'index'])->name('index');
            Route::post('/add/{productId}', [UserCartController::class, 'add'])->name('add');
            Route::put('/{cartId}', [UserCartController::class, 'updateCart'])->name('update');
            Route::delete('/{cartId}', [UserCartController::class, 'removeFromCart'])->name('remove');
            Route::post('/bulk-remove', [UserCartController::class, 'removeSelectedItems'])->name('bulkRemove');
        });

        // Checkout routes  
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [UserCheckoutController::class, 'index'])->name('index');
            Route::post('/', [UserCheckoutController::class, 'store'])->name('store');
            Route::post('/createTransaction', [UserCheckoutController::class, 'createTransaction']);
        });

        // Order routes
        Route::prefix('orders')->name('order.')->group(function () {
            Route::get('/', [UserOrderController::class, 'index'])->name('index');
            Route::get('/snap-token', [UserOrderController::class, 'SnapToken']);
            Route::post('/payment-success', [UserOrderController::class, 'handlePaymentSuccess']);
        });

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/edit', [UserUserController::class, 'showEditForm'])->name('edit');
            Route::put('/update', [UserUserController::class, 'editUser'])->name('update');
        });

        // Address routes
        Route::prefix('address')->name('address.')->group(function () {
            Route::post('/save', [UserUserController::class, 'saveAddress'])->name('save');
            Route::get('/edit', [UserUserController::class, 'showEditAddress'])->name('edit');
            Route::post('/update', [UserUserController::class, 'updateAddress'])->name('update');
            Route::delete('/delete', [UserUserController::class, 'deleteAddress'])->name('delete');
            Route::get('/provinces', [UserUserController::class, 'searchProvinces'])->name('provinces');
            Route::get('/cities', [UserUserController::class, 'searchCities'])->name('cities');
        });
    });
});

// Route untuk autentikasi (login, register, logout)
Auth::routes();
