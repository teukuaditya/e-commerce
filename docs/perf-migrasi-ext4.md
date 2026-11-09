# Fix "berpindah page terasa berat & lama"

## Diagnosa (7 Nov 2026)
- TTFB (time-to-first-byte) via localhost: **1–9 detik** per request — bukan masalah asset (semua static ~0s), bukan masalah query (query count sudah 0–3).
- Pengukuran boot Laravel:
  | Lokasi proyek | Boot framework |
  |---|---|
  | `/mnt/d/e-commerce` (NTFS via WSL/9P) | **6.500 ms** |
  | `~/projects/e-commerce` (ext4 WSL) | **99–323 ms** |
- Biaya CPU kecil; hampir semuanya waktu tunggu syscall per file dari drive Windows. Opcache & realpath cache tidak menolong karena kendala per *stat/open*, bukan kompilasi.

## Yang dipindahkan
Seluruh proyek (termasuk `.git`, `.env`, `database/database.sqlite`, `storage`) dipindah ke **`~/projects/e-commerce`**. Salinan lama di `D:\e-commerce` (WSL: `/mnt/d/e-commerce`) tinggal sebagai backup — jangan diedit lagi.

Server dev sekarang berjalan dari lokasi baru: `http://127.0.0.1:8000` TTFB **40–80 ms**.

## Cara menjalankan server dev
```bash
cd ~/projects/e-commerce
mkdir -p /tmp/op/opcache-ext4
PHP_CLI_SERVER_WORKERS=4 php \
  -d opcache.enable_cli=1 \
  -d opcache.file_cache=/tmp/op/opcache-ext4 \
  -d realpath_cache_size=40M \
  -S 0.0.0.0:8000 -t public public/dev-router.php
```
- `-t public` WAJIB — dalam speech docroot = folder statis (css/js/gambar di-serve benar). Kalau lupa flag ini semua asset 404.
- `public/dev-router.php` = router `php -S` + serve static file; memungkinkan opcache CLI aktif.
- `PHP_CLI_SERVER_WORKERS=4` = 4 worker (requests tidak antre saat browser memuat aset paralel).

## Cara mengakses/mengedit file dari Windows
- VS Code + ekstensi **WSL**: `Remote-WSL: Open Folder -> ~/projects/e-commerce` (paling enak).
- Di Explorer: `\\wsl$\<distro>\home\adit\projects\e-commerce`.

## Berguna juga
- Test suite di ext4: **24.5s → 2.1s**; `php artisan test` sekarang nyaris instan.
- JS berat dibuat kondisional dari `layouts/main.blade.php`:
  - slick (+ slick-custom) → hanya halaman **detail produk**.
  - isotope → hanya halaman **search** & **kategori**.
  → halaman lain kosongkan parse ~150KB JS. Home (hero crossfade mandiri) tidak memuat keduanya.
