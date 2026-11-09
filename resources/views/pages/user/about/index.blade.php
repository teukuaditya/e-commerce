@extends('layouts.main')

@section('title', 'Tentang Kami | ' . ($company['name'] ?? 'DRVN'))

@section('content')
@php
    $name    = $company['name'] ?? 'DRVN';
    $tagline = $company['tagline'] ?? 'all about essentials';
    $email   = $company['email'] ?? '-';
    $address = $company['address'] ?? '-';
@endphp

<div class="about-page">

    {{-- ============ 1. HERO ============ --}}
    <section class="about-hero" id="aboutHero">
        <div class="about-hero__inner container">
            <nav class="about-crumb" aria-label="breadcrumb">
                <a href="{{ route('user.home') }}">Home</a>
                <span>›</span>
                <span>Tentang Kami</span>
            </nav>

            <p class="about-eyebrow">Tentang Kami</p>
            <h1 class="about-title">
                Dirancang untuk jadi <span>esensial</span><br>
                di lemari Anda.
            </h1>
            <p class="about-lead">
                {{ ucfirst($tagline) }} — kami membuat pakaian yang simpel, nyaman, dan tahan
                lama. Tanpa banyak drama, cukup kualitas yang bisa diandalkan setiap hari.
            </p>

            <div class="about-hero__cta">
                <a href="{{ route('user.products.index') }}" class="about-btn about-btn--light">
                    Mulai Belanja <i class="fa fa-arrow-right"></i>
                </a>
                <a href="#aboutStory" class="about-btn about-btn--ghost">Cerita Kami</a>
            </div>

            <div class="about-hero__tags">
                <span><i class="fas fa-check"></i> Kualitas teruji</span>
                <span><i class="fas fa-check"></i> Pengiriman cepat</span>
                <span><i class="fas fa-check"></i> Harga transparan</span>
            </div>
        </div>
    </section>

    {{-- ============ 2. STATISTIK ============ --}}
    <section class="about-stats">
        <div class="container about-stats__grid">
            <div class="about-stat">
                <span class="about-stat__num" data-count="{{ max(0, ($stats['products'] ?? 0)) }}">0</span>
                <span class="about-stat__label">Produk</span>
            </div>
            <div class="about-stat">
                <span class="about-stat__num" data-count="{{ max(0, $stats['categories'] ?? 0) }}">0</span>
                <span class="about-stat__label">Kategori</span>
            </div>
            <div class="about-stat">
                <span class="about-stat__num" data-count="{{ max(0, $stats['orders'] ?? 0) }}">0</span>
                <span class="about-stat__label">Pesanan Diproses</span>
            </div>
            <div class="about-stat">
                <span class="about-stat__num">@php $avg = $stats['avg_rating'] ?? null; @endphp
                    @if ($avg !== null)
                        {{ number_format($avg, 1) }}<i class="fa fa-star"></i>
                    @else
                        <i class="fa fa-star" style="opacity:.35"></i>
                    @endif
                </span>
                <span class="about-stat__label">{{ $avg !== null ? 'Rating Produk' : 'Rating (menunggu review)' }}</span>
            </div>
        </div>
    </section>

    {{-- ============ 3. CERITA / STIRI KAMI ============ --}}
    <section class="about-story" id="aboutStory">
        <div class="container">
            <div class="row align-items-center about-row">

                <div class="col-lg-6 about-reveal">
                    <div class="about-story__media">
                        <img src="{{ asset('images/about-01.jpg') }}" alt="Studio &amp; prototipe produk {{ $name }}"
                             class="about-story__img" loading="lazy">
                        <img src="{{ asset('images/about-02.jpg') }}" alt="Proses produksi {{ $name }}"
                             class="about-story__img about-story__img--small" loading="lazy">
                        <div class="about-story__badge">
                            <strong>Since<br>2024</strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 about-reveal">
                    <p class="about-eyebrow about-eyebrow--dark">Cerita Kami</p>
                    <h2 class="about-heading">Dari satu masalah sederhana —
                        <span>butuh kaos yang benar-benar awet</span>
                    </h2>
                    <p class="about-story__text">
                        {{ $name }} lahir di Purwokerto dari pengalaman banyak orang: mudah
                        menemukan pakaian, sulit temu yang benar-benar berkualitas dengan harga masuk akal.
                        Kami memulai dengan satu kategori yang paling krusial — pakaian esensial — lalu
                        membangun proses produksi di sekitarnya.
                    </p>
                    <p class="about-story__text">
                        Setiap item melewati tahap riset bahan, prototipe, produksi, dan
                        quality control sebelum masuk ke etalase. Hasilnya: potongan minimalis
                        dengan detail yang berfungsi nyata, bukan sekadar gaya.
                    </p>

                    <div class="row about-mv">
                        <div class="col-sm-6">
                            <div class="about-mv__item">
                                <i class="fas fa-bullseye" aria-hidden="true"></i>
                                <div>
                                    <h3>Misi</h3>
                                    <p>Pakaian esensial berkualitas dengan harga terjangkau.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-mv__item">
                                <i class="fas fa-compass" aria-hidden="true"></i>
                                <div>
                                    <h3>Visi</h3>
                                    <p>Menjadi brand lokal terdepan kategori pakaian esensial.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ 4. NILAI / KENAPA KAMI ============ --}}
    <section class="about-values">
        <div class="container">
            <div class="text-center about-values__head about-reveal">
                <p class="about-eyebrow about-eyebrow--dark">Kenapa {{ $name }}</p>
                <h2 class="about-heading">Tiga hal yang selalu kami kejar</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 about-reveal">
                    <div class="about-card">
                        <div class="about-card__icon"><i class="fas fa-tshirt" aria-hidden="true"></i></div>
                        <h3>Bahan Berkualitas</h3>
                        <p>Riset mendalam untuk memilih bahan yang nyaman dipakai
                            sehari-hari dan tidak cepat berubah bentuk — kualitas yang bisa diandalkan.</p>
                    </div>
                </div>
                <div class="col-lg-4 about-reveal">
                    <div class="about-card">
                        <div class="about-card__icon"><i class="fas fa-award" aria-hidden="true"></i></div>
                        <h3>Produksi Lokal</h3>
                        <p>Diproduksi secara lokal dengan pengawasan kualitas ketat pada
                            tiap tahap — sekaligus tumbuh bersama lingkungan &amp; umpan kekomunitas.</p>
                    </div>
                </div>
                <div class="col-lg-4 about-reveal">
                    <div class="about-card">
                        <div class="about-card__icon"><i class="fas fa-tags" aria-hidden="true"></i></div>
                        <h3>Harga Terjangkau</h3>
                        <p>Harga transparan untuk kualitas premium. Pakaian esensial
                            harusnya masuk akal — bukan mahal karena citra semata.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 5. CTA ============ --}}
    <section class="about-cta about-reveal">
        <div class="container">
            <div class="about-cta__inner">
                <div>
                    <h2>Sudah menemukan esensial Anda?</h2>
                    <p>Lihat koleksi lengkap di halaman produk — semuanya sudah menunggu Anda.</p>
                </div>
                <a href="{{ route('user.products.index') }}" class="about-btn about-btn--accent">
                    Jelajahi Produk <i class="fa fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ============ 6. KONTAK ============ --}}
    <section class="about-contact">
        <div class="container">
            <div class="row g-4 about-reveal">
                <div class="col-lg-6">
                    <div class="about-contact__item">
                        <div class="about-contact__icon"><i class="fas fa-envelope" aria-hidden="true"></i></div>
                        <div>
                            <h3>Email</h3>
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-contact__item">
                        <div class="about-contact__icon"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></div>
                        <div>
                            <h3>Alamat</h3>
                            <p>{{ $address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center about-sign about-reveal">
                <img src="{{ asset('images/drvn-logo.png') }}" alt="Logo {{ $name }}">
                <p>{{ $name }} — {{ $tagline }}</p>
            </div>
        </div>
    </section>

</div>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ==== About page — scoped, tidak memengaruhi halaman lain ==== */
    .about-page {
        --ink: #17171d;
        --ink-soft: #55555f;
        --accent: #4f46e5;
        --accent-soft: #eef2ff;
        --line: #e9e9ee;
        --radius: 16px;
        font-family: 'Inter', sans-serif;
    }

    .about-eyebrow {
        font-size: 12px; font-weight: 700; letter-spacing: .18em;
        text-transform: uppercase; color: #c9c9d4; margin-bottom: 14px;
    }
    .about-eyebrow--dark { color: var(--accent); }
    .about-heading { font-size: 28px; line-height: 1.3; color: var(--ink); }
    .about-heading span { color: var(--accent); }

    /* ---- hero ---- */
    /* Hero ditarik ke atas (menutup padding main 100px) supaya navbar
       floating kaca melayang di atas hero gelap — tanpa space putih.
       FULL SCREEN: gambar mengisi tepat 1 layar; konten dicenter vertikal. */
    .about-hero {
        background: linear-gradient(120deg, rgba(20,20,26,.92), rgba(20,20,26,.62) 55%, rgba(79,70,229,.28)),
                    url('{{ asset('images/about-01.jpg') }}') center/cover no-repeat;
        color: #fff;
        margin-top: -100px;               /* tandingi padding-top main (desktop) */
        min-height: 100vh;                /* ← GAMBAR MEMENUHI LAYAR PENUH */
        display: flex;
        align-items: center;              /* konten vertical center, tidak mepet atas */
        padding: 120px 0;                 /* udara nyaman, sisanya diisi gambar */
    }
    @media (max-width: 991.98px) {
        .about-hero { margin-top: -86px; padding: 96px 0; } /* navbar mobile overlay */
    }
    .about-crumb { font-size: 13px; opacity: .72; margin-bottom: 26px; }
    .about-crumb a { color: #fff; text-decoration: none; }
    .about-crumb a:hover { color: #fff; opacity: .8; }
    .about-crumb span + span { margin-left: 8px; }
    .about-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(34px, 5vw, 56px); line-height: 1.12;
        max-width: 640px;
    }
    .about-title span { color: #a5b4fc; }
    .about-lead { font-size: 17px; opacity: .86; max-width: 560px; margin-top: 18px; }
    .about-hero__cta { margin-top: 34px; display: flex; gap: 14px; flex-wrap: wrap; }
    .about-hero__tags { margin-top: 40px; display: flex; gap: 26px; flex-wrap: wrap; font-size: 14px; opacity: .85; }
    .about-hero__tags i { color: #a5b4fc; margin-right: 7px; }

    .about-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px; font-weight: 600;
        text-decoration: none; transition: all .25s ease;
    }
    .about-btn--light { background: #fff; color: var(--ink); }
    .about-btn--light:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,.25); color: var(--ink); }
    .about-btn--ghost { color: #fff; border: 1px solid rgba(255,255,255,.4); }
    .about-btn--ghost:hover { border-color: #fff; color: #fff; }
    .about-btn--accent { background: var(--accent); color: #fff; }
    .about-btn--accent:hover { background: #4338ca; color: #fff; transform: translateY(-2px); }

    /* ---- stats ---- */
    .about-stats { background: var(--ink); color: #fff; padding: 44px 0; }
    .about-stats__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; text-align: center; }
    @media (max-width: 767.98px) { .about-stats__grid { grid-template-columns: repeat(2, 1fr); } }
    .about-stat__num { display: block; font-size: 34px; font-weight: 700; }
    .about-stat__num i { font-size: 18px; color: #fbbf24; margin-left: 6px; }
    .about-stat__label { font-size: 13px; opacity: .7; margin-top: 4px; display: block; }

    /* ---- story ---- */
    .about-story { padding: 96px 0; }
    .about-story__media { position: relative; padding-bottom: 46px; }
    .about-story__img {
        width: 78%; border-radius: var(--radius);
        box-shadow: 30px 30px 0 var(--accent-soft);
    }
    .about-story__img--small {
        position: absolute; right: 0; bottom: 0;
        width: 42%; border-radius: var(--radius); box-shadow: 0 18px 40px rgba(23,23,29,.15);
        border: 6px solid #fff;
    }
    .about-story__badge {
        position: absolute; left: -14px; top: -22px;
        background: var(--ink); color: #fff; border-radius: 14px;
        padding: 14px 18px; font-size: 13px; line-height: 1.35;
        box-shadow: 0 12px 30px rgba(0,0,0,.18);
    }
    .about-story__text { color: var(--ink-soft); margin-top: 16px; }

    .about-mv { margin-top: 34px; }
    .about-mv__item {
        display: flex; gap: 14px; padding: 18px;
        background: var(--accent-soft); border-radius: 12px; height: 100%;
    }
    .about-mv__item i { color: var(--accent); font-size: 20px; margin-top: 3px; }
    .about-mv__item h3 { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
    .about-mv__item p { font-size: 13.5px; color: var(--ink-soft); margin: 0; }

    .about-row { row-gap: 44px; }

    /* ---- values ---- */
    .about-values { background: #fafafc; padding: 90px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
    .about-values__head { margin-bottom: 46px; }
    .about-card {
        background: #fff; height: 100%; border-radius: var(--radius);
        padding: 34px 30px; border: 1px solid var(--line);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .about-card:hover { transform: translateY(-6px); box-shadow: 0 20px 45px rgba(23,23,29,.09); }
    .about-card__icon {
        width: 54px; height: 54px; border-radius: 14px;
        background: var(--accent-soft); color: var(--accent);
        display: flex; align-items: center; justify-content: center;
        font-size: 21px; margin-bottom: 20px;
    }
    .about-card h3 { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
    .about-card p { font-size: 14px; color: var(--ink-soft); margin: 0; }

    /* ---- cta ---- */
    .about-cta { padding: 84px 0; background: #fff; }
    .about-cta__inner {
        background: linear-gradient(120deg, var(--ink), #2f2f3d);
        color: #fff; border-radius: var(--radius);
        padding: 52px 54px;
        display: flex; align-items: center; justify-content: space-between; gap: 30px; flex-wrap: wrap;
    }
    .about-cta__inner h2 { font-size: 26px; margin-bottom: 8px; }
    .about-cta__inner p { opacity: .8; margin: 0; }

    /* ---- contact ---- */
    .about-contact { padding: 20px 0 90px; background: #fff; }
    .about-contact__item {
        display: flex; gap: 18px; align-items: flex-start;
        background: #fafafc; border: 1px solid var(--line);
        border-radius: var(--radius); padding: 26px; height: 100%;
    }
    .about-contact__icon {
        width: 46px; height: 46px; flex: 0 0 46px; border-radius: 12px;
        background: var(--ink); color: #fff;
        display: flex; align-items: center; justify-content: center;
    }
    .about-contact__item h3 { font-size: 14px; text-transform: uppercase; letter-spacing: .1em; color: var(--ink-soft); margin-bottom: 6px; }
    .about-contact__item p, .about-contact__item a { color: var(--ink); text-decoration: none; margin: 0; }
    .about-contact__item a:hover { color: var(--accent); }

    .about-sign { margin-top: 60px; }
    .about-sign img { max-width: 130px; opacity: .9; }
    .about-sign p { font-size: 13px; color: var(--ink-soft); margin-top: 12px; }

    /* ---- reveal on scroll ---- */
    .about-reveal { opacity: 0; transform: translateY(26px); transition: opacity .7s ease, transform .7s ease; }
    .about-reveal.is-visible { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) {
        .about-reveal { opacity: 1; transform: none; transition: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    // 1) Count-up angka statistik + (2) Reveal on scroll:
    $(document).ready(function () {
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Counter angka (mulai animasi saat terlihat)
        function runCounter($el) {
            var target = parseInt($el.data('count'), 10) || 0;
            if (reduced) { $el.text(target); return; }

            var started = null;
            var duration = 1300;
            function tick(ts) {
                if (!started) started = ts;
                var p = Math.min((ts - started) / duration, 1);
                $el.text(Math.floor(target * (1 - Math.pow(1 - p, 3)))); // ease-out
                if (p < 1) requestAnimationFrame(tick); else $el.text(target);
            }
            requestAnimationFrame(tick);
        }

        // Reveal: element baru tampil saat masuk viewport
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    var $el = $(entry.target);
                    $el.addClass('is-visible');

                    // jika section stat/counter: jalankan counter sekali saja
                    if ($el.hasClass('about-stat__num') || $el.data('count') !== undefined) {
                        if ($el.data('count') !== undefined && !$el.data('counted')) {
                            $el.data('counted', true);
                            runCounter($el);
                        }
                    }
                    io.unobserve(entry.target);
                });
            }, { threshold: 0.18 });

            $('.about-reveal, .about-stat__num[data-count]').each(function () {
                io.observe(this);
            });
        } else {
            // fallback tanpa IntersectionObserver
            $('.about-reveal').addClass('is-visible');
            $('.about-stat__num[data-count]').each(function () { runCounter($(this)); });
        }
    });
</script>
@endpush
