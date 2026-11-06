# HASIL REFACTOR — SETELAH (dibanding baseline, dibaca terpisah: docs/baseline-performa.md)
Diuji: 6 Nov 2026 · Laravel 10.48.22 · PHP 8.5.4 · DB sqlite (dev) · metodologi sama (Uji query terisolasi)

## 1. Query + waktu render per halaman (pass hangat, mesin Uji)

| Halaman | Query (dulu) | Query | Render dulu | Render |
|---|---|---|---|---|
| `/user` (home) | 15 | 0 (cache) / 2 (cold) | 120–152 ms | 69–72 ms |
| `/user/about` | 5 | 5 | 71 ms | 61 ms |
| `/user/products` | 9 | 3 | 71–81 ms | 72 ms |
| `/user/categories` | 1 | 1 | 54 ms | 64 ms |
| `/login` | 0 | 0 | 616* | 17 ms |
| `/admin/dashboard` | 11 | 7 | 189 ms | 38–46 ms |
| `/admin/categories` | 5 | 1 | 51 ms | 54 ms |
| `/admin/products` | 16 | 3 | 68–156 ms | 57 ms |
| `/admin/users` | 5 | 1 | 51 ms | 50 ms |
| `/admin/carts` | 7 (+2 full-table boros) | 3 | 53–55 ms | 44 ms |
| `/admin/transactions` | 5 | 1 | 50 ms | 44 ms |
| `/admin/transactions-details` | 5 | 1 | 55 ms | 43 ms |
| `/user/cart` | 14 | 1 | – | 54–67 ms |

`*` 616 ms pada /login adalah kompilasi view pertama (dev, tanpa view cache) — sudah tidak relevan dengan view cache.

Hal yang dieliminasi sejak Tahap 2–4:
- Composer `view()->composer('*')` → memoized sekali per-request & dicache.
- `view()->share('products', Product::all())` → dihapus diganti cache `home.products` + invalidasi model event.
- N+1 `$product->category` → `with('category')` di semua halaman yang domain: home, admin products, user kategori-show, search.
- Dashboard: ADMIN aggregasi SQL satu-kali (GROUP BY bulan) menggantikan perulangan PHP.
- 6 indeks baru: carts.user_id, products.category_id, transactions.transaction_status, transactions.transaction_time, transaction_details.transaction_id, transaction_details.product_id.

## 2. Berat aset halaman user

| Halaman | Dulu | Sekarang |
|---|---|---|
| `/user` | ~2.3 MB | ~770 KB lokal + Google Fonts Poppins |
| `/login` | ~2.3 MB | ~770 KB |
| `/admin/users` | – | ~489 KB (datatables+chart) |

Dihapus dari HALAMAN SEMUA:
- jQuery-UI CDN (CSS+JS ~330 KB) → sisa address autocomplete masih via CDN jquery-ui (dimuat HANYA di halaman address).
- FontAwesome 4.7 lokal + 6.0-beta CDN (doble) → FA5 lokal (`libs/fontawesome-free`).
- snap.js Midtrans (global) → @push hanya di checkout + order history.
- daterangepicker, moment, MagnificPopup, perfect-scrollbar, parallax, sweetalert — keluar dari layout global.

## 3. TAHAP 6–7 (refactor struktur)
- Untuk FormRequest 10x di `app/Http/Requests/` menggantikan 16 lokasi inline validate.
- Route model binding di 7 titik CRUD admin.
- Dihilangkan utama `/user/address/provinces` + `/cities` (selalu 500, method tidak ada).
- PSR-12 via Pint semua `app/`, `routes/`, `tests/` (180 file).
- Komponen Blade `<x-flash-alert>` memang menggantikan 5 blok alert duplikat.
- CSS Poppins + JS navbar/auth di-extract ke file (main.blade −85 baris; navbar −52; login/register −2×11).
- Layout yatim `layouts/user.blade.php` dihapus; 10 vendor yatim (~4.3 MB) dihapus.

## 4. Test suite
- `17 tests / 59 assertions PASS` (HomeSmokeTest, AuthFlowTest, AdminCrudTest).
- Pint: 180 file, **0 error PSR-12**.
- Rute: 72 entri (`route:list`).
- storage/logs: tidak ada ERROR aplikasi nyata (hanya noise deprecation PHP 8.5 saat menjalankan `artisan`).

## 5. Ke dalam produksi (rekomendasi)
- Set `OPCACHE` + `php artisan optimize` di deploy.
- CACHE_DRIVER & SESSION_DRIVER -> database / redis.
- Perhatikan I/O NTFS (dev WSL) tidak sama dengan server Linux: spike acak 1.5–2.5s tidak muncul di produksi.
- Poppins bermuatan Google Fonts CDN — idealnya self-host (waktu bersemuka LCP + budget re-flow sama).
