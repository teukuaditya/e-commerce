<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>DRVN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ !empty($company['favicon']) ? asset('storage/'.$company['favicon']) : asset('images/drvn-logo.png') }}" />

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
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    </script>
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
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        /* SweetAlert2 toast + confirm helpers */
        var _swalToast = (window.Swal ? Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true
        }) : null);

        window.tuToast = function (title, msg) {
            var text = [title, msg].filter(Boolean).join(' — ');
            if (_swalToast) {
                _swalToast.fire({ icon: 'success', title: text });
                return;
            }
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

        // Override native alert -> SweetAlert2
        window.alert = function (msg) {
            if (window.Swal) {
                Swal.fire({
                    text: String(msg == null ? '' : msg),
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#111827'
                });
            }
        };

        window.swalConfirm = function (opts) {
            opts = opts || {};
            if (!window.Swal) return Promise.resolve(confirm(opts.text || 'Yakin?'));
            return Swal.fire({
                title: opts.title || 'Konfirmasi',
                text: opts.text || '',
                icon: opts.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: opts.confirmText || 'Ya, lanjutkan',
                cancelButtonText: opts.cancelText || 'Batal',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-dark px-4 py-2 mx-1',
                    cancelButton: 'btn btn-outline-secondary px-4 py-2 mx-1'
                }
            }).then(function (r) { return r.isConfirmed; });
        };

        window.confirmLogout = function (formId) {
            var id = formId || 'logout-form';
            var form = document.getElementById(id);
            if (!form) return;
            window.swalConfirm({
                title: 'Sign Out',
                text: 'Apakah kamu yakin ingin keluar dari akun ini?',
                icon: 'question',
                confirmText: 'Ya, keluar',
                cancelText: 'Batal'
            }).then(function (ok) {
                if (ok) form.submit();
            });
        };

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.dataset || !form.dataset.confirm) return;
            if (form.dataset.confirmed === '1') { form.dataset.confirmed = '0'; return; }
            e.preventDefault();
            window.swalConfirm({
                title: form.dataset.confirmTitle || 'Konfirmasi',
                text: form.dataset.confirm,
                icon: 'warning',
                confirmText: form.dataset.confirmBtn || 'Ya, hapus'
            }).then(function (ok) {
                if (ok) { form.dataset.confirmed = '1'; form.submit(); }
            });
        });

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