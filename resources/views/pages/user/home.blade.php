@extends('layouts.main')

@section('title', 'Home | DRVN')

@section('body-class', 'page-home')

@section('content')
<!-- Slider: 2 gambar, crossfade mandiri (tanpa slick) -->
<section class="section-slide">
    <div class="wrap-hero">
        <div class="hero-track">
            <div class="item-slick1 hero-slide is-active" style="background-image: url('{{ asset('images/slide-02.jpg') }}');">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="rollIn" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                Men New-Season
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="lightSpeedIn" data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                Jackets & Coats
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="slideInUp" data-delay="1600">
                            <a href="{{ route('user.products.index') }}"
                                class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item-slick1 hero-slide" style="background-image: url('{{ asset('images/slide-01.jpg') }}');">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                Women Collection 2025
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                NEW SEASON
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                            <a href="{{ route('user.products.index') }}"
                                class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <button class="arrow-slick1 prev-slick1" type="button" aria-label="Slide sebelumnya"><i class="zmdi zmdi-caret-left"></i></button>
        <button class="arrow-slick1 next-slick1" type="button" aria-label="Slide berikutnya"><i class="zmdi zmdi-caret-right"></i></button>
    </div>
</section>

<script>document.documentElement.classList.add('js-reveal');</script>

<!-- Category Banner -->
@if(isset($bestSellers) && $bestSellers->isNotEmpty())
<section class="home-section home-bestseller-section p-t-80 p-b-50" data-reveal>
    <div class="container">
        <div class="d-flex align-items-end justify-content-between flex-wrap" style="gap:16px; margin-bottom: 36px;">
            <div>
                <span class="home-sec-eyebrow">Paling Diminati</span>
                <h2 class="home-sec-title" style="margin:0; font-size: clamp(28px, 4vw, 42px); line-height:1.1; color:#111827;">Best Sellers</h2>
                <p style="margin:10px 0 0; color:#64748B; max-width:560px;">Produk favorit yang paling banyak dipilih. Pilihan aman buat kamu yang mau tampil on-point.</p>
            </div>
            <a href="{{ route('user.products.index') }}" class="home-newarrivals-link stext-101 cl2 hov-cl1 trans-04 m-tb-10">
                Lihat Semua Produk <i class="zmdi zmdi-arrow-right m-l-4"></i>
            </a>
        </div>

        <div class="row">
            @foreach($bestSellers as $product)
            @php
                $img = is_array($product->image) ? ($product->image[0] ?? '') : $product->image;
                $catName = optional($product->category)->category_name ?? '';
                $isOutOfStock = (int) ($product->stock ?? 0) <= 0;
                $sold = (int) ($product->sold_count ?? 0);
                $rank = $loop->iteration;
            @endphp
            <div class="col-6 col-md-3 p-b-35 product-item {{ strtolower($catName) }}" data-reveal style="--rv-delay: {{ ($loop->index % 4) * 70 }}ms">
                <div class="block2 home-product-card" style="position:relative;">
                    <div class="block2-pic hov-img0 home-prod-pic">
                        <span style="position:absolute;top:12px;left:12px;z-index:4;background:{{ $rank === 1 ? 'linear-gradient(135deg,#F59E0B,#F97316)' : ($rank === 2 ? 'linear-gradient(135deg,#64748B,#475569)' : ($rank === 3 ? 'linear-gradient(135deg,#B45309,#92400E)' : '#111827')) }};color:#fff;font-size:11px;font-weight:800;padding:6px 10px;border-radius:999px;box-shadow:0 8px 20px rgba(15,23,42,.18);">#{{ $rank }} Best Seller</span>
                        <a href="{{ route('user.products.show', $product->id) }}">
                            <img src="{{ asset('storage/products/' . $img) }}" alt="{{ $product->title }}" loading="lazy" class="home-prod-img">
                        </a>
                        <div class="home-prod-actions" aria-label="Aksi cepat">
                            @auth
                                @php $isWished = in_array($product->id, $wishlistedIds ?? []); @endphp
                                <button type="button"
                                    class="home-prod-action-btn js-wishlist-toggle {{ $isWished ? 'is-wishlisted' : '' }}"
                                    data-url="{{ route('user.wishlist.toggle', $product->id) }}"
                                    data-wishlisted="{{ $isWished ? '1' : '0' }}"
                                    title="{{ $isWished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                                    aria-label="{{ $isWished ? 'Hapus' : 'Simpan' }} {{ $product->title }} dari wishlist"
                                    aria-pressed="{{ $isWished ? 'true' : 'false' }}">
                                    <i class="zmdi {{ $isWished ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="home-prod-action-btn" title="Login untuk Wishlist" aria-label="Login untuk menyimpan {{ $product->title }}">
                                    <i class="zmdi zmdi-favorite-outline"></i>
                                </a>
                            @endauth
                            @auth
                                <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="home-prod-action-form">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="home-prod-action-btn" title="Quick Add" aria-label="Tambah {{ $product->title }} ke keranjang">
                                        <i class="zmdi zmdi-shopping-cart"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('user.products.show', $product->id) }}" class="home-prod-action-btn" title="Quick Add" aria-label="Lihat {{ $product->title }}">
                                    <i class="zmdi zmdi-shopping-cart"></i>
                                </a>
                            @endauth
                        </div>
                        <a href="{{ route('user.products.show', $product->id) }}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 home-prod-quickview">Quick View</a>
                    </div>
                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l" style="width:100%;">
                            @if($catName)
                                <span class="stext-106 cl6 trans-04 m-b-3 home-prod-cat">{{ strtoupper($catName) }}</span>
                            @endif
                            <a href="{{ route('user.products.show', $product->id) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6 home-prod-title">{{ $product->title }}</a>
                            <div class="d-flex align-items-center justify-content-between" style="gap:8px;width:100%;">
                                <a href="{{ route('user.products.show', $product->id) }}" class="stext-105 cl5 hov-cl1 trans-04 home-prod-price" style="font-weight:700;color:#111827;">Rp {{ number_format($product->price, 0, ',', '.') }}</a>
                                <span class="stext-106 cl6" style="font-size:12px;white-space:nowrap;text-align:right;">{{ $sold }} terjual</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Promo Banners Slider -->
<section class="home-promo-banners" data-reveal>
    <div class="container">
        <div class="hpb-slider" id="hpbSlider" aria-roledescription="carousel">
            <div class="hpb-track">
                <article class="hpb-slide is-active">
                    <div class="hpb-slide__bg" style="background-image:url('{{ asset('images/banner-04.jpg') }}');"></div>
                    <div class="hpb-slide__overlay"></div>
                    <div class="hpb-slide__content">
                        <span class="hpb-kicker">Limited Vibe</span>
                        <h2 class="hpb-title">STREET READY.<br>EVERY DAY.</h2>
                        <p class="hpb-desc">Outfit simpel dengan impact kuat buat gerak harian lo.</p>
                        <a href="{{ route('user.products.index') }}" class="hpb-btn">Shop Now</a>
                    </div>
                </article>
                <article class="hpb-slide">
                    <div class="hpb-slide__bg" style="background-image:url('{{ asset('images/banner-05.jpg') }}');"></div>
                    <div class="hpb-slide__overlay hpb-slide__overlay--right"></div>
                    <div class="hpb-slide__content hpb-slide__content--right">
                        <span class="hpb-kicker">Move Freely</span>
                        <h2 class="hpb-title">BUILT FOR<br>THE MOTION.</h2>
                        <p class="hpb-desc">Sportswear nyaman yang tetap clean dari gym sampai hangout.</p>
                        <a href="{{ route('user.categories.index') }}" class="hpb-btn">Explore Categories</a>
                    </div>
                </article>
                <article class="hpb-slide">
                    <div class="hpb-slide__bg" style="background-image:url('{{ asset('images/banner-06.jpg') }}');"></div>
                    <div class="hpb-slide__overlay"></div>
                    <div class="hpb-slide__content">
                        <span class="hpb-kicker">Fresh Drop</span>
                        <h2 class="hpb-title">NEW LOOK.<br>SAME DRIVE.</h2>
                        <p class="hpb-desc">Upgrade gaya lo dengan koleksi terbaru dari DRVN.</p>
                        <a href="{{ route('user.products.index') }}" class="hpb-btn">See Collection</a>
                    </div>
                </article>
            </div>
            <button class="hpb-nav hpb-nav--prev" type="button" aria-label="Banner sebelumnya"><i class="zmdi zmdi-chevron-left"></i></button>
            <button class="hpb-nav hpb-nav--next" type="button" aria-label="Banner berikutnya"><i class="zmdi zmdi-chevron-right"></i></button>
            <div class="hpb-dots" aria-label="Navigasi banner">
                <button class="hpb-dot is-active" type="button" aria-label="Banner 1"></button>
                <button class="hpb-dot" type="button" aria-label="Banner 2"></button>
                <button class="hpb-dot" type="button" aria-label="Banner 3"></button>
            </div>
        </div>
    </div>
</section>


<!-- New Arrivals -->
<div class="bg0 m-t-23 p-b-80 home-section" id="home-new-arrivals">
    <div class="container">
        <div class="flex-w flex-sb-m p-b-52 align-items-end home-newarrivals-head" data-reveal>
            <div class="home-sec-head home-sec-head--left m-b-0">
                <span class="home-sec-eyebrow">Just Dropped</span>
                <h3 class="ltext-103 cl5 home-sec-title m-b-0">
                    New Arrivals
                </h3>
                <span class="home-sec-accent home-sec-accent--left" aria-hidden="true"></span>
            </div>
            <a href="{{ route('user.products.index') }}" class="home-newarrivals-link stext-101 cl2 hov-cl1 trans-04 m-tb-10">
                Lihat Semua Produk <i class="zmdi zmdi-arrow-right m-l-4"></i>
            </a>
        </div>

        <!-- Produk -->
        <div class="row" id="homeProductGrid">
            @foreach ($products as $product)
            @php
                $img = is_array($product->image) ? ($product->image[0] ?? '') : $product->image;
                $catName = optional($product->category)->category_name ?? '';
                $isOutOfStock = (int) ($product->stock ?? 0) <= 0;
            @endphp
            <div class="col-6 col-md-3 p-b-35 product-item {{ strtolower($catName) }}" data-reveal style="--rv-delay: {{ ($loop->index % 4) * 70 }}ms">
                <div class="block2 home-product-card">
                    <div class="block2-pic hov-img0 home-prod-pic">
                        <span class="home-prod-badge">New</span>
                        <a href="{{ route('user.products.show', $product->id) }}">
                            <img src="{{ asset('storage/products/' . $img) }}"
                                alt="{{ $product->title }}"
                                loading="lazy"
                                class="home-prod-img">
                        </a>

                        {{-- Overlay aksi (selalu tampil) --}}
                        <div class="home-prod-actions" aria-label="Aksi cepat">
                            @auth
                                @php $isWished = in_array($product->id, $wishlistedIds ?? []); @endphp
                                <button type="button"
                                    class="home-prod-action-btn js-wishlist-toggle {{ $isWished ? 'is-wishlisted' : '' }}"
                                    data-url="{{ route('user.wishlist.toggle', $product->id) }}"
                                    data-wishlisted="{{ $isWished ? '1' : '0' }}"
                                    title="{{ $isWished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                                    aria-label="{{ $isWished ? 'Hapus' : 'Simpan' }} {{ $product->title }} dari wishlist"
                                    aria-pressed="{{ $isWished ? 'true' : 'false' }}">
                                    <i class="zmdi {{ $isWished ? 'zmdi-favorite' : 'zmdi-favorite-outline' }}"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="home-prod-action-btn" title="Login untuk Wishlist" aria-label="Login untuk menyimpan {{ $product->title }}">
                                    <i class="zmdi zmdi-favorite-outline"></i>
                                </a>
                            @endauth
                            @auth
                                <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="home-prod-action-form">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="home-prod-action-btn" title="Quick Add" aria-label="Tambah {{ $product->title }} ke keranjang">
                                        <i class="zmdi zmdi-shopping-cart"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('user.products.show', $product->id) }}" class="home-prod-action-btn" title="Quick Add" aria-label="Lihat {{ $product->title }}">
                                    <i class="zmdi zmdi-shopping-cart"></i>
                                </a>
                            @endauth
                        </div>

                        {{-- Tombol detail tetap ada (fitur lama), di-restyle via CSS --}}
                        <a href="{{ route('user.products.show', $product->id) }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 home-prod-quickview">
                            Quick View
                        </a>
                    </div>

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l">
                            @if($catName)
                                <span class="stext-106 cl6 trans-04 m-b-3 home-prod-cat">{{ strtoupper($catName) }}</span>
                            @endif

                            <a href="{{ route('user.products.show', $product->id) }}"
                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6 home-prod-title">
                                {{ $product->title }}
                            </a>

                            <div class="d-flex align-items-center justify-content-between" style="gap:8px;width:100%;">
                                <a href="{{ route('user.products.show', $product->id) }}"
                                    class="stext-105 cl5 hov-cl1 trans-04 home-prod-price" style="font-weight:700; color:#111827;">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </a>
                                <span class="stext-106 cl6" style="font-size:12px;white-space:nowrap;text-align:right;">{{ (int) ($product->sold_count ?? 0) }} terjual</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

<!-- Editorial Banner -->
<section class="home-section home-editorial-section" data-reveal>
    <div class="container">
        <div class="home-editorial">
            <div class="home-editorial__bg" aria-hidden="true"></div>
            <div class="home-editorial__overlay" aria-hidden="true"></div>
            <div class="home-editorial__content">
                <span class="home-sec-eyebrow" style="color: rgba(255,255,255,.72);">DRVN Lifestyle</span>
                <h2 class="home-editorial__title">BUILT TO MOVE.<br><span>STYLED TO STAND OUT.</span></h2>
                <p class="home-editorial__desc">Dari jalanan ke arena, temukan gear yang siap menemani setiap langkah dan gaya lo.</p>
                <div class="home-editorial__actions">
                    <a href="{{ route('user.products.index') }}" class="home-editorial__btn home-editorial__btn--primary">Explore Collection</a>
                    <a href="{{ route('user.categories.index') }}" class="home-editorial__btn home-editorial__btn--ghost">Lihat Kategori</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why DRVN / USP -->
<section class="home-section home-usp-section" data-reveal>
    <div class="container">
        <div class="home-usp-grid">
            <div class="home-usp-item">
                <span class="home-usp-icon"><i class="zmdi zmdi-layers"></i></span>
                <div class="home-usp-body">
                    <h4 class="home-usp-title">Bahan Premium</h4>
                    <p class="home-usp-desc">Material pilihan yang nyaman dipakai.</p>
                </div>
            </div>
            <div class="home-usp-item">
                <span class="home-usp-icon"><i class="zmdi zmdi-truck"></i></span>
                <div class="home-usp-body">
                    <h4 class="home-usp-title">Pengiriman Cepat</h4>
                    <p class="home-usp-desc">Kirim ke seluruh Indonesia.</p>
                </div>
            </div>
            <div class="home-usp-item">
                <span class="home-usp-icon"><i class="zmdi zmdi-refresh"></i></span>
                <div class="home-usp-body">
                    <h4 class="home-usp-title">Retur 7 Hari</h4>
                    <p class="home-usp-desc">Tukar ukuran tanpa ribet.</p>
                </div>
            </div>
            <div class="home-usp-item">
                <span class="home-usp-icon"><i class="zmdi zmdi-lock"></i></span>
                <div class="home-usp-body">
                    <h4 class="home-usp-title">Pembayaran Aman</h4>
                    <p class="home-usp-desc">Transaksi terenkripsi & terpercaya.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Promo Marquee -->
<div class="promo-bar" data-reveal>
    <div class="promo-track">
        @for ($i = 0; $i < 6; $i++)
            <span class="promo-item">
                New Drop <span class="promo-dot">&bull;</span> Free Ongkir Min. Belanja Rp 300.000 <span class="promo-dot">&bull;</span> Streetwear &amp; Sportswear
            </span>
        @endfor
    </div>
</div>

{{-- style navbar & grid ada di theme-upgrade.css dan main.css --}}

@push('styles')
<style>
  /* ===== Promo Banners Slider ===== */
  .home-promo-banners { padding: 16px 0 56px; }
  .hpb-slider {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    background: #111827;
    box-shadow: 0 20px 50px rgba(15,23,42,.12);
  }
  .hpb-track { position: relative; min-height: 420px; }
  .hpb-slide {
    position: absolute; inset: 0;
    opacity: 0; visibility: hidden;
    transition: opacity .7s ease, visibility .7s ease;
  }
  .hpb-slide.is-active { opacity: 1; visibility: visible; z-index: 1; }
  .hpb-slide__bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    transform: scale(1.03);
    transition: transform 6s ease;
  }
  .hpb-slide.is-active .hpb-slide__bg { transform: scale(1.08); }
  .hpb-slide__overlay {
    position: absolute; inset: 0;
    background: linear-gradient(100deg, rgba(17,24,39,.78) 0%, rgba(17,24,39,.52) 45%, rgba(17,24,39,.18) 100%);
  }
  .hpb-slide__overlay--right {
    background: linear-gradient(260deg, rgba(17,24,39,.78) 0%, rgba(17,24,39,.52) 45%, rgba(17,24,39,.18) 100%);
  }
  .hpb-slide__content {
    position: relative; z-index: 2;
    max-width: 620px; padding: 64px;
    color: #fff;
  }
  .hpb-slide__content--right { margin-left: auto; text-align: right; }
  .hpb-slide__content--right .hpb-desc { margin-left: auto; }
  .hpb-slide__content--right .hpb-btn { margin-left: auto; }
  .hpb-kicker {
    display: inline-block;
    font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 11px; letter-spacing: .24em; text-transform: uppercase;
    color: rgba(255,255,255,.72); margin-bottom: 12px;
  }
  .hpb-title {
    margin: 0 0 14px;
    font-family: Poppins-Bold, Poppins, sans-serif;
    font-size: clamp(2rem, 4.8vw, 3.4rem);
    line-height: 1.05; letter-spacing: -.02em; color: #fff;
  }
  .hpb-desc {
    margin: 0 0 28px; max-width: 460px;
    font-size: .98rem; line-height: 1.7; color: rgba(255,255,255,.84);
  }
  .hpb-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-height: 46px; padding: 0 24px; border-radius: 999px;
    background: #fff; color: #111827; font-weight: 600; font-size: .88rem;
    text-decoration: none !important; transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease, color .25s ease;
    box-shadow: 0 10px 24px rgba(0,0,0,.18);
  }
  .hpb-btn:hover { transform: translateY(-2px); background: #eef2ff; color: #3730a3; }
  .hpb-nav {
    position: absolute; top: 50%; transform: translateY(-50%);
    width: 44px; height: 44px; border-radius: 50%; border: 0;
    background: rgba(255,255,255,.16); color: #fff; z-index: 3;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .2s ease, transform .2s ease;
    backdrop-filter: blur(4px);
  }
  .hpb-nav:hover { background: rgba(255,255,255,.28); transform: translateY(-50%) scale(1.04); }
  .hpb-nav--prev { left: 18px; }
  .hpb-nav--next { right: 18px; }
  .hpb-nav i { font-size: 24px; line-height: 1; }
  .hpb-dots {
    position: absolute; left: 50%; bottom: 22px; transform: translateX(-50%);
    display: flex; gap: 8px; z-index: 3;
  }
  .hpb-dot {
    width: 28px; height: 4px; border-radius: 999px; border: 0;
    background: rgba(255,255,255,.35); cursor: pointer; padding: 0;
    transition: all .25s ease;
  }
  .hpb-dot.is-active { background: #fff; width: 40px; }
  @media (max-width: 767px) {
    .home-promo-banners { padding: 36px 0 0; }
    .hpb-track { min-height: 360px; }
    .hpb-slide__content, .hpb-slide__content--right { padding: 40px 20px 64px; text-align: left; margin-left: 0; }
    .hpb-slide__content--right .hpb-desc, .hpb-slide__content--right .hpb-btn { margin-left: 0; }
    .hpb-nav { display: none; }
    .hpb-slide__overlay, .hpb-slide__overlay--right { background: linear-gradient(180deg, rgba(17,24,39,.35) 0%, rgba(17,24,39,.78) 100%); }
  }

  /* ===== Hero slider mandiri (crossfade 2 gambar, tanpa slick) =====
     Geometry mengikuti .section-slide yang sudah 100vh di navbar-floating.css,
     media <=991px mengikuti aturan main.css (100vh - 70px utk navbar mobile). */
  .wrap-hero {
    position: relative;
    width: 100%;
    height: 100%;
  }

  .hero-track {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }

  .hero-track .item-slick1 {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    z-index: 1;
    pointer-events: none;
    transition: opacity 1s ease;
  }

  /* Slide aktif di depan & interaktif. Slide lama fade-out di belakangnya
     sehingga transisi benar-benar crossfade (langsung gambar ke gambar). */
  .hero-track .item-slick1.is-active {
    opacity: 1;
    z-index: 2;
    pointer-events: auto;
  }

  @media (max-width: 991px) {
    .wrap-hero,
    .hero-track,
    .hero-track .item-slick1 {
      height: calc(100vh - 70px) !important;
    }
  }

  /* Tombol panah (styling .arrow-slick1 dari main.css masih dipakai) */
  .wrap-hero .arrow-slick1 {
    cursor: pointer;
    background-color: transparent;
    border: none;
  }

  /* ===== HOME POLISH (scoped) ===== */
  .home-section { scroll-margin-top: 110px; }

  .home-sec-head { margin-bottom: 8px; }
  .home-sec-head--left { text-align: left; }
  .home-sec-eyebrow {
    display: inline-block;
    font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 11px;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 10px;
  }
  .home-sec-title {
    letter-spacing: 0.04em;
    margin-bottom: 0;
  }
  .home-sec-accent {
    display: block;
    width: 48px;
    height: 3px;
    background: #222;
    margin-top: 14px;
  }
  .home-sec-accent--left { margin-left: 0; margin-right: auto; }

  .home-newarrivals-head { gap: 16px; }
  .home-newarrivals-link {
    display: inline-flex;
    align-items: center;
    font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 13px;
    letter-spacing: 0.04em;
    color: #111827;
    text-decoration: none;
    padding-bottom: 4px;
    border-bottom: 1px solid #d1d5db;
    transition: color .2s ease, border-color .2s ease;
    white-space: nowrap;
  }
  .home-newarrivals-link:hover {
    color: #4f46e5;
    border-color: #4f46e5;
    text-decoration: none;
  }
  .home-newarrivals-link i { font-size: 16px; transition: transform .2s ease; }
  .home-newarrivals-link:hover i { transform: translateX(3px); }

  /* --- Category card: samakan dengan halaman kategori (x-category-card) --- */
  .cat-meta {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    pointer-events: none;
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
    color: #0A0A0A;
    border: 1px solid rgba(0, 0, 0, 0.08);
  }
  .cat-meta__count {
    color: #4F46E5;
    border: 1px solid rgba(79, 70, 229, 0.18);
  }
  .cat-arrow {
    display: inline-block;
    margin-left: 7px;
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .block1-txt:hover .cat-arrow {
    transform: translateX(6px);
  }

  /* --- Product filter pills --- */
  .home-filter-btn {
    border: 1px solid #d1d5db !important;
    border-radius: 999px;
    padding: 9px 20px !important;
    margin-right: 10px !important;
    background: #fff;
    color: #4b5563;
    line-height: 1.2;
  }
  .home-filter-btn:hover {
    border-color: #111827 !important;
    color: #111827;
  }
  .home-filter-btn.how-active1 {
    background: #111827 !important;
    border-color: #111827 !important;
    color: #fff !important;
  }

  /* --- Product card polish (dimensi kartu tetap) --- */
  .page-home .home-prod-pic { background: #f3f4f6; overflow: hidden; position: relative; }
  .page-home .home-prod-pic .home-prod-img {
    transition: transform 0.35s ease;
  }
  .page-home .home-prod-pic:hover .home-prod-img {
    transform: scale(1.05);
  }
  .home-prod-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 4;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 10px;
    border-radius: 999px;
    background: #111827;
    color: #fff;
    font-family: Poppins-SemiBold, Poppins, sans-serif;
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    line-height: 1;
    pointer-events: none;
  }
  .home-prod-actions {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    opacity: 1;
    transform: none;
    transition: opacity 0.3s ease, transform 0.3s ease;
    z-index: 5;
  }
  .home-prod-action-form { margin: 0; }
  .home-prod-action-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.96);
    color: #111827;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    line-height: 1;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    cursor: pointer;
    transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
    text-decoration: none;
    font-size: 18px;
  }
  .home-prod-action-btn i {
    display: block;
    line-height: 1;
  }
  .home-prod-action-btn:hover {
    background: #4F46E5;
    color: #fff;
    transform: scale(1.04);
    text-decoration: none;
  }
  .home-prod-action-btn.is-wishlisted {
    color: #e53935;
  }
  .home-prod-action-btn.is-wishlisted:hover {
    background: #e53935;
    color: #fff;
  }
  .home-prod-action-btn:focus-visible {
    outline: 2px solid #4F46E5;
    outline-offset: 2px;
  }
  .home-prod-quickview {
    z-index: 4;
  }
  .wishlist-inline-btn {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    line-height: 1;
    font-size: 20px;
    color: #999;
    transition: color 0.2s ease, transform 0.2s ease;
  }
  .wishlist-inline-btn:hover { color: #e53935; transform: scale(1.08); }
  .wishlist-inline-btn.is-wishlisted { color: #e53935; }
  .wishlist-inline-btn i { display: block; line-height: 1; }
  .home-prod-cat {
    display: block;
    font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 10px;
    letter-spacing: 0.14em;
    color: #6b7280 !important;
    line-height: 22px;
    min-height: 22px;
  }
  .home-prod-title {
    display: -webkit-box !important;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 41px;
    font-family: Poppins-SemiBold, Poppins, sans-serif;
    color: #1f2937 !important;
  }
  .home-prod-price {
    font-variant-numeric: tabular-nums;
    letter-spacing: 0.01em;
  }

  /* --- Reveal animation (opacity+transform only) --- */
  .js-reveal [data-reveal] {
    opacity: 0;
    transform: translateY(18px);
    transition: opacity 0.5s ease, transform 0.5s ease;
    transition-delay: var(--rv-delay, 0ms);
  }
  .js-reveal [data-reveal].is-revealed {
    opacity: 1;
    transform: translateY(0);
  }

  @media (max-width: 767px) {
    .home-filter-btn { margin-right: 8px !important; padding: 8px 14px !important; font-size: 12px; }
    .home-sec-accent { width: 40px; }
  }
  @media (hover: none), (pointer: coarse) {
    .home-prod-actions { opacity: 1; transform: none; }
  }
  .home-filter-btn:focus-visible {
    outline: 2px solid #4F46E5;
    outline-offset: 2px;
  }

  /* ===== Home Editorial Banner ===== */
  .home-editorial-section { padding: 0 0 24px; }
  .home-editorial {
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    min-height: 360px;
    display: flex;
    align-items: center;
    background: #111827;
    isolation: isolate;
  }
  .home-editorial__bg {
    position: absolute;
    inset: 0;
    background: url('{{ asset('images/bg-02.jpg') }}') center/cover no-repeat;
    transform: scale(1.02);
    transition: transform 0.8s ease;
    z-index: -2;
  }
  .home-editorial:hover .home-editorial__bg { transform: scale(1.06); }
  .home-editorial__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(100deg, rgba(17,24,39,.88) 0%, rgba(17,24,39,.72) 45%, rgba(17,24,39,.35) 100%);
    z-index: -1;
  }
  .home-editorial__content {
    position: relative;
    padding: 56px;
    max-width: 720px;
  }
  .home-editorial__title {
    margin: 10px 0 16px;
    font-family: Poppins-Bold, Poppins, sans-serif;
    font-size: clamp(2rem, 4.6vw, 3.5rem);
    line-height: 1.05;
    letter-spacing: -0.02em;
    color: #fff;
  }
  .home-editorial__title span { color: #a5b4fc; }
  .home-editorial__desc {
    margin: 0 0 28px;
    max-width: 560px;
    font-size: 1rem;
    line-height: 1.7;
    color: rgba(255,255,255,.82);
  }
  .home-editorial__actions { display: flex; flex-wrap: wrap; gap: 12px; }
  .home-editorial__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0 22px;
    border-radius: 999px;
    font-family: Poppins-Medium, Poppins, sans-serif;
    font-size: 0.88rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease, color .25s ease, border-color .25s ease;
  }
  .home-editorial__btn:hover { transform: translateY(-2px); }
  .home-editorial__btn--primary { background: #fff; color: #111827; box-shadow: 0 10px 24px rgba(0,0,0,.18); }
  .home-editorial__btn--primary:hover { background: #eef2ff; color: #3730a3; }
  .home-editorial__btn--ghost { border: 1px solid rgba(255,255,255,.38); color: #fff; background: rgba(255,255,255,.04); }
  .home-editorial__btn--ghost:hover { background: rgba(255,255,255,.12); border-color: rgba(255,255,255,.65); color: #fff; }
  @media (max-width: 767px) {
    .home-editorial { min-height: 320px; border-radius: 18px; }
    .home-editorial__content { padding: 32px 24px; }
    .home-editorial__overlay { background: linear-gradient(180deg, rgba(17,24,39,.55) 0%, rgba(17,24,39,.82) 100%); }
  }

  /* ===== Home USP ===== */
  .home-usp-section {
    padding: 56px 0;
    background: #f8fafc;
    border-top: 1px solid #eef2f7;
    border-bottom: 1px solid #eef2f7;
  }
  .home-usp-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }
  .home-usp-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 20px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid #e8edf3;
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
  }
  .home-usp-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(17, 24, 39, 0.06);
    border-color: #c7d2fe;
  }
  .home-usp-icon {
    flex: 0 0 auto;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #eef2ff;
    color: #4f46e5;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .home-usp-title {
    margin: 0 0 4px;
    font-family: Poppins-SemiBold, Poppins, sans-serif;
    font-size: 14px;
    color: #111827;
    line-height: 1.3;
  }
  .home-usp-desc {
    margin: 0;
    font-family: Poppins-Regular, Poppins, sans-serif;
    font-size: 12.5px;
    color: #6b7280;
    line-height: 1.5;
  }
  @media (max-width: 991px) {
    .home-usp-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 575px) {
    .home-usp-grid { grid-template-columns: 1fr; }
    .home-usp-section { padding: 40px 0; }
  }

  /* ===== Promo Marquee ===== */
  .promo-bar {
    background: #111827;
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

  @media (prefers-reduced-motion: reduce) {
    .page-home .home-prod-pic .home-prod-img,
    .cat-arrow,
    .home-prod-actions,
    .home-prod-action-btn,
    .home-usp-item,
    .home-editorial__bg,
    .home-editorial__btn,
    .js-reveal [data-reveal] {
      transition: none !important;
      transform: none !important;
    }
    .js-reveal [data-reveal] { opacity: 1 !important; }
    .promo-track { animation: none !important; }
    .home-editorial:hover .home-editorial__bg { transform: none !important; }
    .hpb-slide, .hpb-slide__bg, .hpb-btn, .hpb-nav, .hpb-dot { transition: none !important; }
    .hpb-slide.is-active .hpb-slide__bg { transform: scale(1.03) !important; }
  }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        // Toggle class scrolled pada navbar home untuk efek frosted glass
        // (threshold 50px, sinkron dengan toggle global di navbar.blade.php)
        var $header = $('header.header-v4');
        function updateNavbar() {
            if ($(window).scrollTop() > 50) {
                $header.addClass('scrolled');
            } else {
                $header.removeClass('scrolled');
            }
        }
        $(window).on('scroll', updateNavbar);
        updateNavbar(); // cek langsung saat load

        /* ===== Hero slider mandiri: crossfade + animasi layer (animate.css) =====
           Markup memakai atribut sama dgn versi slick dulu:
           data-appear = nama animasi animate.css utk layer
           data-delay  = jeda muncul layer (ms)
           visible-false/visible-true dari util.css menyembunyikan layer sblm anim. */
        var $slides = $('.hero-track .hero-slide');
        var heroCurrent = 0;
        var heroTimer = null;
        var layerTimers = [];

        // Kumpulkan semua nama animasi utk dihapus dgn pasti saat reset layer
        var $layers = $slides.find('.layer-slick1');
        var allAppears = [];
        $layers.each(function () {
            var name = $(this).data('appear');
            if (allAppears.indexOf(name) === -1) allAppears.push(name);
        });

        function clearLayerTimers() {
            for (var i = 0; i < layerTimers.length; i++) clearTimeout(layerTimers[i]);
            layerTimers = [];
        }

        // Set semua layer sembunyi, lalu mainkan animasi layer dlm slide aktif
        function playLayers($activeSlide) {
            clearLayerTimers();
            $layers.each(function () {
                $(this)
                    .removeClass(allAppears.join(' ') + ' visible-true')
                    .addClass('visible-false');
                this.offsetWidth; // paksa reflow agar animasi animate.css bisa diputar ulang
            });
            $activeSlide.find('.layer-slick1').each(function () {
                var $el = $(this);
                var delay = parseInt($el.data('delay'), 10) || 0;
                layerTimers.push(setTimeout(function () {
                    $el.removeClass('visible-false')
                       .addClass($el.data('appear') + ' visible-true');
                }, delay));
            });
        }

        function heroGo(idx) {
            heroCurrent = (idx + $slides.length) % $slides.length;
            var $active = $slides.eq(heroCurrent);
            $slides.removeClass('is-active');
            $active.addClass('is-active');
            playLayers($active);
        }

        function restartAuto() {
            if (heroTimer) clearInterval(heroTimer);
            heroTimer = setInterval(function () {
                heroGo(heroCurrent + 1);
            }, 6000);
        }

        heroGo(0);      // animasi layer slide pertama saat load
        restartAuto();  // autoplay 6s

        $('.wrap-hero .next-slick1').on('click', function () {
            heroGo(heroCurrent + 1);
            restartAuto();
        });
        $('.wrap-hero .prev-slick1').on('click', function () {
            heroGo(heroCurrent - 1);
            restartAuto();
        });

        // Promo Banners auto slider
        (function () {
            var slider = document.getElementById('hpbSlider');
            if (!slider) return;
            var slides = slider.querySelectorAll('.hpb-slide');
            var dots = slider.querySelectorAll('.hpb-dot');
            var prev = slider.querySelector('.hpb-nav--prev');
            var next = slider.querySelector('.hpb-nav--next');
            var idx = 0, timer = null;
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function go(n) {
                idx = (n + slides.length) % slides.length;
                for (var i = 0; i < slides.length; i++) {
                    slides[i].classList.toggle('is-active', i === idx);
                    if (dots[i]) dots[i].classList.toggle('is-active', i === idx);
                }
            }
            function start() {
                if (reduce) return;
                stop();
                timer = setInterval(function () { go(idx + 1); }, 4500);
            }
            function stop() { if (timer) { clearInterval(timer); timer = null; } }

            if (next) next.addEventListener('click', function () { go(idx + 1); start(); });
            if (prev) prev.addEventListener('click', function () { go(idx - 1); start(); });
            for (var d = 0; d < dots.length; d++) {
                (function (di) {
                    dots[di].addEventListener('click', function () { go(di); start(); });
                })(d);
            }
            slider.addEventListener('mouseenter', stop);
            slider.addEventListener('mouseleave', start);
            slider.addEventListener('focusin', stop);
            slider.addEventListener('focusout', start);
            go(0);
            start();
        })();

        // Grid produk home (New Arrivals) — tidak ada filter kategori

        // Wishlist toggle (AJAX)
        $(document).on('click', '.js-wishlist-toggle', function (e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.data('loading')) return;
            $btn.data('loading', true);

            $.ajax({
                url: $btn.data('url'),
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    var wished = !!res.wishlisted;
                    $btn.toggleClass('is-wishlisted', wished);
                    $btn.attr('data-wishlisted', wished ? '1' : '0');
                    $btn.attr('aria-pressed', wished ? 'true' : 'false');
                    $btn.attr('title', wished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist');
                    $btn.find('i').attr('class', 'zmdi ' + (wished ? 'zmdi-favorite' : 'zmdi-favorite-outline'));
                    $('a.icon-header-noti[href*="wishlist"]').each(function () {
                        var n = parseInt($(this).attr('data-notify') || '0', 10) || 0;
                        $(this).attr('data-notify', Math.max(0, n + (wished ? 1 : -1)));
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route("login") }}';
                        return;
                    }
                },
                complete: function () {
                    $btn.data('loading', false);
                }
            });
        });

        // Reveal on scroll (IntersectionObserver, opacity+transform saja)
        document.documentElement.classList.add('js-reveal');
        var revealEls = document.querySelectorAll('[data-reveal]');
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
            for (var i = 0; i < revealEls.length; i++) revealEls[i].classList.add('is-revealed');
        } else {
            var revealObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        revealObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            for (var j = 0; j < revealEls.length; j++) revealObs.observe(revealEls[j]);
        }
    });
</script>
@endpush

@endsection