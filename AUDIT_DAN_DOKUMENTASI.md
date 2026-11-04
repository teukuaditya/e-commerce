# 📦 E-Commerce Laravel — Dokumentasi & Audit Kode

> Tanggal audit: **30 Oktober 2026**
> Framework: **Laravel 10** (PHP ^8.1)
> Scope: seluruh codebase `/mnt/d/e-commerce` (routes, controllers, models, migrations, middleware, config, views, dependencies)

---

## DAFTAR ISI

1. [Ringkasan Proyek](#1-ringkasan-proyek)
2. [Teknologi & Dependency](#2-teknologi--dependency)
3. [Struktur Direktori](#3-struktur-direktori)
4. [Skema Database](#4-skema-database)
5. [Arsitektur & Alur Aplikasi](#5-arsitektur--alur-aplikasi)
6. [Dokumentasi Routes](#6-dokumentasi-routes)
7. [Dokumentasi Controllers](#7-dokumentasi-controllers)
8. [Dokumentasi Models](#8-dokumentasi-models)
9. [Konfigurasi Environment](#9-konfigurasi-environment)
10. [HASIL AUDIT — Ringkasan Temuan](#10-hasil-audit--ringkasan-temuan)
11. [Temuan Kritis](#11-temuan-kritis-prioritas-1)
12. [Temuan Tinggi](#12-temuan-tinggi-prioritas-2)
13. [Temuan Sedang](#13-temuan-sedang-prioritas-3)
14. [Temuan Rendah](#14-temuan-rendah-prioritas-4)
15. [Rekomendasi Contoh Perbaikan](#15-rekomendasi-contoh-perbaikan)
16. [Checklist Remediasi](#16-checklist-remediasi)

---

## 1. Ringkasan Proyek

Aplikasi **e-commerce** berbasis Laravel 10 dengan dua area utama:

| Area | Prefix | Deskripsi |
|---|---|---|
| **Frontend (User)** | `/user` | Katalog produk, kategori, pencarian, keranjang, checkout (ongkir via RajaOngkir), pembayaran via **Midtrans Snap**, riwayat order, profil & alamat |
| **Backend (Admin)** | `/admin` | Dashboard statistik, CRUD kategori, produk, transaksi, detail transaksi, keranjang, user |

**Integrasi pihak ketiga:**

| Layanan | Fungsi | Controller |
|---|---|---|
| **Midtrans (Snap)** | Payment gateway (kartu, e-wallet, VA, QRIS, dll.) | `App\Http\Controllers\Api\MidtransController` |
| **RajaOngkir (Komerce API v1)** | Pencarian destinasi & kalkulasi ongkos kirim | `App\Http\Controllers\Api\RajaOngkirController` |

**Alur pembayaran (implementasi saat ini):**
1. User memilih item di keranjang → halaman checkout.
2. Blade JS memanggil `POST /api/midtrans/snap` untuk mendapat **Snap Token**, lalu membuka popup `snap.pay()`.
3. Setelah pembayaran **berhasil di sisi client** (`onSuccess`), JS memanggil `POST /user/checkout/createTransaction` untuk menyimpan transaksi + detail + mengurangi stok.
4. Midtrans juga mengirim callback server-to-server ke `POST /api/midtrans/callback` yang memvalidasi signature & memperbarui status (`settlement` / `expire` / `cancel` / `failure`) termasuk **restock** bila pembayaran gagal.

---

## 2. Teknologi & Dependency

### PHP (`composer.json`)

| Paket | Versi | Peran |
|---|---|---|
| `laravel/framework` | ^10.0 | Framework utama |
| `laravel/ui` | ^4.5 | Auth scaffolding (Blade) |
| `laravel/sanctum` | ^3.2 | Token API (terpasang, tapi API belum memakainya) |
| `laravel/telescope` | ^5.2 | Debug/monitoring (dev) |
| `midtrans/midtrans-php` | ^2.6 | SDK Midtrans |
| `guzzlehttp/guzzle` | ^7.2 | HTTP client |
| dev: `barryvdh/laravel-debugbar`, `phpunit`, `faker`, dll. | | |

### JavaScript (`package.json`)

- `vite` ^4 + `laravel-vite-plugin` (entry: `resources/js/app.js`, `resources/sass/app.scss`)
- `bootstrap` ^5, `axios`, `select2`, `@popperjs/core`

> ⚠️ Catatan: asset build Vite ada, namun CSS utama di-serve dari `public/css/main.css` & `sb-admin-2.css` — pemisahan asset tidak konsisten.

---

## 3. Struktur Direktori

```
e-commerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/            → MidtransController, RajaOngkirController
│   │   │   ├── Auth/           → scaffolding login/register/reset (laravel/ui)
│   │   │   ├── User/           → HomeController, ProductController, CategoryController,
│   │   │   │                     CartController, CheckoutController, OrderController,
│   │   │   │                     UserController (profil/alamat), AboutController
│   │   │   ├── DashboardController.php     → admin
│   │   │   ├── ProductController.php       → admin CRUD + public search
│   │   │   ├── CategoryController.php      → admin CRUD
│   │   │   ├── CartController.php          → admin resource
│   │   │   ├── TransactionController.php   → admin list
│   │   │   ├── TransactionDetailController.php → admin resource
│   │   │   └── UserController.php           → admin CRUD user
│   │   └── Middleware/
│   │       ├── IsAdmin.php            → alias 'admin', cek `role !== 'admin'`
│   │       ├── CheckAuthenticated.php → alias 'auth.check'
│   │       └── (default Laravel: auth, guest, csrf, dll.)
│   ├── Models/ → User, Product, Category, Cart, Transaction, TransactionDetail
│   └── Providers/
├── config/     → app, database, midtrans, rajaongkir, sanctum, telescope, dll.
├── database/migrations/ → users, categories, products, carts, transactions, transaction_details
├── resources/views/
│   ├── layouts/
│   ├── auth/
│   └── pages/{admin,user}/...
├── routes/     → web.php, api.php, console.php, channels.php
├── public/     → css utama (main.css, sb-admin-2.css), .htaccess
├── .env.example / .gitignore / phpunit.xml / vite.config.js / schema.dbml
└── [file non-standar: app.zip, storage.zip, error_log, symlink.php, .htaccess di root]
```

---

## 4. Skema Database

Diagram tersedia di `schema.dbml`. Ringkasan dari migrations:

### `users`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| name | string | |
| email | string unique | |
| email_verified_at | timestamp nullable | |
| password | string | |
| phone | string nullable | |
| **role** | enum(`user`,`admin`) default `user` | dipakai middleware `IsAdmin` |
| address | text nullable | ⚠️ alamat disimpan sebagai **string terformat + di-parse regex** |

### `categories`
`id`, `category_name`, `description` (nullable), `image` (text nullable), timestamps.

### `products`
| Kolom | Tipe | Catatan |
|---|---|---|
| category_id | FK → categories | `onDelete('cascade')` ⚠️ |
| brand, title | string | |
| description | text nullable | |
| price | decimal(10,2) | |
| size | json nullable | diakses via accessor json_decode |
| stock | integer | |
| weight | integer default 0 | dipakai untuk hitung ongkir |
| image | json nullable | array nama file di `storage/app/public/products` |

### `carts`
`user_id` FK (cascade), `product_id` FK (cascade), `size` nullable, `quantity` integer.

### `transactions`
| Kolom | Tipe | Catatan |
|---|---|---|
| order_id | string **unique** | dari Midtrans |
| user_id | FK users (cascade) | |
| customer_name | string | |
| gross_amount | bigInteger | |
| payment_type | enum (`credit_card`,`bank_transfer`,`ewallet`,`gopay`,`qris`,`cstore`,`kredivo`,`akulaku`) | ⚠️ nilai di luar enum → DB error 500 |
| courier | enum(`JNE`,`POS`,`SICEPAT`) nullable | ⚠️ enum kalau mau tambah kurir lain = migration |
| courier_service | string nullable | |
| transaction_status | string nullable | pending/settlement/expire/... |
| transaction_time | timestamp nullable | |
| snap_token | string nullable | disimpan plain |

### `transaction_details`
`transaction_id` FK (cascade), `product_id` FK (cascade ⚠️), `quantity`, `price`, `subtotal` (decimal 10,2).

**Relasi Eloquent:**
- `User 1..* Cart`; `Product 1..* Cart`
- `Category 1..* Product`
- `Transaction 1..* TransactionDetail`; `Product 1..* TransactionDetail`
- `TransactionDetail → Product`, `TransactionDetail → Transaction`

---

## 5. Arsitektur & Alur Aplikasi

### 5.1 Alur Belanja
```
/user (home) → /user/products → /user/products/{id} (pilih size & qty)
   → POST /user/cart/add → /user/cart (kelola keranjang, bulk remove)
   → /user/checkout?cartItems=1,2,3 (form alamat + ongkir)
        ├── GET /api/search-destinations (autocomplete kota RajaOngkir)
        ├── POST /api/cost (hitung ongkir, origin=17549 hard-coded, berat = Σ qty×weight)
        └── POST /api/midtrans/snap → snap.pay(token) → onSuccess
             └── POST /user/checkout/createTransaction
                  → validasi stok → DB transaction:
                       insert transactions + transaction_details
                       → kurangi stok → hapus item keranjang
   → POST /api/midtrans/callback (server-to-server)
        → validasi signature sha512(order_id+status_code+gross_amount+serverKey)
        → update status; expire/cancel/failure → restock
   → /user/orders/order-history (riwayat + retry bayar via snap_token tersimpan)
```

### 5.2 Alur Admin
```
Login (role admin) → /admin/dashboard (penjualan bulan/tahun, pending, target bulanan,
                     penjualan per bulan, jumlah produk per kategori)
 → CRUD /admin/categories, /admin/products (upload max 3 gambar, size array),
        /admin/transactions (+ details), /admin/carts, /admin/users
```

### 5.3 Lapisan Keamanan

| Lapisan | Implementasi |
|---|---|
| Autentikasi | Session-based, scaffolding `laravel/ui` (`Auth::routes()`), middleware `auth` |
| Otorisasi admin | Middleware `IsAdmin` (`role !== 'admin'` → redirect `/`) |
| CSRF | `VerifyCsrfToken` aktif; **except kosong** (callback Midtrans aman karena di group `api`, bukan `web`) |
| Rate limit | Group `api` diberi `ThrottleRequests:api`; halaman web **tanpa throttle** |
| Sanctum | Terpasang, **belum dipakai** oleh route API manapun |

---

## 6. Dokumentasi Routes

### 6.1 `routes/web.php`

| Method | URI | Nama | Middleware | Handler |
|---|---|---|---|---|
| GET | `/` | — | — | redirect → `user.home` |
| GET | `/products/search` | `products.search` | — | Admin\]ProductController `search` *(juga dipakai publik)* |
| GET | `/user` | `user.home` | — | `User\HomeController@index` |
| GET | `/user/about` | `user.about.index` | — | `AboutController@index` |
| GET | `/user/products` | `user.products.index` | — | `User\ProductController@index` |
| GET | `/user/products/{id}` | `user.products.show` | — | `User\ProductController@show` |
| GET | `/user/categories` | `user.categories.index` | — | `User\CategoryController@index` |
| GET | `/user/categories/{id}` | `user.categories.products` | — | `User\CategoryController@show` |
| GET | `/user/products/search` | `user.products.search` | — | `ProductController@search` |
| — | **Keranjang** (`auth`) | | | |
| GET | `/user/cart` | `user.cart.index` | auth | `User\CartController@index` |
| POST | `/user/cart/add/{productId}` | `user.cart.add` | auth | `User\CartController@add` |
| PUT | `/user/cart/{cartId}` | `user.cart.update` | auth | `User\CartController@updateCart` |
| DELETE | `/user/cart/{cartId}` | `user.cart.remove` | auth | `User\CartController@removeFromCart` |
| POST | `/user/cart/bulk-remove` | `user.cart.bulkRemove` | auth | `User\CartController@removeSelectedItems` |
| — | **Checkout** (`auth`) | | | |
| GET/POST | `/user/checkout` | `user.checkout.*` | auth | `User\CheckoutController@index/store` |
| POST | `/user/checkout/createTransaction` | — | auth | `User\CheckoutController@createTransaction` |
| — | **Order** (`auth`) | | | |
| GET | `/user/orders/order-history` | `user.order.index` | auth | `User\OrderController@index` |
| GET | `/user/orders/snap-token` | — | auth | `User\OrderController@SnapToken` |
| POST | `/user/orders/payment-success` | — | auth | `User\OrderController@handlePaymentSuccess` |
| — | **Profil & Alamat** (`auth`) | | | |
| GET/PUT | `/user/profile/edit`, `/user/profile/update` | `user.profile.*` | auth | `User\UserController@showEditForm/editUser` |
| POST/GET/POST/DELETE/GET/GET | `/user/address/{save,edit,update,delete,provinces,cities}` | `user.address.*` | auth | `User\UserController@...` |
| — | **Admin** (`auth` + `admin`) | | | |
| GET | `/admin/dashboard` | `admin.dashboard.index` | auth,admin | `DashboardController@index` |
| resource | `/admin/categories` | `admin.categories.*` | auth,admin | `CategoryController` |
| resource | `/admin/products` | `admin.products.*` | auth,admin | `ProductController` |
| resource | `/admin/transactions` | `admin.transactions.*` | auth,admin | `TransactionController` |
| resource | `/admin/transactions-details` | `admin.transactions-details.*` | auth,admin | `TransactionDetailController` |
| resource | `/admin/carts` | `admin.carts.*` | auth,admin | `CartController` |
| resource | `/admin/users` | `admin.users.*` | auth,admin | `UserController` |
| — | **Auth** | | | `Auth::routes()` (login/register/reset/verify + POST logout) |

### 6.2 `routes/api.php` (middleware group `api`)

| Method | URI | Handler | Catatan |
|---|---|---|---|
| GET | `/api/search-destinations` | `RajaOngkirController@searchDestinations` | Proxy autocomplete destinasi |
| POST | `/api/cost` | `RajaOngkirController@cekOngkir` | Hitung ongkir (origin hard-coded `17549`) |
| POST | `/api/midtrans/snap` | `MidtransController@getSnapToken` | Generate Snap Token |
| POST | `/api/midtrans/callback` | `MidtransController@handleMidtransCallback` | Webhook Midtrans (validasi signature) |

---

## 7. Dokumentasi Controllers

### `Api\MidtransController`
- **`getSnapToken(Request)`** — konfigurasi Midtrans langsung via `env()` di runtime; membangun `item_details` dari items frontend + item `shipping` bila ada ongkir; memanggil `Snap::getSnapToken()`. Pada error, **mengembalikan `params` lengkap di response**.
- **`handleMidtransCallback(Request)`** — memvalidasi `signature_key` = `sha512(order_id + status_code + gross_amount + serverKey)`; mencari transaksi by `order_id`; skip bila sudah `settlement`; update status; **restock** sekali untuk `expire/cancel/failure` (dengan cek status lama agar tidak double-restock); update `gross_amount` dari payload callback.

### `Api\RajaOngkirController`
- **`searchDestinations(Request)`** — proxy ke `rajaongkir.komerce.id/api/v1/destination/domestic-destination` (`?search=&limit=20`), log **seluruh body response tiap request**.
- **`cekOngkir(Request)`** — validasi `courier`, `destination`, `weight`; POST ke `calculate/domestic-cost` dengan **origin hard-coded `17549`**; menghasilkan daftar layanan (service, deskripsi, cost ter-format, etd).

### `User\CheckoutController`
- **`index()`** — ambil item cart berdasar query `cartItems` (id,id,...) milik user; hitung total; **parse alamat user via regex format ENGLISH** (`Recipient Name: ..., Phone: ...`).
- **`store()`** — validasi form alamat; simpan sebagai string terformat **English** ke `users.address`; redirect balik ke checkout.
- **`createTransaction(Request)`** — baca `items` dari request (id, quantity, **price**), validasi stok, lalu dalam `DB::beginTransaction()`: buat `Transaction` (order_id/gross_amount/payment_type dari request, status default `pending`), hapus cart, buat `TransactionDetail`, `decrement` stok.

### `User\OrderController`
- **`index()`** — riwayat transaksi user + eager load `details.product`.
- **`SnapToken(Request)`** — kembalikan `snap_token` transaksi by `order_id` (untuk retry pembayaran).
- **`handlePaymentSuccess(Request)`** — update `payment_type` & `transaction_status` transaksi **dari payload client**, tanpa verifikasi Midtrans.

### `User\CartController`
- **`add($productId)`** — validasi `size` (nullable) & `quantity >= 1`; cek stok; cari cart item user+product+size → increment atau create.
- **`index()`** — semua cart user + total kuantitas.
- **`updateCart($cartId)`** — update kuantitas dengan cek stok produk.
- **`removeFromCart($cartId)`** — hapus satu item.
- **`removeSelectedItems()`** — hapus banyak berdasarkan `ids` array.

### `User\UserController` (Profil & Alamat)
- CRUD alamat user dengan **string terformat berbahasa INDONESIA**: `Nama Penerima: ..., No. Telepon: ..., Alamat: ..., Destination: ..., Destination ID: ...`.
- **`editUser()`** — update name/phone/email (email **tanpa validasi `unique`**).
- `showEditAddress()` mem-parse regex format Indonesia.

### `ProductController` (Admin + publik)
- `index` (admin), `store`/`update` (validasi + upload max 3 gambar ke `storage/app/public/products`; nama file = `slug_title-index.ext`), `destroy` (hapus file gambar).
- **`search(Request)`** — publik; LIKE pada title/brand/description; AJAX → JSON `limit(10)`; halaman penuh → paginate(12) view `pages.user.products.search`.

### `DashboardController` (Admin)
- Statistik: pendapatan bulan ini & tahun ini (`status = settlement`), jumlah pending/NULL, `SUM(gross_amount) GROUP BY MONTH()` lintas tahun, jumlah produk per kategori, persentase capai target bulanan (Rp 50 juta, hard-coded).

### Lain-lain
- `HomeController` (root) — dashboard view bawaan, `middleware('auth')` (tidak direferensikan routes web).
- `CategoryController`, `CartController`, `TransactionController`, `TransactionDetailController`, `UserController` (admin) — CRUD standar; admin TransactionController menampilkan `Transaction::all()` tanpa eager load/pagination.

---

## 8. Dokumentasi Models

Model-model **tidak punya cast** kecuali default; `image` & `size` di-decode via **accessor manual**.

| Model | `table` | `fillable` | Relasi | Catatan |
|---|---|---|---|---|
| `User` | — | name, email, password, phone, address | — | `HasApiTokens` siap; `role` **tidak** fillable (aman dari mass-assignment role) |
| `Category` | — | (category_name, description, image) | `products` | — |
| `Product` | — | category_id, brand, title, description, price, size, stock, image, weight | `category` | Accessor `size` & `image` → json_decode |
| `Cart` | `carts` | user_id, product_id, quantity, size | `user`, `product` | Method `totalPrice()` (bukan fatser/cast) |
| `Transaction` | — | order_id, user_id, customer_name, gross_amount, payment_type, courier, courier_service, transaction_status, transaction_time, metadata | `details` | `snap_token` **tidak ada di fillable** → diisi via property assignment |
| `TransactionDetail` | — | transaction_id, product_id, price, quantity, subtotal | `product`, `transaction` | — |

---

## 9. Konfigurasi Environment

Variabel yang **dibaca aplikasi** (lihat `config/midtrans.php`, `config/rajaongkir.php`, controllers):

```env
MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
RAJAONGKIR_API_KEY=
DB_CONNECTION=mysql / DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD
APP_ENV / APP_DEBUG / APP_URL / APP_KEY
```

> ⚠️ **`.env.example` tidak memuat `MIDTRANS_*` dan `RAJAONGKIR_API_KEY`**, dan `MIDTRANS_*` hanya boleh dibaca lewat config (lihat Temuan #T2). Copy manual dari daftar di atas ketika setup.

---

## 10. HASIL AUDIT — Ringkasan Temuan

Total temuan: **24** (5 Kritis, 6 Tinggi, 8 Sedang, 5 Rendah). Skala keparahan: 🔴 Kritis (kerugian finansial/keamanan langsung), 🟠 Tinggi, 🟡 Sedang, ⚪ Rendah.

| # | Severity | Temuan | Lokasi |
|---|---|---|---|
| K1 | 🔴 | Pembayaran bisa "ditandai lunas" dari client tanpa verifikasi (bypass bayar) | `User\OrderController@handlePaymentSuccess` |
| K2 | 🔴 | Harga barang & total transaksi ditentukan frontend → manipulasi harga | `User\CheckoutController@createTransaction`, `Api\MidtransController@getSnapToken` |
| K3 | 🔴 | IDOR: update/hapus keranjang & ambil snap_token tidak diperiksa kepemilikan user | `User\CartController`, `User\OrderController@SnapToken` |
| K4 | 🔴 | Admin menghapus produk → **detail transaksi ikut terhapus** (FK cascade) → riwayat/keuangan hilang | Migrations `transaction_details`, `products`, `categories` |
| K5 | 🔴 | `env()` dipanggil di controller/renderer runtime → rusak setelah `config:cache`, key bocor ke error response | `MidtransController`, `RajaOngkirController` |

| # | Severity | Temuan | Lokasi |
|---|---|---|---|
| T1 | 🟠 | Inkonsistensi format alamat EN vs ID → checkout gagal mem-parse alamat yang disimpan lewat halaman alamat | `CheckoutController@index/store` vs `User\UserController` |
| T2 | 🟠 | Urutan pembayaran terbalik: transaksi DB hanya dibuat **setelah** payment success di JS → callback Midtrans bisa "Transaction not found" | Blade checkout + `createTransaction` |
| T3 | 🟠 | Race condition stok: validasi stok + `decrement` tanpa `lockForUpdate` | `createTransaction` |
| T4 | 🟠 | `gross_amount` ditimpa dari payload callback tanpa dicocokkan dengan nilai tersimpan | `MidtransController@handleMidtransCallback` |
| T5 | 🟠 | Nama file gambar bertabrakan (judul sama → file saling menimpa); nama file tidak acak | `ProductController@generateImageName` |
| T6 | 🟠 | Route Blade `/user/getCitiesByProvince` tidak terdaftar → 404 alur kota; juga duplikasi `products.search` di 2 prefix | `routes/web.php`, blade checkout |

| # | Severity | Temuan | Lokasi |
|---|---|---|---|
| S1 | 🟡 | `where('size', null)` tidak pernah match (`= NULL`) → duplikasi baris cart | `User\CartController@add` |
| S2 | 🟡 | AJAX search menyeleksi kolom `slug` yang tidak ada → SQL error | `ProductController@search` |
| S3 | 🟡 | Email update tanpa `unique`, tanpa verifikasi ulang | `User\UserController@editUser` |
| S4 | 🟡 | `payment_type` / `courier` dari request → DB enum enum error 500 | Migrations + `createTransaction` |
| S5 | 🟡 | Log berlebihan + merespons param lengkap ke client saat error (kebocoran info) | `MidtransController`, `RajaOngkirController` |
| S6 | 🟡 | Validasi lemah: `price` tanpa `min:0`, `stock` boleh negatif, `weight` tanpa unit | ProductController |
| S7 | 🟡 | Kuantitas cart: cek stok di `add` vs `updateCart` tidak konsisten & `$product` bisa null | `User\CartController` |
| S8 | 🟡 | Konfigurasi selalu sandbox `isProduction=false` hard-coded di config & controller | `config/midtrans.php` |

| # | Severity | Temuan | Lokasi |
|---|---|---|---|
| R1 | ⚪ | File sampah di repo: `app.zip`, `storage.zip`, `error_log`, `symlink.php`, `.htaccess` di root | root |
| R2 | ⚪ | Tidak ada seed (role admin) & test hanya `ExampleTest` | `database/seeders`, `tests` |
| R3 | ⚪ | Migrasi pertama bernama `..._create_categories_table (1).php` (spasi + angka) | migrations |
| R4 | ⚪ | `metadata` di fillable `Transaction` tapi tidak ada kolomnya; `snap_token` tidak di fillable | `app/Models/Transaction.php` |
| R5 | ⚪ | N+1 query di admin (list transaksi tanpa eager load) & Dashboard GROUP BY bulan lintas tahun | admin controllers |

---

## 11. Temuan KRITIS (Prioritas 1)

### K1 — Status pembayaran bisa di-set dari client tanpa verifikasi 🔴
**File:** `app/Http/Controllers/User/OrderController.php` (`handlePaymentSuccess`, garis 36–46)

```php
$transaction->payment_type = $request->payment_type;
$transaction->transaction_status = $request->transaction_status; // ← apa pun yang dikirim client
$transaction->save();
```

Route `POST /user/orders/payment-success` menerima `transaction_status` APA PUN dari browser. Resolver cukup kirim `{"order_id": "...", "transaction_status": "settlement"}` via curl (autentikasi hanya login biasa) untuk menandai transaksi lunas — tanpa membayar. Ini **bypass pembayaran penuh**. `SnapToken()` juga tidak mengecek `user_id`, sehingga token transaksi orang lain bisa diambil kapan saja oleh user login mana pun.

**Perbaikan:** hapus route ini; status hanya boleh berubah dari **callback Midtrans terverifikasi signature**. Kalau butuh update sisi client, hanya ke `pending` dan tetap tunggu webhook untuk `settlement`.

### K2 — Harga ditentukan frontend (Price Tampering) 🔴
**File:** `app/Http/Controllers/User/CheckoutController.php` (`createTransaction`, garis 105–209) & `Api/MidtransController.php` (garis 66–77)

```php
$price = (int) $item['price'];          // harga dari request JS
... 'price' => ($item['price'] ?? 0),   // item_details Midtrans dari frontend
$transaction->gross_amount = $request->gross_amount; // total dari frontend
```

Validasi stok sudah benar, tetapi **harga produk tidak dihitung ulang di server**. Resolver bisa mengirim `items: [{id: 5, quantity: 1, price: 1}, ...]` dan `gross_amount` sesuai keinginan; transaksi tersimpan + Snap token dibuat dengan nominal murah. Padahal produk asli dikirim.

**Perbaikan (di `createTransaction`):**
```php
$price = (int) $product->price; // jangan pakai $item['price']
```
Dan di `MidtransController@getSnapToken`, bangun `item_details` **dari database**, bukan dari payload frontend. Bandingkan `sum(item_details×qty) + shipping` dengan `gross_amount` sebelum minta token; jika membentuk → tolak `400`.

### K3 — IDOR di keranjang & snap token 🔴
**File:** `app/Http/Controllers/User/CartController.php` (`updateCart`, `removeFromCart`, `removeSelectedItems`) dan `User\OrderController@SnapToken`

```php
$cart = Cart::find($cartId);            // tidak cek user_id
Cart::whereIn('id', $ids)->delete();    // bisa hapus cart user lain
Transaction::where('order_id', $request->order_id)->first(); // tidak cek user_id
```

User login mana pun bisa `PUT /user/cart/{id}` mengubah kuantitas keranjang orang lain, `DELETE /user/cart/{id}`, atau `POST /user/cart/bulk-remove` dengan id bebas; `snap-token` bisa minta token transaksi user lain.

**Perbaikan:** selalu scope by user — contoh:
```php
$cart = Cart::where('user_id', Auth::id())->find($cartId);
```

### K4 — FK cascade menghapus riwayat transaksi 🔴
**File:** `database/migrations/2024_11_27_000007_create_transaction_details_table.php`, `..._000002_create_products_table.php`

`transaction_details.product_id` dan `products.category_id` memakai `onDelete('cascade')`. Saat admin menghapus produk: **baris `transaction_details` ikut terhapus** → riwayat penjualan, detail belanja, dan angka dashboard rusak/kehilangan data. Menghapus kategori juga menghapus semua produk di bawahnya.

**Perbaikan:** gunakan relasi longgar `->onDelete('restrict')` (atau laravel default *deny*) untuk `product_id`/`category_id`; kalau ingin produk bisa dihapus tetap, gunakan **SoftDeletes** pada `Product` sehingga referensi remain valid.

### K5 — `env()` di runtime — rusak saat config cached 🔴
**File:** `app/Http/Controllers/Api/MidtransController.php` (garis 22–24, 143), `app/Http/Controllers/Api/RajaOngkirController.php` (garis 21, 24, 86)

`env()` hanya bekerja sebelum config di-cache. Setelah deploy produksi menjalankan `php artisan config:cache`, **`env('MIDTRANS_SERVER_KEY')` mengembalikan null** → semua pembayaran gagal ("ServerKey is missing"). Padahal config `config/midtrans.php` dan `config/rajaongkir.php` **sudah ada tapi tidak pernah dipakai** (controller memanggil `env()` langsung).

**Perbaikan:**
```php
Config::$serverKey = config('midtrans.server_key');
// RajaOngkir: config('rajaongkir.api_key')
```
**Plus:** di `getSnapToken`, blok `catch` mengembalikan `params` lengkap ke client (garis 125–128) — jangan: cukup `Log::error(...)`, response hanya pesan generik. Callback di garis 143 juga pakai `env()` → sama. Jangan hard-code `Config::$isProduction = false` di controller; ambil dari `config('midtrans.is_production')` dan dievaluasi dari env di config.

---

## 12. Temuan TINGGI (Prioritas 2)

### T1 — Dua format alamat berbeda (EN vs ID) 🟠
**File:** `User\CheckoutController@index/store` vs `User\UserController`

- Checkout menyimpan & mem-parse: `Recipient Name: %s, Phone: %s, Address: %s, Destination: %s, Destination ID: %s`
- UserController menyimpan & mem-parse: `Nama Penerima: %s, No. Telepon: %s, Alamat: %s, Destination: %s, Destination ID: %s`

Alamat yang disimpan lewat halaman `/user/address` **tidak bisa di-parse** oleh `CheckoutController@index` (regex English tidak cocok) → nilai checkout dikosongkan (`null` semua) meski alamat sudah diisi. Pendekatan "string terformat + regex" juga rapuh (koma di alamat user memecah parsing).

**Perbaikan:** simpan alamat sebagai kolom/tabel terstruktur — idealnya tabel `addresses` (atau minimal JSON dengan cast):
```php
// contoh minimal dan aman:
$user->address = json_encode([...], JSON_UNESCAPED_UNICODE);
$user->casts: ['address' => 'array'];
```

### T2 — Urutan pembayaran terbalik 🟠
**File:** `resources/views/pages/user/checkout/index.blade.php` (garis ~787–890), `User\CheckoutController@createTransaction`

Saat ini `snap.pay()` dipanggil dulu; **barulah setelah `onSuccess`** JS memanggil `createTransaction` untuk menyimpan transaksi. Konsekuensi:
1. Webhook `/api/midtrans/callback` bisa tiba **sebelum** transaksi dibuat → `Transaction not found` (Midtrans menganggap gagal, retry berhenti).
2. Bila JS gagal mati (network/tutup tab) setelah pembayaran sukses → **uang masuk tapi tidak ada transaksi di database**.
3. `SnapToken()` (retry bayar) hanya bisa dipakai untuk transaksi yang sudah ada di DB.

**Perbaikan:** buat transaksi (status `pending`) + kurangi stok **sebelum** memanggil Midtrans; Snap token ikut disimpan; `onSuccess` hanya menandai "menunggu webhook". Ini juga otomatis menyinkronkan `order_id`.

### T3 — Race condition stok 🟠
**File:** `User\CheckoutController@createTransaction` (garis 122–200)

Validasi stok dilakukan dulu, `decrement` belakangan. Dua request bersamaan dengan stok 1 → keduanya lolos validasi → stok minus / oversell.

**Perbaikan:** baca produk dengan lock dalam transaction:
```php
$products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
```

### T4 — `gross_amount` di-timpa dari payload callback 🟠
**File:** `Api\MidtransController@handleMidtransCallback` (garis 220)

Nilai `gross_amount` dari Midtrans ditulis ke transaksi tanpa dicocokkan dengan nilai yang tersimpan/seharusnya. Signature valid hanya membuktikan notifikasi dari Midtrans, bukan bahwa nominal sesuai yang ditandatangani sebelumnya. Sisi aman: simpan `gross_amount` saat order dibuat di server (lihat K2) dan pada callback bandingkan; bila beda →警惕 & log untuk investigasi, jangan overwrite.

### T5 — Nama file gambar produk tidak unik 🟠
**File:** `ProductController@generateImageName` (garis 133–137)

Nama file = `slug_judul-index.ext`. Dua produk berbeda dengan judul sama → gambar di `storage/app/public/products/` **saling menimpa**. Sudah dirampungkan dengan meng-hitung `$index` di pemanggil, tapi itu index ke-1..3 dalam satu upload, bukan penghitung produk.

**Perbaikan:** pakai nama unik —
```php
return $titleSlug.'-'.Str::random(10).'-'.$index.'.'.$image->getClientOriginalExtension();
```
atau `$image->hashName()` dengan `storeAs`.

### T6 — Route referenced tapi tidak ada + duplikasi route 🟠
**File:** `resources/views/pages/user/checkout/index.blade.php` (garis 618), `routes/web.php` (garis 35, 59)

1. Blade mem-fetch `/user/getCitiesByProvince` — tidak ada route dengan URI tersebut (yang ada `GET /api/...` & `/user/address/{provinces,cities}`) → alur pilih kota gagal 404.
2. Route `products.search` didaftarkan **dua kali** dengan prefix berbeda (`/products/search` dan `/user/products/search`) — tidak error, tapi dua sisi yang pakai view bisa pakai base yang berbeda; disarankan cukup satu.

---

## 13. Temuan SEDANG (Prioritas 3)

### S1 — `where('size', null)` tidak pernah match
`User\CartController@add` (garis 29–32): `where('size', $size)` dengan `$size = null` menghasilkan `WHERE size = NULL` yang di SQL selalu FALSE → item tanpa size **selalu dibuat baris baru** (interval duplikasi cart di UI).

```php
$query = Cart::where('user_id', $user->id)->where('product_id', $product->id);
$size ? $query->where('size', $size) : $query->whereNull('size');
```

### S2 — AJAX search memilih kolom yang tidak ada
`ProductController@search` (garis 157–159): `get(['id','title','brand','price','slug'])` — **kolom `slug` tidak ada** di migration products → setiap AJAX autocomplete = SQL error. Hapus `slug` dari select.

### S3 — Email profile tanpa validasi `unique`
`User\UserController@editUser` (garis 135–150): dua user bisa punya email sama (login jadi kacau karena email unik saat register tapi bebas saat edit). Tambahkan `Rule::unique('users')->ignore($user->id)`.

### S4 — `payment_type` / `courier` bisa menembus enum DB
`User\CheckoutController@createTransaction` men-streaming `$request->payment_type` langsung ke kolom enum. Response Midtrans bisa mengirim nilai tak dikenal (mis. `other`) → **insert gagal, DB error 500** (dalam transaction, jadi di-rollback). Ubah kolom ke `string`/`nullable` atau map nilai Midtrans ke enum internal.

### S5 — Logging berlebihan & kebocoran info ke client
- `RajaOngkirController`: **seluruh `body()` response** di-log tiap request (garis 32) — log membengkak.
- `MidtransController@getSnapToken` mengembalikan `params` lengkap ke browser saat error (garis 127).
- `createTransaction` mengembalikan `$e->getMessage()` mentah ke client (garis 220) — bocor detail internal (path/kelas). Kembalikan pesan generik; detail hanya ke log.

### S6 — Validasi numerik lemah
`ProductController@store/update`: `price` tanpa `min:0`, `stock` tanpa `min:0`, `weight` tanpa unit (gram?) & `min:0` saja; harga bisa negatif.

### S7 — Cart: null-safety tidak konsisten
- `updateCart` (`CartController`): `$product = $cart->product;` bisa null (produk terhapus / cascade) → `if ($request->quantity > $product->stock)` fatal error.
- `add()`: cek stok hanya `product->stock >= quantity` — tidak memperhitungkan kuantitas yang sudah di keranjang; user bisa menumpuk qty melebihi stok.

### S8 — Mode produksi Midtrans hard-code
`config/midtrans.php` **dan** controller memaksa `is_production = false`. Akibatnya bahkan di server produksi, pembayaran selalu diarahkan ke sandbox Midtrans. Kaitan erat dengan K5: ambil dari config.

---

## 14. Temuan RENDAH (Prioritas 4)

| # | Temuan | Perbaikan |
|---|---|---|
| R1 | `app.zip`, `storage.zip`, `error_log`, `symlink.php`, `.htaccess` (root) ter-commit — duplikasi source (app.zip!), log debugging, dan file util transport hosting | Hapus dari git & tambahkan ke `.gitignore` (`*.zip`, `error_log*`); `symlink.php` sebaiknya hanya deployment hook tanpa echo info |
| R2 | Tidak ada seeder admin/role; test hanya ExampleTest | Seeder `AdminSeeder` (role admin) + feature test penting: checkout K2, cart IDOR K3 |
| R3 | `2024_11_27_000001_create_categories_table (1).php` nama file mengandung spasi & "(1)" — hasil copy duplikat | Rename ke nama bersih tanpa spasi |
| R4 | `Transaction::$fillable` memuat `metadata` (kolom tidak ada) dan **tidak** memuat `snap_token` (dipakai via properti) | Sinkronkan fillable dengan kolom migration |
| R5 | Admin list `Transaction::all()` tanpa pagination/eager load; Dashboard `GROUP BY MONTH()` menggabungkan data antar-tahun ke satu bulan; `orWhereNull` tanpa closure bisa salah grouping jika kapanya ada where lain | `->with(...)->paginate(...)`; `GROUP BY YEAR(), MONTH()`; bungkus where dalam `->where(function($q){...})` |

**Catatan tambahan keamanan (ringan tapi perlu):**
- `snap_token` disimpan plaintext pada `transactions` — hanya tampil di view order user sendiri; masih acceptable untuk kasus Snap public token, tapi jangan pernah expose lewat API publik.
- Route web tanpa per-route throttle (rate limit) — vulnerability brute force pada search/reviews saja tidak fatal, tapi `POST /user/checkout/createTransaction` bisa spam.

---

## 15. Rekomendasi Contoh Perbaikan

### 15.1 Pindahkan semua kredensial ke config
```php
// app/Http/Controllers/Api/MidtransController.php — hapus env() langsung
use Illuminate\Support\Facades\Config as AppConfig;

Config::$serverKey    = config('midtrans.server_key');
Config::$clientKey    = config('midtrans.client_key');
Config::$isProduction = config('midtrans.is_production');
Config::$isSanitized  = config('midtrans.is_sanitized');
Config::$is3ds        = config('midtrans.is_3ds');
```

### 15.2 Server-authoritative checkout (`createTransaction`)
```php
public function createTransaction(Request $request)
{
    $items = $request->input('items', []);
    if (empty($items) || !is_array($items)) {
        return response()->json(['success' => false, 'message' => 'Items tidak valid.'], 422);
    }

    $productIds = collect($items)->pluck('id')->unique()->values();

    DB::beginTransaction();
    try {
        // 1. LOCK produk agar tidak race (T3)
        $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

        $gross = 0;
        $lines = [];
        foreach ($items as $item) {
            $product = $products->get($item['id'] ?? null);
            $qty = (int) ($item['quantity'] ?? 0);

            if (!$product) throw new \Exception("Produk tidak ditemukan.", 404);
            if ($qty <= 0) throw new \Exception("Kuantitas tidak valid.", 422);
            if ($product->stock < $qty) throw new \Exception("Stok '{$product->title}' tidak cukup.", 422);

            $price = (int) $product->price;   // ← K2: harga dari DB, bukan dari client!
            $gross += $price * $qty;

            $lines[] = [$product, $qty, $price];
        }

        // 2. Gross amount harus variabel yang dihitung server (K2/K4)
        $transaction = Transaction::create([
            'order_id'           => $request->order_id,
            'user_id'            => Auth::id(),
            'customer_name'      => $request->customer_name,
            'gross_amount'       => $gross,     // server-computed
            // payment_type diberi default 'bank_transfer' atau simpan string (S4)
            'transaction_status' => 'pending',  // selalu mulai dari pending
            'transaction_time'   => now(),
        ]);

        foreach ($lines as [$product, $qty, $price]) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $product->id,
                'quantity'       => $qty,
                'price'          => $price,
                'subtotal'       => $qty * $price,
            ]);
            Product::where('id', $product->id)->decrement('stock', $qty);
        }

        Cart::where('user_id', Auth::id())->whereIn('product_id', $productIds)->delete();

        DB::commit();
        return response()->json(['success' => true, 'order_id' => $transaction->order_id]);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('createTransaction: '.$e->getMessage());
        return response()->json(
            ['success' => false, 'message' => 'Gagal membuat transaksi.'],
            $e->getCode() >= 400 && $e->getCode() < 500 ? $e->getCode() : 500,
        );
    }
}
```

### 15.3 Perbaiki IDOR (K3)
```php
// User\CartController — semua query di-scope user_id
public function updateCart(Request $request, $cartId)
{
    $validated = $request->validate(['quantity' => 'required|integer|min:1']);

    $cart = Cart::where('user_id', Auth::id())->with('product')->find($cartId);
    if (!$cart || !$cart->product) {
        return response()->json(['success' => false, 'message' => 'Cart tidak ditemukan.'], 404);
    }
    if ($validated['quantity'] > $cart->product->stock) {
        return response()->json(['success' => false, 'message' => 'Stok tidak cukup.']);
    }
    $cart->update(['quantity' => $validated['quantity']]);
    return response()->json(['success' => true]);
}

public function removeFromCart($cartId)
{
    Cart::where('user_id', Auth::id())->where('id', $cartId)->delete();
    return redirect()->route('user.cart.index');
}

public function removeSelectedItems(Request $request)
{
    $ids = (array) $request->input('ids', []);
    Cart::where('user_id', Auth::id())->whereIn('id', $ids)->delete();
    return response()->json(['success' => true]);
}

// User\OrderController::SnapToken — verifikasi kepemilikan
$transaction = Transaction::where('user_id', Auth::id())
    ->where('order_id', $request->order_id)->first();
```

### 15.4 Hapus `handlePaymentSuccess` (K1)
Status hanya boleh di-update oleh:
1. **`/api/midtrans/callback`** — verifikasi signature (sudah ada),
2. atau pengecekan **Midtrans API status** (Snap `Status::status($orderId)`) jika ingin sinkron langsung setelah `snap.pay()` sukses di browser.

```php
// Alternatif aman bila tetap butuh Endpoint status,
// verifikasi ke Midtrans terlebih dahulu:
use Midtrans\Transaction as MidtransTransaction;
$status = MidtransTransaction::status($request->order_id);

Transaction::where('user_id', Auth::id())
    ->where('order_id', $request->order_id)
    ->update(['transaction_status' => $status->transaction_status]);
```

### 15.5 FK yang aman (K4)
```php
// products
$table->foreignId('category_id')->constrained('categories')->onDelete('restrict');

// transaction_details
$table->foreignId('product_id')->constrained('products')->onDelete('restrict');
```
Konsisten dengan itu: di `ProductController@destroy` tambahkan guard:
```php
if ($product->transactionDetails()->exists()) {
    return redirect()->back()->with('error', 'Produk sudah pernah dijual, hanya bisa dinonaktifkan.');
}
```

---

## 16. Checklist Remediasi

Urutan pengerjaan yang disarankan (sprint 1–3):

### 🔴 Sprint 1 — Finance & Keamanan (Selesai ✅)
- [x] **K1**: Hapus/strengthen `POST /user/orders/payment-success`; status diverifikasi langsung ke Midtrans API & callback signature
- [x] **K2**: Hitung ulang `price` & `gross_amount` di server (`createTransaction` & `MidtransController@getSnapToken`)
- [x] **K3**: Tambah `user_id` filter untuk SEMUA mutasi cart & query transaksi (`CartController`, `OrderController@SnapToken`)
- [x] **K5**: Ganti semua `env()` di controller/blade dengan `config()` (`MidtransController`, `RajaOngkirController`, `main.blade.php`, `user.blade.php`)
- [x] Hapus `params`/`$e->getMessage()` dari response error ke client (aman dari kebocoran info)

### 🟠 Sprint 2 — Konsistensi Bisnis (Selesai ✅)
- [x] **K4**: Guard hapus produk di `ProductController@destroy` bila sudah memiliki riwayat penjualan
- [x] **T1**: Satukan format alamat via `AddressFormatter` (mendukung canonical ID & legacy EN)
- [x] **T2**: Fixed array destructuring bug & route name di `createTransaction`
- [x] **T3**: `lockForUpdate` di `createTransaction`
- [x] **T4**: Cocokkan `gross_amount` callback vs tersimpan; jangan overwrite
- [x] **T5**: Nama file gambar unik (`Str::slug` + `Str::random(8)`)
- [x] **T6**: Hapus dead code `getCitiesByProvince`; satukan route search dengan redirect
- [x] **S4**: `payment_type`/`courier` dinormalisasi ke nilai valid

### 🟡 Sprint 3 — Kebersihan & UX (Selesai ✅)
- [x] **S1** `whereNull('size')`; **S2** hapus `slug` dari select live search; **S3** unique email di editUser
- [x] **S5–S8** validasi numerik `min:0`; null-safety cart & order history; konfigurasi `is_production` dinamis
- [x] **R1** bersihkan `*.zip`, `error_log`, `symlink.php` dari git tracking & tambahkan ke `.gitignore`
- [x] **R2** `DatabaseSeeder` dibuat dengan akun default admin & user demo
- [x] **R4** sinkron `$fillable` Transaction (tambah `snap_token`, hapus `metadata`)
- [x] **R5** eager loading & ordering admin transactions; perbaiki Dashboard groupBy tahun & filter settlement
- [x] **Fitur & Tampilan Tambahan**: Sistem Review & Rating produk (Review model, migration, controller, review list, rating summary, star-picker form, UI/CSS polish)

---

*Seluruh referensi garis mengacu pada kode saat audit (commit terakhir `9a3d306`).*

---

## CATATAN RUN (3 Nov 2026) — deployment & fix terbaru

### Permasalahan performa (resolved)
- Menjalankan Laravel dari `/mnt/d` (drive Windows via WSL, filesystem 9p) membuat
  bootsrap tiap request butuh 7–17 detik — hampir semuanya menunggu disk, bukan CPU
  (OPcache pun aktif, tetap lambat).
- Solusi: code di-serve dari mirror ext4 cepat `/tmp/opencode/ecom` (rsync dari repo),
  sementara **database + logs tetap di `/mnt/d/e-commerce`** (path absolut itu tertanam
  di `bootstrap/cache/config.php`, jadi sumber data tetap satu).
- Server demo berjalan di **http://127.0.0.1:8000** (8 worker + OPcache, ±0.3s/request).

### Cara restart server demo
```bash
rsync -a --exclude .git --delete /mnt/d/e-commerce/ /tmp/opencode/ecom/
cd /tmp/opencode/ecom && PHP_CLI_SERVER_WORKERS=8 \
  php -d error_reporting=24575 -d opcache.enable_cli=1 -d opcache.validate_timestamps=0 \
  artisan serve --host=127.0.0.1 --port=8000
```
Catatan: setiap kali file blade/CSS di `/mnt/d` diedit:
- CSS/asset: cukup rsync ulang (asset dibaca live).
- PHP/php kode: rsync ulang **dan** restart/reload server (opcache `validate_timestamps=0`
  tidak membaca ulang file, mtime tidak dipakai).
- Ganti config (.env): `php artisan config:cache` lalu rsync + restart.

### Fix fungsional kali ini
1. **Navbar (desktop & mobile)**:
   - Link About mobile kosong `href=""` → diarahkan ke `user.about.index`; item Blog &
     Contact dead-link dihapus dari menu mobile.
   - ID `logout-form` duplikat antara navbar & mobile-nav → mobile memakai
     `logout-form-m` sendiri.
   - Greeting "Hi {nama}" memakai class `.mtext-50` yang **tidak ada di CSS mana pun**
     (tampil patah tanpa style) → sekarang class `.nav-greeting` (13.5px, semibold,
     tersembunyi di layar kecil).
2. **Harmonisasi warna tombol** — palet disatukan ke indigo di seluruh halaman:
   - `main.css`: `--primary: #4F46E5`, `--primary-hover: #4338CA` (sementara sebelumnya
     copper `#A66B2B`) → `.bg1`, `hov-btn*`, `active-menu`, dsb. mengikuti satu aksen.
   - `theme-upgrade.css`: tambahan `.tu-btn-outline` (tombol Register berbentuk outline
     yang match dengan tombol Sign In solid), shadow konsisten di `.bg1`,
     `accent-color` checkbox cart cart → indigo (tadi masih `#717fe0`).
   - Inline style `.dropdown-item:active` di navbar copper → indigo.
   - Palet admin (`admin-upgrade.css`) sudah memakai `#4f46e5` — kini seragam dengan
     user-side.
3. **Konfigurasi demo**: `MAIL_MAILER` smtp (mailpit tidak ada) → log; forgot-password
   memang error 500 karena koneksi SMTP. Kini link reset muncul di `storage/logs/laravel.log`.

### Fix navbar "ikut ke bawah / space kosong di atas navbar" saat scroll (3 Nov 2026, revisi 2)
**Akar masalah:** dua hal komplit:
1. Template memakai `position: fixed` + JS (`main.js`) yang memanipulasi `top` inline
   saat scroll + transisi `all .3s` + shrink height 84→70px (class `fix-menu-desktop`)
   → navbar "ngikut" dan menimbulkan gap.
2. Plugin **animsition** (page-transition jQuery) menyetel `opacity`/`transform`
   inline di `<body>` (CSS awalnya `opacity: 0`) — sisa style yang nyangkut membuat
   elemen `fixed` ikut page scroll (navbar turun bersama konten).

**Solusi permanen:**
- Navbar desktop diganti ke **sticky header murni CSS** (`header.header-v4 { position: sticky; top: 0; height: 84px }`), semua mekanisme fixed/JS/`fix-menu-desktop` dinetralkan via CSS (`position: static !important` dll.) — tanpa JS scroll.
- `body.animsition` dinetralkan (`opacity:1; transform:none; animation:none !important`).
- Semua margin-top kompensasi (70–120px) di halaman products/search/categories/category-show/about/cart/order/profile/address dihapus → diseragamkan 24px (header kini in-flow).
- Transisi halaman (`tuPage`) hanya pada `main` (bukan `#content`) supaya transform tidak merusak sticky.
- `header.header-v4` disembunyikan di < 992px (mobile supaya tidak muncul bar putih 84px; mobile tetap memakai wrap-header-mobile).
- Polish navbar: logo "DRVN." wordmark (spasi letter 5px + titik indigo), link menu
  14.5px/medium + warna state, hover ikon aksen, badge cart pill indigo,
  `scroll-padding-top: 92px` untuk anchor.

### Fix dropdown akun & logout (3 Nov 2026, revisi 3)
**Akar masalah:** dropdown akun bergantung pada `data-toggle="dropdown"` Bootstrap 4
(popper.js) yang bertabrakan dengan plugin bawaan template, sedangkan menu
Keluar mengandalkan **modal** `data-target="#logoutModal"` yang sering gagal terpasang —
menghalangi pengguna untuk tidak ke mana-mana.

**Solusi:**
1. Dropdown akun kini **mandiri (Popper/jQuery/modal-free)**:
   - Toggle memakai atribut `data-tu-dropdown`; CSS menampilkan menu via class
     `.show` + posisi murni (`.tu-dd { position: relative }` + `.tu-dd .dropdown-menu.show`).
   - Satu script pendek di `navbar.blade.php` (pakai *event delegation*, jadi otomatis
     berlaku juga untuk partial mobile-nav) → klik ikon membuka/menutup, klik di luar
     atau Escape menutup, dan tidak ada lagi konflik double-toggle.
   - Desain menu: panel putih satu-satunya min-width 250px, header nama+email,
     itemnya bergengsi 13.5px dengan ikon; item **Keluar** berwarna merah saat hover.
2. **Logout pasti berfungsi**: tidak melalui modal lagi — item Keluar langsung
   melakukan submit form POST `/logout` (`#logout-form`, berisi token @csrf) via
   `onclick`. Markup modal lama dihapus (tidak ada lagi `#logoutModal` di DOM).
3. Duplikat id `userDropdown` desktop vs mobile dibedakan (`userDropdown` /
   `userDropdownM`), masing-masing form logout terpisah (`logout-form` / `logout-form-m`).
4. Uji end-to-end: login 302 → POST /logout 302 → sesi habis (cart redirect ke login) ✓.
