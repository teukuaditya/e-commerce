<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// USER (Frontend) Controllers
use App\Http\Controllers\User\HomeController as UserHomeController;
use App\Http\Controllers\User\CategoryController as UserCategoryController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\CheckoutController as UserCheckoutController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\UserController as UserUserController;
use App\Http\Controllers\User\AboutController as UserAboutController;

// ADMIN (Backend) Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailController;
use App\Http\Controllers\UserController;

/**
 * -------------------------------------------------------
 * PUBLIC ROOT
 * -------------------------------------------------------
 * Root diarahkan ke user home. TIDAK diberi nama 'user.home'
 * agar tidak duplikat dengan versi /user.
 */
Route::get('/', fn () => redirect()->route('user.home'));

// Search Products
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');


/**
 * -------------------------------------------------------
 * USER (PUBLIC) — semua orang bisa akses (guest/login/admin)
 * -------------------------------------------------------
 */
Route::prefix('user')->name('user.')->group(function () {
    // Home (ini yang dipakai navbar: route('user.home'))
    Route::get('/', [UserHomeController::class, 'index'])->name('home');

    // About
    Route::get('/about', [UserAboutController::class, 'index'])->name('about.index');

    // Products (public listing & detail)
    Route::get('/products', [UserProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [UserProductController::class, 'show'])->name('products.show');

    // Categories (public listing & detail)
    Route::get('/categories', [UserCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{id}', [UserCategoryController::class, 'show'])->name('categories.products');

    // Search Products
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    /**
     * -----------------------------------------------
     * USER (AUTH) — butuh login
     * -----------------------------------------------
     */
    Route::middleware('auth')->group(function () {

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [UserCartController::class, 'index'])->name('index');
            Route::post('/add/{productId}', [UserCartController::class, 'add'])->name('add');
            Route::put('/{cartId}', [UserCartController::class, 'updateCart'])->name('update');
            Route::delete('/{cartId}', [UserCartController::class, 'removeFromCart'])->name('remove');
            Route::post('/bulk-remove', [UserCartController::class, 'removeSelectedItems'])->name('bulkRemove');
        });

        // Checkout
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [UserCheckoutController::class, 'index'])->name('index');
            Route::post('/', [UserCheckoutController::class, 'store'])->name('store');
            Route::post('/createTransaction', [UserCheckoutController::class, 'createTransaction']);
        });

        // Orders
        Route::prefix('orders')->name('order.')->group(function () {
            Route::get('/order-history', [UserOrderController::class, 'index'])->name('index');
            Route::get('/snap-token', [UserOrderController::class, 'SnapToken']);
            Route::post('/payment-success', [UserOrderController::class, 'handlePaymentSuccess']);
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/edit', [UserUserController::class, 'showEditForm'])->name('edit');
            Route::put('/update', [UserUserController::class, 'editUser'])->name('update');
            Route::get('/update', [UserUserController::class, 'showEditForm'])->name('update.get');
        });


        // Address
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

/**
 * -------------------------------------------------------
 * ADMIN — wajib login + role admin
 * -------------------------------------------------------
 */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('transactions-details', TransactionDetailController::class);
    Route::resource('carts', CartController::class);
    Route::resource('users', UserController::class);
});

/**
 * -------------------------------------------------------
 * AUTH scaffolding
 * -------------------------------------------------------
 * Auth::routes() biasanya sudah memuat POST /logout (name: logout).
 * Jadi TIDAK perlu bikin route logout manual agar tidak bentrok.
 */
Auth::routes();

// (Opsional) fallback 404 untuk route tak dikenal
// Route::fallback(fn() => abort(404));
