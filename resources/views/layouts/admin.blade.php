<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DRVN-Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('images/drvn-logo.png') }}" />

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="{{ asset('libs/fontawesome-free/css/all.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Terapkan tema tersimpan SEBELUM CSS render (hindari flash terang) -->
    <script>
        try {
            var t = localStorage.getItem('adm-theme');
            if (t === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
        } catch (e) { /* localStorage diblokir — abaikan */ }
    </script>

    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="{{ asset('css/sb-admin-2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/datatables/dataTables.bootstrap4.min.css')}}">

    <!-- Admin upgrade (polish sb-admin-2) -->
    <link rel="stylesheet" href="{{ asset('css/admin-upgrade.css') }}">

    <!-- Admin theme modern (design tokens + dark mode) -->
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ filemtime(public_path('css/admin-theme.css')) }}">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        @include('layouts.partials.admin-nav')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                @include('layouts.partials.admin-topbar')


                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-4">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright © 2025 All rights reserved | Teuku Aditya</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Core JavaScript (Bootstrap v4 — dibutuhkan sb-admin-2 & data-toggle) -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Overlay drawer mobile + container toast -->
    <div class="adm-overlay" id="admOverlay" aria-hidden="true"></div>
    <div id="adm-toasts" aria-live="polite"></div>

    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>

    {{-- JS ekstra per-halaman --}}
    @stack('scripts')

    <script>
        /* ==== DRVN ADMIN GLUE: tema, drawer, toast ==== */
        (function () {
            /* Toggle dark/light — tersimpan di localStorage */
            function applyTheme(t) {
                document.documentElement.setAttribute('data-theme', t);
                document.querySelectorAll('.adm-theme-toggle i')
                    .forEach(function (i) { i.className = 'fas ' + (t === 'dark' ? 'fa-sun' : 'fa-moon'); });
            }
            var saved = 'light';
            try { saved = localStorage.getItem('adm-theme') || 'light'; } catch (e) {}
            applyTheme(saved);
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.adm-theme-toggle');
                if (!btn) return;
                var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                try { localStorage.setItem('adm-theme', next); } catch (err) {}
                applyTheme(next);
            });

            /* Drawer sidebar di mobile + overlay */
            var overlay = document.getElementById('admOverlay');
            var sbToggleTop = document.getElementById('sidebarToggleTop');
            function closeDrawer() {
                document.body.classList.remove('sidebar-mobile-open');
                var sb = document.querySelector('.sidebar');
                if (sb && window.innerWidth < 768) sb.classList.remove('toggled');
                overlay.classList.remove('show');
            }
            if (overlay) overlay.addEventListener('click', closeDrawer);
            if (sbToggleTop) sbToggleTop.addEventListener('click', function () {
                document.body.classList.add('sidebar-mobile-open');
                overlay.classList.add('show');
            });
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) closeDrawer();
            });

            /* Toast global: admin.toast('pesan', 'ok'|'err') */
            window.admin = window.admin || {};
            window.admin.toast = function (msg, type) {
                var wrap = document.getElementById('adm-toasts');
                if (!wrap) return;
                var el = document.createElement('div');
                el.className = 'adm-toast ' + (type === 'err' ? 'err' : 'ok');
                el.innerHTML = '<i class="fas ' + (type === 'err' ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i><span></span>';
                el.querySelector('span').textContent = msg;
                wrap.appendChild(el);
                setTimeout(function () {
                    el.style.transition = 'opacity .3s ease, transform .3s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(20px)';
                    setTimeout(function () { el.remove(); }, 320);
                }, 3200);
            };

            /* Pesan sesi (session('success')) otomatis jadi toast juga */
            @if (session('success'))
                window.admin.toast({{ json_encode(session('success')) }}, 'ok');
            @endif
            @if (session('error'))
                window.admin.toast({{ json_encode(session('error')) }}, 'err');
            @endif
        })();
    </script>



</body>

</html>