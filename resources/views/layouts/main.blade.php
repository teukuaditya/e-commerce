<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>DRVN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/drvn-logo.png') }}" />

    <!-- Google Fonts: Poppins (global) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    {{-- FA lokal (5 Free, satu-satunya) — CDN FA6-beta dihapus: kelas fa/fa4 + fas semuanya didukung --}}
    <link rel="stylesheet" href="{{ asset('libs/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">

    {{-- CSS plugin berat dimuat kondisional via @push('styles') di view terkait:
         select2 (checkout, detail produk), jquery-ui (address) --}}
    <link rel="stylesheet" href="{{ asset('vendor/slick/slick.css') }}">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ filemtime(public_path('css/main.css')) }}">

    <!-- Theme upgrade (design system global) -->
    <link rel="stylesheet" href="{{ asset('css/theme-upgrade.css') }}?v={{ filemtime(public_path('css/theme-upgrade.css')) }}">

    <!-- Navbar floating glassmorphism (mudah dicabut: hapus baris ini) -->
    <link rel="stylesheet" href="{{ asset('css/navbar-floating.css') }}?v={{ filemtime(public_path('css/navbar-floating.css')) }}">


    <!-- Slot CSS per-halaman (harus setelah style global agar bisa override) -->
    @stack('styles')

    {{-- Midtrans Snap tidak lagi dipuat global — hanya di page yang
         membutuhkan (checkout, order) via @push('scripts'). --}}
</head>

<body class="@yield('body-class')">

    <!-- Main Content -->
    <div id="content">
        {{-- Header --}}
        @include('layouts.partials.navbar')
        @include('layouts.partials.mobile-nav')

        <main>
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.partials.footer')
    </div>
    <!-- End of Main Content -->

    <!--===============================================================================================-->
    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!--===============================================================================================-->

    <!--===============================================================================================-->
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <!--===============================================================================================-->
    {{-- select2 pindah kondisional: @push('scripts') di checkout & detail produk --}}
    <!--===============================================================================================-->
    {{-- daterangepicker/moment, parallax100, MagnificPopup, perfect-scrollbar, sweetalert
        DIHAPUS — tidak ada satu view yang memakai plugin tersebut --}}
    {{-- slick & isotope pindah kondisional: hanya halaman yang benar2 memakainya
        (slick: detail produk; isotope: filter/search & halaman kategori) --}}
    <!--===============================================================================================-->
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        /* Toast mini pengganti sweetalert (wishlist dll.) */
        window.tuToast = function (title, msg) {
            var el = document.createElement('div');
            el.className = 'tu-toast';
            el.innerHTML = '<strong></strong><span></span>';
            el.children[0].textContent = title || '';
            el.children[1].textContent = msg || '';
            document.body.appendChild(el);
            setTimeout(function () {
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 350);
            }, 2200);
        };

        $('.js-addwish-b2, .js-addwish-detail').on('click', function (e) {
            e.preventDefault();
        });

        $('.js-addwish-b2').each(function () {
            var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
            $(this).on('click', function () {
                window.tuToast(nameProduct, "is added to wishlist !");
                $(this).addClass('js-addedwish-b2');
                $(this).off('click');
            });
        });

        $('.js-addwish-detail').each(function () {
            var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();
            $(this).on('click', function () {
                window.tuToast(nameProduct, "is added to wishlist !");
                $(this).addClass('js-addedwish-detail');
                $(this).off('click');
            });
        });
    </script>
    {{-- JS/CSS per-halaman dimuat lewat slot ini --}}
    @stack('scripts')

</body>

</html>