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

<!-- Category Banner -->
<div class="sec-banner bg0 p-t-80 p-b-50">
    <div class="container">
        <h3 class="ltext-103 cl5 mb-4">
            CATEGORIES
        </h3>  
        <div class="row">
            @foreach ($categories as $category)
            <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto"> 
                <div class="block1 wrap-pic-w">
                    <img src="{{ asset('storage/categories/' . $category->image) }}"
                        alt="{{ $category->category_name }}">
                    <a href="{{ route('user.categories.products', $category->id) }}"
                        class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ $category->category_name }}
                            </span>

                            <span class="block1-info stext-102 trans-04">
                                {{ $category->description }}
                            </span>
                        </div>

                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                Explore
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Product Overview -->
<div class="bg0 m-t-23 p-b-140">
    <div class="container">
        <div class="p-b-10">
            <h3 class="ltext-103 cl5">
                Product Overview
            </h3>
        </div>

        <div class="flex-w flex-sb-m p-b-52">
            <!-- Filter kategori -->
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1"
                    data-filter="*">
                    All Products
                </button>
                @foreach ($categories as $category)
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                    data-filter=".{{ strtolower($category->category_name) }}">
                    {{ $category->category_name }}
                </button>
                @endforeach
            </div>

            {{-- Kalau mau nanti di sini bisa ditambah search seperti di halaman product --}}
        </div>

        <!-- Produk -->
        <div class="row" id="homeProductGrid">
            @foreach ($products as $product)
            <div
                class="col-6 col-md-3 p-b-35 product-item {{ strtolower($product->category->category_name) }}">
                <div class="block2">
                    <div class="block2-pic hov-img0">
                        <a href="{{ route('user.products.show', $product->id) }}">
                            <img src="{{ asset('storage/products/' . (is_array($product->image) ? $product->image[0] : $product->image)) }}"
                                alt="{{ $product->title }}">
                        </a>

                        <a href="{{ route('user.products.show', $product->id) }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            Add To Cart
                        </a>
                    </div>

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l">
                            <a href="{{ route('user.products.show', $product->id) }}"
                                class="stext-106 cl5 hov-cl1 trans-04 m-b-3" style="font-weight: bold;">
                                {{ $product->brand }}
                            </a>

                            <a href="{{ route('user.products.show', $product->id) }}"
                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                {{ $product->title }}
                            </a>

                            <a href="{{ route('user.products.show', $product->id) }}"
                                class="stext-105 cl5 hov-cl1 trans-04">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </a>
                        </div>

                        {{-- <div class="block2-txt-child2 flex-r p-t-3">
                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                <img class="icon-heart1 dis-block trans-04"
                                    src="{{ asset('images/icons/icon-heart-01.png') }}" alt="ICON">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                    src="{{ asset('images/icons/icon-heart-02.png') }}" alt="ICON">
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
{{-- style navbar & grid ada di theme-upgrade.css dan main.css --}}

@push('styles')
<style>
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

        // Filter kategori home — jQuery show/hide (tanpa Isotope)
        $('.filter-tope-group button').on('click', function () {
            var filterValue = $(this).attr('data-filter');
            $('.filter-tope-group button').removeClass('how-active1');
            $(this).addClass('how-active1');

            if (filterValue === '*') {
                $('#homeProductGrid .product-item').show();
            } else {
                var cls = filterValue.replace('.', '');
                $('#homeProductGrid .product-item').each(function () {
                    $(this).toggle($(this).hasClass(cls));
                });
            }
        });
    });
</script>
@endpush

@endsection