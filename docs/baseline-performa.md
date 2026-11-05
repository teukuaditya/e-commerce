# BASELINE PERFORMA — SEBELUM REFACTOR
Diukur: 5 Nov 2026 · Laravel 10.48.22 · PHP 8.5.4 · DB sqlite (dev)

## 1. Query count + waktu render per halaman (mesin test terisolasi)

| Halaman | Query | Render |
|---|---|---|
| `/user` (home) | 15 | 120–152 ms |
| `/user/about` | 5 | 71 ms |
| `/user/products` | 9 | 71–81 ms |
| `/user/categories` | 1 | 54 ms |
| `/login` | 0 | 616 ms (compile pertama) |
| `/admin/dashboard` | 11 | 189 ms |
| `/admin/categories` | 5 | 51 ms |
| `/admin/products` | 16 | 68–156 ms |
| `/admin/users` | 5 | 51 ms |
| `/admin/carts` | 7 | 53–55 ms |
| `/admin/transactions` | 5 | 50 ms |
| `/admin/transactions-details` | 5 | 55 ms |

Query yang boros (ditemukan via dump SQL):
- `sum(quantity) from carts` (view composer cache) **berulang 3–6× per request** →
  composer `view()->composer('*', …)` dijalankan untuk setiap view yang di-render (layout + tiap `@include`).
- `select * from categories where id = 2 limit 1` **berulang 7–10× di /user & /admin/products** →
  N+1 dari `$product->category` tanpa eager loading.
- `/admin/carts` memuat `select * from users` dan `select * from products` **tanpa filter** (full table).

## 2. Spikes Via artisan serve (curl)
Frekuensi: ~1 dari 3–5 request menabrak +1.5–2.5s TTFB — pola acak antar halaman.
Tersangka utama **I/O disk drive NTFS** (`/mnt/d`, project di drive Windows via WSL):
semua write session/cache/compiled-view melewati izin file Windows.
→ Di server Linux/production tidak terjadi.

## 3. Asset frontend halaman user (per HIKE full reload)

| Sumber | Ukuran |
|---|---|
| jQuery 3.2.1 + Bootstrap 4 + popper | ~88 + 86 + 24 KB |
| **jQuery-UI via CDN (CSS + JS)** | **~330 KB** |
| FontAwesome **4.7 lokal** + **6.0-beta CDN** (doble) | ~89 KB + 170 KB |
| select2 + daterangepicker + moment + slick + MagnificPopup + perfect-scrollbar + parallax + isotope + sweetalert + animsition | ~450 KB |
| Midtrans `snap.js` (dimuat di SEMUA halaman) | ~100 KB |
| animsition: fade-out 800–1500ms ANIMASI + loader di setiap pindah halaman | tambahdeley UX besar |
| `public/vendor/telescope` 20MB (bukak public tapi ada di disk) | unused |

Total ~2.3 MB+ per page utk "halaman produk kategori ATAU About Life".

## 4. Ada di dalam AppBundle? nope.

## 5. Test suite
- Sebelum tahap ini: hanya `ExampleTest` + `AddressFormatterTest` (4 pass, 1 fail karena saling previouss).
- Sesudah: `17 tests / 59 assertions PASS` (HomeSmokeTest, AuthFlowTest, AdminCrudTest).
- DB test dipisah: `database/testing.sqlite` (copy dev) + `DatabaseTransactions` → dev data tidak tersentuh.

## 6. Lingkungan
- Debugbar terpasang tapi `DEBUGBAR_ENABLED=false` → tidak berjalan.
- Telescope sudah dimatikan di provider (kandidat hapus paket di Tahap 6).
- Vite terpasang tapi TIDAK dipakai (semua layout pakai `asset()` langsung, `public/build` belum dibangun).
- CACHE_DRIVER, SESSION_DRIVER: file (dev) — untuk produksi pindah ke database/redis via rekomendasi.
