@extends('layouts.main')

@section('title', 'Home | DRVN')

@section('content')
<!-- Slider -->
<section class="section-slide">
    <div class="wrap-slick1">
        <div class="slick1">
            <div class="item-slick1" style="background-image: url(images/slide-02.jpg);">
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

            <div class="item-slick1" style="background-image: url(images/slide-01.jpg);">
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

            <div class="item-slick1" style="background-image: url(images/slide-03.jpg);;">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="rotateInDownLeft"
                            data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                Men Collection 2025
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="rotateInUpRight"
                            data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                New arrivals
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="rotateIn"
                            data-delay="1600">
                            <a href="{{ route('user.products.index') }}"
                                class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <div class="row isotope-grid">
            @foreach ($products as $product)
            <div
                class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item {{ strtolower($product->category->category_name) }}">
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

                        <div class="block2-txt-child2 flex-r p-t-3">
                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                <img class="icon-heart1 dis-block trans-04"
                                    src="{{ asset('images/icons/icon-heart-01.png') }}" alt="ICON">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                    src="{{ asset('images/icons/icon-heart-02.png') }}" alt="ICON">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@push('styles')
<style>
    /* ===== NAVBAR HOME ===== */

    /* Default (di atas slider) = transparan */
    .header-v4 .container-menu-desktop,
    .header-v4 .wrap-menu-desktop {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    /* Saat discroll, CozaStore nambahin class "fix-menu-desktop" ke container-menu-desktop */
    .header-v4 .container-menu-desktop.fix-menu-desktop,
    .header-v4 .container-menu-desktop.fix-menu-desktop .wrap-menu-desktop {
        background-color: #ffffff !important;
        box-shadow: 0 2px 10px rgba(0,0,0,.05) !important;
    }
</style>
@endpush



@endsection