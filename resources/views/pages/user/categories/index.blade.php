@extends('layouts.main')

@section('title', 'Kategori | DRVN')

@section('content')
<div class="cat-page">

    {{-- ============================================================
         1. HERO HEADER  [BARU]
         ============================================================ --}}
    <header class="cat-hero">
        <div class="cat-container">
            <p class="cat-hero__eyebrow cat-hero-anim" style="--d: 0ms">
                DRVN &mdash; Streetwear &amp; Sportswear
            </p>
            <h1 class="cat-hero__title cat-hero-anim" style="--d: 90ms">
                Shop by Category
            </h1>
            <p class="cat-hero__sub cat-hero-anim" style="--d: 180ms">
                Dirancang untuk bergerak. Pilih gayamu.
            </p>
        </div>
    </header>

    {{-- ============================================================
         2. CARDS KATEGORI  (hover LAMA tidak diubah)
         1 kolom mobile / 2 tablet / 3 desktop
         ============================================================ --}}
    <section class="cat-section cat-section--tight">
        <div class="cat-container">
            <div class="cat-grid">
                @foreach ($categories as $category)
                    <x-category-card
                        :category="$category"
                        :position="$loop->index"
                        :delay="$loop->index * 100" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         3a. NEW ARRIVALS  [BARU]
         ============================================================ --}}
    <section class="cat-section">
        <div class="cat-container">
            <div class="cat-sec-head" data-reveal>
                <div class="cat-sec-head__left">
                    <span class="cat-eyebrow">Baru Minggu Ini</span>
                    <h2 class="cat-sec-title">New Arrivals</h2>
                </div>

                <a href="{{ route('user.products.index') }}" class="cat-see-all">
                    Lihat Semua <span class="cat-see-all__arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <div class="na-grid">
                @forelse ($newArrivals as $i => $product)
                    <x-product-card :product="$product" :delay="$i * 100" />
                @empty
                    <p class="na-empty">Belum ada produk baru saat ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============================================================
         3b. BANNER PROMO MARQUEE  [BARU]
         ============================================================ --}}
    <div class="promo-bar" data-reveal>
        <div class="promo-track">
            {{-- Diulang 6x; translateX(-50%) butuh 2 blok identik -> loop mulus --}}
            @for ($i = 0; $i < 6; $i++)
                <span class="promo-item">
                    New Drop <span class="promo-dot">&bull;</span> Free Ongkir Min. Belanja Rp 300.000
                </span>
            @endfor
        </div>
    </div>

    {{-- ============================================================
         3c. STRIP KEUNGGULAN  [BARU]
         ============================================================ --}}
    <section class="cat-section">
        <div class="cat-container">
            <div class="usp-grid">
                @php
                    $usps = [
                        ['icon' => 'zmdi-layers',           'title' => 'Bahan Premium',     'desc' => 'Katun & fleece pilihan'],
                        ['icon' => 'zmdi-truck',            'title' => 'Pengiriman Cepat',  'desc' => 'Kirim ke seluruh Indonesia'],
                        ['icon' => 'zmdi-refresh',          'title' => 'Retur 7 Hari',      'desc' => 'Tukar ukuran tanpa ribet'],
                        ['icon' => 'zmdi-lock',             'title' => 'Pembayaran Aman',   'desc' => 'Midtrans terenkripsi'],
                    ];
                @endphp

                @foreach ($usps as $i => $usp)
                    <div class="usp" data-reveal style="--reveal-delay: {{ $i * 80 }}ms">
                        <span class="usp__icon"><i class="zmdi {{ $usp['icon'] }}"></i></span>
                        <div class="usp__body">
                            <h3 class="usp__title">{{ $usp['title'] }}</h3>
                            <p class="usp__desc">{{ $usp['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection

@push('styles')
{{-- Harus paling awal: menandai JS aktif sebelum body dirender, supaya
     gaya "sembunyi dulu lalu fade-in" hanya berlaku bila JS menyala.
     Kalau JS mati, gambar/tetap tampil normal (tidak hilang). --}}
<script>document.documentElement.classList.add('js');</script>

<style>
/* ============================================================================
   [BARU SAJA] Halaman Kategori — lapisan visual premium.
   ----------------------------------------------------------------------------
   CSS hover card LAMA ada di public/css/main.css baris 1994-2070
   (.block1, .block1-txt, .block1-link, .block1-txt-child2, dst)
   dan TIDAK disentuh sama sekali di file ini.
   ============================================================================ */

:root {
    --drvn-indigo: #4F46E5;
    --drvn-ink: #0A0A0A;
    --drvn-muted: #6B7280;
    --drvn-line: #E5E7EB;
}

.cat-page {
    background: #fff;
    overflow-x: hidden;          /* cegah scroll horizontal dari marquee */
}

/* Container max-w-7xl (80rem) sesuai spesifikasi */
.cat-container {
    width: 100%;
    max-width: 80rem;
    margin-inline: auto;
    padding-inline: 1.5rem;
}

/* ---------- 1. HERO HEADER ---------- */
/* Sebelumnya: p-t-80 (80px) + margin-top 24px = 104px.
   Navbar melayang menempel di top:16px dengan tinggi 68px (bottom = 84px),
   jadi 96px tetap aman namun terasa lebih rapat. */
.cat-hero {
    padding-top: 96px;
    padding-bottom: 4px;
}

.cat-hero__eyebrow {
    margin: 0 0 14px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--drvn-muted);
}

.cat-hero__title {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;                        /* diminta: 600 */
    font-size: clamp(2.3rem, 7.2vw, 5.75rem); /* responsif clamp */
    line-height: 0.98;
    letter-spacing: 0.045em;                  /* tracking lebar */
    text-transform: uppercase;
    color: var(--drvn-ink);
}

.cat-hero__sub {
    margin: 18px 0 0;
    font-size: clamp(0.95rem, 1.5vw, 1.15rem);
    color: var(--drvn-muted);
}

/* Animasi masuk hero: fade + slide up */
.js .cat-hero-anim {
    opacity: 0;
    animation: catHeroIn 0.85s cubic-bezier(0.16, 0.84, 0.44, 1) forwards;
    animation-delay: var(--d, 0ms);
}
@keyframes catHeroIn {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: none; }
}

/* ---------- SPACING ANTAR SECTION (konsisten) ---------- */
.cat-section {
    padding-block: 4rem;                 /* py-16 */
}
.cat-section--tight {
    padding-top: 2.75rem;
    padding-bottom: 4rem;
}
@media (min-width: 1024px) {
    .cat-section { padding-block: 6rem; } /* py-24 */
    .cat-section--tight { padding-top: 3rem; padding-bottom: 6rem; }
}

/* ---------- 2. GRID KARTU: 1 / 2 / 3 kolom ---------- */
.cat-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 768px) {
    .cat-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1200px) {
    .cat-grid { grid-template-columns: repeat(3, 1fr); }
}

/* Kartu seragam: rasio 4/5 agar tinggi sama rata (premium look) */
.cat-card {
    aspect-ratio: 4 / 5;
}
.cat-card img {
    height: 100%;
    object-fit: cover;
}

/* [BARU] Meta pojok kartu: nomor index + jumlah produk */
.cat-meta {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    pointer-events: none;   /* hover tetap diteruskan ke link .block1-txt */
}
.cat-meta__idx,
.cat-meta__count {
    font-size: 0.66rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    white-space: nowrap;
    line-height: 1.5;
}
.cat-meta__idx {
    color: var(--drvn-ink);
    border: 1px solid rgba(0, 0, 0, 0.08);
}
.cat-meta__count {
    color: var(--drvn-indigo);
    border: 1px solid rgba(79, 70, 229, 0.18);
}

/* [BARU] Panah "→" pada link Explore — hanya menimpa anak, bukan aturan lama */
.cat-arrow {
    display: inline-block;
    margin-left: 7px;
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.block1-txt:hover .cat-arrow {
    transform: translateX(6px);
}

/* ---------- SCROLL REVEAL (IntersectionObserver) ---------- */
.js [data-reveal] {
    opacity: 0;
    transform: translateY(26px);
    transition: opacity 0.7s cubic-bezier(0.16, 0.84, 0.44, 1),
                transform 0.7s cubic-bezier(0.16, 0.84, 0.44, 1);
    transition-delay: var(--reveal-delay, 0ms);
}
.js [data-reveal].is-revealed {
    opacity: 1;
    transform: none;
}

/* ---------- FADE-IN GAMBAR LAZY LOAD ----------
   Transisi transform ikut ditulis ulang supaya zoom hover
   (.block1:hover img { transform: scale(1.08) }) tetap beranimasi. */
.js .fade-img {
    opacity: 0;
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.js .fade-img.is-loaded {
    opacity: 1;
}

/* ---------- 3a. NEW ARRIVALS ---------- */
.cat-sec-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1.25rem;
    margin-bottom: 2.25rem;
}
.cat-eyebrow {
    display: block;
    margin-bottom: 0.6rem;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--drvn-indigo);
}
.cat-sec-title {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: clamp(1.6rem, 3.4vw, 2.6rem);
    line-height: 1.05;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: var(--drvn-ink);
}
.cat-see-all {
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    color: var(--drvn-ink);
    text-decoration: none;
    border-bottom: 2px solid var(--drvn-ink);
    padding-bottom: 4px;
    transition: color 0.3s ease, border-color 0.3s ease;
}
.cat-see-all:hover {
    color: var(--drvn-indigo);
    border-color: var(--drvn-indigo);
}
.cat-see-all__arrow {
    display: inline-block;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.cat-see-all:hover .cat-see-all__arrow {
    transform: translateX(5px);
}

/* Grid produk: 1 / 2 / 4 kolom.
   4 kolom di desktop agar 4 produk baru muat sebaris (tanpa yatim di baris 2). */
.na-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem 1.5rem;
}
@media (min-width: 640px) {
    .na-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1200px) {
    .na-grid { grid-template-columns: repeat(4, 1fr); }
}
.na-empty {
    grid-column: 1 / -1;
    margin: 0;
    color: var(--drvn-muted);
}

/* ---------- 3b. BANNER PROMO MARQUEE ---------- */
.promo-bar {
    background: var(--drvn-ink);
    color: #fff;
    overflow: hidden;
    padding-block: 15px;
}
.promo-track {
    display: flex;
    width: max-content;
    animation: promoMarquee 26s linear infinite;
}
.promo-item {
    white-space: nowrap;
    padding-inline: 2.5rem;
    font-size: 0.76rem;
    font-weight: 600;
    letter-spacing: 0.24em;
    text-transform: uppercase;
}
.promo-dot {
    color: #818CF8;
    padding-inline: 0.5rem;
}
@keyframes promoMarquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* ---------- 3c. STRIP KEUNGGULAN ---------- */
.usp-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
}
@media (min-width: 640px) {
    .usp-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1024px) {
    .usp-grid { grid-template-columns: repeat(4, 1fr); }
}
.usp {
    display: flex;
    align-items: flex-start;
    gap: 0.9rem;
    padding: 1.25rem;
    border: 1px solid var(--drvn-line);
    border-radius: 16px;
    background: #fff;
    transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
}
.usp:hover {
    border-color: var(--drvn-indigo);
    box-shadow: 0 10px 30px rgba(79, 70, 229, 0.09);
    transform: translateY(-3px);
}
.usp__icon {
    flex: 0 0 auto;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #EEF2FF;
    color: var(--drvn-indigo);
    display: grid;
    place-items: center;
    font-size: 1.05rem;
}
.usp__body { min-width: 0; }
.usp__title {
    margin: 0 0 0.25rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--drvn-ink);
}
.usp__desc {
    margin: 0;
    font-size: 0.8rem;
    line-height: 1.5;
    color: var(--drvn-muted);
}

/* ============================================================================
   HORMATI prefers-reduced-motion — semua gerakan dimatikan, konten tetap terlihat
   ============================================================================ */
@media (prefers-reduced-motion: reduce) {
    .js .cat-hero-anim {
        opacity: 1 !important;
        animation: none !important;
    }
    .js [data-reveal] {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
    }
    .js .fade-img {
        opacity: 1 !important;
        transition: none !important;
    }
    .promo-track { animation: none !important; }
    .cat-arrow,
    .cat-see-all__arrow,
    .usp { transition: none !important; }
    .usp:hover { transform: none !important; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---- 1. Fade-in gambar lazy-load ---- */
    function markLoaded(img) { img.classList.add('is-loaded'); }

    var images = document.querySelectorAll('img.fade-img');
    for (var i = 0; i < images.length; i++) {
        (function (img) {
            if (img.complete) {
                // Sudah termuat dari cache -> langsung tampilkan
                markLoaded(img);
            } else {
                img.addEventListener('load', function () { markLoaded(img); }, { once: true });
                img.addEventListener('error', function () { markLoaded(img); }, { once: true });
            }
        })(images[i]);
    }

    /* ---- 2. Scroll reveal bertahap (stagger via --reveal-delay) ---- */
    var targets = document.querySelectorAll('[data-reveal]');

    var supported = ('IntersectionObserver' in window);
    if (!supported || reduceMotion) {
        for (var j = 0; j < targets.length; j++) targets[j].classList.add('is-revealed');
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);   // sekali saja
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

    for (var k = 0; k < targets.length; k++) observer.observe(targets[k]);
})();
</script>
@endpush
