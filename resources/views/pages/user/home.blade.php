<!DOCTYPE html>
<html lang="en">

<head>
    <title>DRVN</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="{{ asset('images/icons/flexora.png') }}" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animsition/css/animsition.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/select2/select2.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/slick/slick.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/MagnificPopup/magnific-popup.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    <!--===============================================================================================-->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="animsition">

    <!-- Header -->
    <header>
        <!-- Header desktop -->
        <div class="container-menu-desktop">
            <div class="wrap-menu-desktop">
                <nav class="limiter-menu-desktop container">

                    <!-- Logo desktop -->
                    <a href="{{ route('user.home') }}" class="logo">
                        <span
                            style="font-size: 25px; font-weight: bold; color: #000; letter-spacing: 2px;">DRVN</span>
                    </a>


                    <!-- Menu desktop -->
                    <div class="menu-desktop">
                        <ul class="main-menu">
                            <li class="{{ Route::currentRouteName() == 'user.home' ? 'active-menu' : '' }}">
                                <a href="{{ route('user.home') }}">Home</a>
                            </li>

                            <li class="{{ Route::currentRouteName() == 'user.products.index' ? 'active-menu' : '' }}">
                                <a href="{{ route('user.products.index') }}">Product</a>
                            </li>

                            <li
                                class="{{ Route::currentRouteName() == 'user.categories.index' ? 'active-menu' : '' }}">
                                <a href="{{ route('user.categories.index') }}">Category</a>
                                <ul class="sub-menu">
                                    @foreach ($categories as $category)
                                        <li><a
                                                href="{{ route('user.categories.products', $category->id) }}">{{ $category->category_name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                            <li class="{{ Route::currentRouteName() == 'blog' ? 'active-menu' : '' }}">
                                <a href="">Blog</a>
                            </li>

                            <li class="{{ Route::currentRouteName() == 'about' ? 'active-menu' : '' }}">
                                <a href="{{ route('user.about.index')}}">About</a>
                            </li>

                            <li class="{{ Route::currentRouteName() == 'contact' ? 'active-menu' : '' }}">
                                <a href="">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Icon header -->
                    <div class="wrap-icon-header flex-w flex-r-m">
                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                            <i class="zmdi zmdi-search"></i>
                        </div>

                        <a href="{{ route('user.cart.index') }}">
                            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                                data-notify="{{ $cartCount }}">
                                <i class="zmdi zmdi-shopping-cart"></i>
                            </div>
                        </a>

                        <a href="#"
                            class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                            data-notify="0">
                            <i class="zmdi zmdi-favorite-outline"></i>
                        </a>

                        {{-- <!-- Garis Pemisah -->
                    <div class="line-separator"></div> --}}

                        @guest
                            <!-- Jika user belum login -->
                            <a href="{{ route('login') }}"
                                class="flex-c-m stext-110 cl0 size-127 bg1 ml-4 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                style="font-size: 14px; min-width: 10%; height: 30px">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}"
                                class="flex-c-m stext-110 cl0 size-127 bg3 ml-2 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                style="font-size: 14px; min-width: 10%; height: 30px">
                                Register
                            </a>
                        @else
                            <!-- Dropdown User Info -->
                            <div class="">
                                <a class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11" href="#"
                                    id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="zmdi zmdi-account"></i>
                                </a>
                                <!-- Dropdown Menu -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <h6 class="dropdown-header stext-110 cl2" style="font-weight: bold; font-size: 14px">
                                        {{ Auth::user()->name }}<br>
                                        <small class="stext-104 cl4"
                                            style="font-size: 12px">{{ Auth::user()->email }}</small>
                                    </h6>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item stext-104" href="{{ route('user.edit') }}">
                                        <i class="fas fa-user fa-fw m-r-10 mb-2"></i>
                                        My Profile
                                    </a>
                                    <a class="dropdown-item stext-104" href="{{ route('user.address.edit') }}">
                                        <i class="fas fa-map-marker-alt fa-fw m-r-10 mb-2"></i>
                                        My Address
                                    </a>
                                    <a class="dropdown-item stext-104" href="{{ route('user.order.index') }}">
                                        <i class="fas fa-shopping-bag fa-fw m-r-10"></i>
                                        Order History
                                    </a>

                                    <!-- Menambahkan link Admin jika pengguna adalah admin -->
                                    @if (Auth::user()->role == 'admin')
                                        <a class="dropdown-item stext-104" href="{{ route('admin.products.index') }}">
                                            <i class="fas fa-user-cog fa-fw m-r-10 mt-2"></i>
                                            Admin
                                        </a>
                                    @endif

                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}" data-toggle="modal"
                                        data-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt fa-fw mr-2 text-gray-400"></i>
                                        Sign Out
                                    </a>
                                    {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form> --}}
                                </div>
                            </div>
                        @endguest
                    </div>
            </div>

            <!-- Logout Modal-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title mtext-101 cl2" id="exampleModalLabel">Ready to Leave?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body stext-102">Select "Logout" below if you are ready to end your current
                            session.
                        </div>
                        <div class="modal-footer">
                            <button
                                class="flex-c-m stext-102 cl0 size-304 bg10 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                                type="button" data-dismiss="modal">Cancel</button>
                            <a class="flex-c-m stext-102 cl0 size-304 bg1 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                                href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .dropdown-item:active {
                    background-color: #717fe0;
                    /* Warna yang diinginkan */
                    color: #fff;
                    /* Warna teks jika perlu */
                }

                /* CSS untuk Modal */
                .modal-backdrop {
                    z-index: 2000 !important;
                    /* Pastikan backdrop lebih tinggi dari top bar */
                }

                .modal {
                    z-index: 2200 !important;
                    /* Modal harus berada di atas backdrop */
                }

                /* Memberikan margin atas pada modal */
                .modal-dialog {
                    margin-top: 80px;
                    /* Atur sesuai dengan kebutuhan, semakin besar maka semakin ke bawah */
                }
            </style>


            <!-- Modal Search -->
            <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
                <div class="container-search-header">
                    <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                        <img src="images/icons/icon-close2.png" alt="CLOSE">
                    </button>

                    <form class="wrap-search-header flex-w p-l-15">
                        <button class="flex-c-m trans-04">
                            <i class="zmdi zmdi-search"></i>
                        </button>
                        <input class="plh3" type="text" name="search" placeholder="Search...">
                    </form>
                </div>
            </div>
    </header>

    <!-- Header -->
    <header>
        <!-- Header Mobile -->
        <div class="wrap-header-mobile">
            @guest
                <!-- Logo moblie -->
                <a href="{{ route('user.home') }}" class="logo-mobile">
                    <span style="font-size: 20px; font-weight: bold; color: #000; letter-spacing: 2px;">FLEXORA</span>
                </a>
                <!-- Jika user belum login -->
                <a href="{{ route('login') }}"
                    class="flex-c-m stext-110 cl0 size-127 bg1 ml-2 mr-2 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                    style="font-size: 14px">
                    Sign In
                </a>
                {{-- <a href="{{ route('register') }}"
                    class="flex-c-m stext-110 cl0 size-127 bg3 ml-2 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                    style="font-size: 14px">
                    Register
                </a> --}}
            @else
                <a href="{{ route('user.home') }}" class="logo-mobile">
                    <span style="font-size: 20px; font-weight: bold; color: #000; letter-spacing: 2px;">FLEXORA</span>
                </a>
                <!-- Icon header -->
                <div class="wrap-icon-header flex-w flex-r-m">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
                        <i class="zmdi zmdi-search"></i>
                    </div>

                    <a href="{{ route('user.cart.index') }}">
                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-11 p-r-11 icon-header-noti js-show-cart"
                            data-notify="{{ $cartCount }}">
                            <i class="zmdi zmdi-shopping-cart"></i>
                        </div>
                    </a>

                    <a href="#"
                        class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti"
                        data-notify="0">
                        <i class="zmdi zmdi-favorite-outline"></i>
                    </a>
                    <!-- Dropdown User Info -->
                    <div class="">
                        <a class="icon-header-item cl2 hov-cl1 trans-04 p-l-11 p-r-11" href="#" id="userDropdown"
                            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="zmdi zmdi-account"></i>
                        </a>
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <h6 class="dropdown-header stext-110 cl2" style="font-weight: bold; font-size: 14px">
                                {{ Auth::user()->name }}<br>
                                <small class="stext-104 cl4" style="font-size: 12px">{{ Auth::user()->email }}</small>
                            </h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item stext-104" href="{{ route('user.edit') }}">
                                <i class="fas fa-user fa-fw m-r-10 mb-2"></i>
                                My Profile
                            </a>
                            <a class="dropdown-item stext-104" href="{{ route('user.address.edit') }}">
                                <i class="fas fa-map-marker-alt fa-fw m-r-10 mb-2"></i>
                                My Address
                            </a>
                            <a class="dropdown-item stext-104" href="{{ route('user.order.index') }}">
                                <i class="fas fa-shopping-bag fa-fw m-r-10"></i>
                                Order History
                            </a>

                            <!-- Menambahkan link Admin jika pengguna adalah admin -->
                            @if (Auth::user()->role == 'admin')
                                <a class="dropdown-item stext-104" href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-user-cog fa-fw m-r-10 mt-2"></i>
                                    Admin
                                </a>
                            @endif

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
           document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt fa-fw mr-2 text-gray-400"></i>
                                Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                @endguest
                <!-- Button show menu -->
                <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </div>
            </div>
        </div>

        <style>
            .dropdown-item:active {
                background-color: #717fe0;
                /* Warna yang diinginkan */
                color: #fff;
                /* Warna teks jika perlu */
            }
        </style>


        <!-- Menu Mobile -->
        <div class="menu-mobile">

            <ul class="main-menu-m">
                <li class="{{ Route::currentRouteName() == 'user.home' ? 'active-menu' : '' }}">
                    <a href="{{ route('user.home') }}">Home</a>
                </li>

                <li class="{{ Route::currentRouteName() == 'user.products.index' ? 'active-menu' : '' }}">
                    <a href="{{ route('user.products.index') }}">Product</a>
                </li>

                <li class="{{ Route::currentRouteName() == 'user.categories.index' ? 'active-menu' : '' }}">
                    <a href="{{ route('user.categories.index') }}">Category</a>
                    <ul class="sub-menu">
                        @foreach ($categories as $category)
                            <li><a
                                    href="{{ route('user.categories.products', $category->id) }}">{{ $category->category_name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <li class="{{ Route::currentRouteName() == 'blog' ? 'active-menu' : '' }}">
                    <a href="">Blog</a>
                </li>

                <li class="{{ Route::currentRouteName() == 'about' ? 'active-menu' : '' }}">
                    <a href="">About</a>
                </li>

                <li class="{{ Route::currentRouteName() == 'contact' ? 'active-menu' : '' }}">
                    <a href="">Contact</a>
                </li>
            </ul>
        </div>
        <!-- Modal Search -->
        <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
            <div class="container-search-header">
                <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                    <img src="images/icons/icon-close2.png" alt="CLOSE">
                </button>

                <form class="wrap-search-header flex-w p-l-15">
                    <button class="flex-c-m trans-04">
                        <i class="zmdi zmdi-search"></i>
                    </button>
                    <input class="plh3" type="text" name="search" placeholder="Search...">
                </form>
            </div>
        </div>
    </header>

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

                            <div class="layer-slick1 animated visible-false" data-appear="lightSpeedIn"
                                data-delay="800">
                                <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                    Jackets & Coats
                                </h2>
                            </div>

                            <div class="layer-slick1 animated visible-false" data-appear="slideInUp"
                                data-delay="1600">
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

                <div class="item-slick1" style="background-image: url(images/slide-03.jpg);">
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

    <div class="sec-banner bg0 p-t-80 p-b-50">
        <div class="container">
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

    <div class="bg0 m-t-23 p-b-140">
        <div class="container">
            <div class="p-b-10">
                <h3 class="ltext-103 cl5">
                    Product Overview
                </h3>
            </div>
            <div class="flex-w flex-sb-m p-b-52">
                <!-- Tombol Filter Kategori -->
                <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                    <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                        All Products
                    </button>
                    @foreach ($categories as $category)
                        <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                            data-filter=".{{ strtolower($category->category_name) }}">
                            {{ $category->category_name }}
                        </button>
                    @endforeach
                </div>

                <!-- Tombol Filter dan Search -->
                <div class="flex-w flex-c-m m-tb-10">
                    <div
                        class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
                        <i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
                        <i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                        Filter
                    </div>

                    <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
                        <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                        <i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                        Search
                    </div>
                </div>
            </div>

            <!-- Produk -->
            <div class="row isotope-grid">
                @foreach ($products as $product)
                    <div
                        class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item {{ strtolower($product->category->category_name) }}">
                        <!-- Block2 -->
                        <div class="block2">
                            <div class="block2-pic hov-img0">
                                <!-- Gambar Produk, klik gambar ini akan mengarahkan ke halaman detail -->
                                <a href="{{ route('user.products.show', $product->id) }}">
                                    <img src="{{ asset('storage/products/' . (is_array($product->image) ? $product->image[0] : $product->image)) }}"
                                        alt="{{ $product->title }}">
                                </a>

                                <!-- Tombol Quick View -->
                                <a href="{{ route('user.products.show', $product->id) }}"
                                    class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                                    Add To Cart
                                </a>
                            </div>

                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l">
                                    <!-- Klik Brand untuk masuk ke halaman detail produk -->
                                    <a href="{{ route('user.products.show', $product->id) }}"
                                        class="stext-106 cl5 hov-cl1 trans-04 m-b-3" style="font-weight: bold;">
                                        {{ $product->brand }}
                                    </a>

                                    <!-- Nama produk, klik nama ini akan mengarahkan ke halaman detail -->
                                    <a href="{{ route('user.products.show', $product->id) }}"
                                        class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                        {{ $product->title }}
                                    </a>

                                    <!-- Klik Harga untuk masuk ke halaman detail produk -->
                                    <a href="{{ route('user.products.show', $product->id) }}"
                                        class="stext-105 cl5 hov-cl1 trans-04">
                                        Rp.{{ number_format($product->price, 2) }}
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

    {{-- Footer --}}
    @include('layouts.partials.footer')

    <!--===============================================================================================-->
    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/animsition/js/animsition.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script>
        $(".js-select2").each(function() {
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        })
    </script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/slick/slick.min.js') }}"></script>
    <script src="{{ asset('js/slick-custom.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/parallax100/parallax100.js') }}"></script>
    <script>
        $('.parallax100').parallax100();
    </script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
    <script>
        $('.gallery-lb').each(function() {
            $(this).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {
                    enabled: true
                },
                mainClass: 'mfp-fade'
            });
        });
    </script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/isotope/isotope.pkgd.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $('.js-addwish-b2').on('click', function(e) {
            e.preventDefault();
        });

        $('.js-addwish-b2').each(function() {
            var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
            $(this).on('click', function() {
                swal(nameProduct, "is added to wishlist !", "success");

                $(this).addClass('js-addedwish-b2');
                $(this).off('click');
            });
        });

        $('.js-addwish-detail').each(function() {
            var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

            $(this).on('click', function() {
                swal(nameProduct, "is added to wishlist !", "success");

                $(this).addClass('js-addedwish-detail');
                $(this).off('click');
            });
        });

        /*---------------------------------------------*/

        $('.js-addcart-detail').each(function() {
            var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
            $(this).on('click', function() {
                swal(nameProduct, "is added to cart !", "success");
            });
        });
    </script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script>
        $('.js-pscroll').each(function() {
            $(this).css('position', 'relative');
            $(this).css('overflow', 'hidden');
            var ps = new PerfectScrollbar(this, {
                wheelSpeed: 1,
                scrollingThreshold: 1000,
                wheelPropagation: false,
            });

            $(window).on('resize', function() {
                ps.update();
            })
        });
    </script>
    <!--===============================================================================================-->
    <script src="{{ asset('js/main.js') }}"></script>

    @stack('scripts')

</body>

</html>
