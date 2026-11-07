<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
// USER (Frontend) Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailController;
use App\Http\Controllers\User\AboutController as UserAboutController;
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\CategoryController as UserCategoryController;
use App\Http\Controllers\User\CheckoutController as UserCheckoutController;
use App\Http\Controllers\User\HomeController as UserHomeController;
// ADMIN (Backend) Controllers
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ReviewController as UserReviewController;
use App\Http\Controllers\User\UserController as UserUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * PUBLIC ROOT
 * -------------------------------------------------------
 * Root diarahkan ke user home (302) — bentuk Route::redirect supaya
 * bisa di-cache oleh `php artisan route:cache` (closure dilarang).
 * Perilaku output sama: HTTP 302 → /user.
 */
Route::redirect('/', '/user', 302);

// Search Products (alias root route name products.search to user search)
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
    // ->whereNumber mencegah "search"/"abc" tertangkap sebagai {id} (route collision)
    Route::get('/products/{id}', [UserProductController::class, 'show'])
        ->name('products.show')->whereNumber('id');

    // Categories (public listing & detail)
    Route::get('/categories', [UserCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{id}', [UserCategoryController::class, 'show'])
        ->name('categories.products')->whereNumber('id');

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

        // Review produk (butuh login)
        Route::post('/products/{product}/reviews', [UserReviewController::class, 'store'])
            ->name('reviews.store')->whereNumber('product');
        Route::delete('/reviews/{review}', [UserReviewController::class, 'destroy'])
            ->name('reviews.destroy');

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
            /* Route GET provinces & cities DIHAPUS (Tahap 6):
               memanggil UserController::searchProvinces/searchCities yang
               tidak ada (Always 500). Frontend autocomplete memakai
               /api/search-destinations. */
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
    Route::get('dashboard/telemetry', [DashboardController::class, 'telemetry'])->name('dashboard.telemetry');
    Route::get('dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::get('quick-search', [DashboardController::class, 'quickSearch'])->name('quickSearch');

    /* CRUD Category & Product memakai modal di halaman index —
       create/show/edit tidak dipakai (tidak ada view-nya) */
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

    /* Transaksi, Detail Transaksi, Cart — read-only (index).
       CartController/Sub-controller lain tidak punya method/grid aksi. */
    Route::resource('transactions', TransactionController::class)->only(['index']);
    Route::resource('transactions-details', TransactionDetailController::class)->only(['index']);
    Route::resource('carts', CartController::class)->only(['index']);

    /* User — modal CRUD di halaman index (create/show/edit tidak dipakai) */
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
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
