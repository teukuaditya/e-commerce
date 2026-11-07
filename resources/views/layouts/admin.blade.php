<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="DRVN E-Commerce Admin Dashboard">
    <meta name="author" content="Teuku Aditya">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DRVN &bull; Admin Command Center</title>

    <link rel="icon" type="image/png" href="{{ asset('images/drvn-logo.png') }}" />

    <!-- Google Fonts & Clash Grotesk CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/clash-grotesk" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=clash-grotesk@400,500,600,700,800&display=swap" rel="stylesheet">

    <!-- Iconify Web Component (Lucide icon set) -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

    <!-- Tailwind CSS (CDN with preflight disabled to keep Bootstrap modals/tables intact) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            },
            theme: {
                extend: {
                    colors: {
                        sidebar: '#1A1C29',
                        accent: '#6366F1',
                        emerald: {
                            500: '#10B981',
                        },
                        amber: {
                            500: '#F59E0B',
                        },
                        danger: {
                            500: '#EF4444',
                        }
                    },
                    fontFamily: {
                        clash: ['"Clash Grotesk"', 'sans-serif'],
                        jakarta: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        };
    </script>

    <!-- Legacy styles (Bootstrap v4 grid, modals, datatables) -->
    <link rel="stylesheet" href="{{ asset('libs/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sb-admin-2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/datatables/dataTables.bootstrap4.min.css')}}">

    <!-- Modern Admin Dashboard Styling System -->
    <link rel="stylesheet" href="{{ asset('css/modern-admin-dashboard.css') }}?v={{ file_exists(public_path('css/modern-admin-dashboard.css')) ? filemtime(public_path('css/modern-admin-dashboard.css')) : time() }}">

    <style>
        .hover-bg-slate:hover {
            background-color: #F1F5F9;
        }
    </style>

    @stack('styles')
</head>

<body id="page-top" class="font-jakarta">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Fixed Dark Sidebar (w-64, bg-[#1A1C29]) -->
        @include('layouts.partials.admin-nav')

        <!-- Content Area -->
        <div id="content-wrapper" class="adm-modern-content d-flex flex-column">

            <!-- Topbar Header -->
            @include('layouts.partials.admin-topbar')

            <!-- Main Content with Generous Padding (#F7F9FC) -->
            <main class="flex-grow-1 p-3 p-sm-4 p-lg-5" style="background-color: #F7F9FC;">
                @yield('content')
            </main>

            <!-- Modern Footer -->
            <footer class="py-3 px-4 text-center border-top" style="background: #FFFFFF; border-color: rgba(226, 232, 240, 0.8) !important;">
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between text-muted font-jakarta" style="font-size: 0.78rem;">
                    <span>&copy; {{ date('Y') }} DRVN Store &bull; Admin Developer Suite</span>
                    <span class="font-mono-code text-indigo-600 font-weight-bold">DRVN-CORE v2.4.0 [PRODUCTION TELEMETRY]</span>
                </div>
            </footer>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none shadow" href="#page-top" style="width: 44px; height: 44px; background: #6366F1; z-index: 1000;">
        <iconify-icon icon="lucide:arrow-up" width="20" height="20"></iconify-icon>
    </a>

    <!-- Overlay drawer mobile + container toast -->
    <div class="adm-drawer-backdrop" id="admOverlay" aria-hidden="true"></div>
    <div id="adm-toasts" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px;"></div>

    <!-- Core JavaScript (Bootstrap v4 required by sb-admin-2 modals & dropdowns) -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom scripts for DataTables -->
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>

    <script>
        /* ==== MODERN ADMIN DASHBOARD INTERACTIVITY ==== */
        (function () {
            // Global Toast Notification
            window.admin = window.admin || {};
            window.admin.toast = function (msg, type) {
                var wrap = document.getElementById('adm-toasts');
                if (!wrap) return;
                var el = document.createElement('div');
                var isErr = type === 'err';
                el.style.cssText = 'background: ' + (isErr ? '#EF4444' : '#10B981') + '; color: #fff; padding: 12px 18px; border-radius: 14px; font-size: 0.85rem; font-family: var(--font-body); font-weight: 600; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); transform: translateY(20px); opacity: 0;';
                el.innerHTML = '<iconify-icon icon="' + (isErr ? 'lucide:alert-circle' : 'lucide:check-circle-2') + '" width="20" height="20"></iconify-icon><span>' + msg + '</span>';
                wrap.appendChild(el);

                // Animate in
                requestAnimationFrame(function () {
                    el.style.transform = 'translateY(0)';
                    el.style.opacity = '1';
                });

                // Auto dismiss
                setTimeout(function () {
                    el.style.transform = 'translateX(20px)';
                    el.style.opacity = '0';
                    setTimeout(function () { el.remove(); }, 320);
                }, 3200);
            };

            // Session flash alerts
            @if (session('success'))
                window.admin.toast({{ json_encode(session('success')) }}, 'ok');
            @endif
            @if (session('error'))
                window.admin.toast({{ json_encode(session('error')) }}, 'err');
            @endif

            // Mobile Sidebar Drawer Toggle
            var sidebar = document.getElementById('adminSidebar');
            var mobileBtn = document.getElementById('adminMobileToggle');
            var backdrop = document.getElementById('adminDrawerBackdrop');

            function toggleDrawer() {
                if (!sidebar) return;
                sidebar.classList.toggle('is-open');
                if (backdrop) backdrop.classList.toggle('is-visible');
            }

            function closeDrawer() {
                if (sidebar) sidebar.classList.remove('is-open');
                if (backdrop) backdrop.classList.remove('is-visible');
            }

            if (mobileBtn) mobileBtn.addEventListener('click', toggleDrawer);
            if (backdrop) backdrop.addEventListener('click', closeDrawer);

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) closeDrawer();
            });

            // Refresh Button Spin Animation & Live Telemetry Fetch
            var btnRefresh = document.getElementById('btnRefreshDashboard');
            var refreshIcon = document.getElementById('refreshIcon');
            if (btnRefresh && refreshIcon) {
                btnRefresh.addEventListener('click', function () {
                    refreshIcon.classList.add('spin-1s');
                    
                    fetch('{{ route("admin.dashboard.telemetry") }}')
                        .then(function (res) { return res.json(); })
                        .then(function (payload) {
                            if (payload.status === 'ok' && typeof window.updateDashboardTelemetry === 'function') {
                                window.updateDashboardTelemetry(payload.data);
                            }
                            window.admin.toast('Dashboard data refreshed successfully', 'ok');
                        })
                        .catch(function () {
                            window.admin.toast('Dashboard reloaded', 'ok');
                        })
                        .finally(function () {
                            setTimeout(function () {
                                refreshIcon.classList.remove('spin-1s');
                            }, 1000);
                        });
                });
            }

            // Keyboard shortcut (Ctrl+K or Cmd+K) for search
            window.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    var searchInput = document.getElementById('admGlobalSearch');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });

            // Live Autocomplete for Global Search
            var searchInput = document.getElementById('admGlobalSearch');
            var searchResults = document.getElementById('admSearchResults');
            var searchResultsContent = document.getElementById('admSearchResultsContent');
            var searchTimer = null;

            if (searchInput && searchResults && searchResultsContent) {
                searchInput.addEventListener('input', function () {
                    var query = searchInput.value.trim();
                    clearTimeout(searchTimer);
                    if (query.length < 2) {
                        searchResults.classList.add('d-none');
                        return;
                    }

                    searchTimer = setTimeout(function () {
                        fetch('{{ route("admin.quickSearch") }}?q=' + encodeURIComponent(query))
                            .then(function (r) { return r.json(); })
                            .then(function (res) {
                                var html = '';
                                var hasResults = false;

                                // Products
                                if (res.products && res.products.length > 0) {
                                    hasResults = true;
                                    html += '<div class="px-2 py-1 font-mono-code text-indigo-600 font-weight-bold" style="font-size: 0.68rem; letter-spacing: 0.08em;">PRODUK (' + res.products.length + ')</div>';
                                    res.products.forEach(function (p) {
                                        html += '<a href="' + p.url + '" class="d-flex align-items-center gap-2 p-2 rounded-xl text-decoration-none text-slate-800 hover-bg-slate" style="border-radius: 10px;">' +
                                            '<iconify-icon icon="lucide:package" class="text-indigo-500" width="16"></iconify-icon>' +
                                            '<div class="flex-grow-1 text-truncate"><span class="font-weight-bold" style="font-size: 0.82rem;">' + p.title + '</span> <span class="text-muted" style="font-size: 0.72rem;">(' + p.brand + ')</span></div>' +
                                            '<span class="font-mono-code text-indigo-600 font-weight-bold" style="font-size: 0.75rem;">' + p.price_formatted + '</span>' +
                                        '</a>';
                                    });
                                }

                                // Transactions
                                if (res.transactions && res.transactions.length > 0) {
                                    hasResults = true;
                                    html += '<div class="px-2 pt-2 pb-1 font-mono-code text-emerald-600 font-weight-bold border-top mt-1" style="font-size: 0.68rem; letter-spacing: 0.08em;">TRANSAKSI (' + res.transactions.length + ')</div>';
                                    res.transactions.forEach(function (t) {
                                        html += '<a href="' + t.url + '" class="d-flex align-items-center gap-2 p-2 rounded-xl text-decoration-none text-slate-800 hover-bg-slate" style="border-radius: 10px;">' +
                                            '<iconify-icon icon="lucide:receipt" class="text-emerald-500" width="16"></iconify-icon>' +
                                            '<div class="flex-grow-1 text-truncate"><span class="font-weight-bold font-mono-code" style="font-size: 0.82rem;">#' + t.order_id + '</span> <span class="text-muted" style="font-size: 0.72rem;">' + t.customer_name + '</span></div>' +
                                            '<span class="badge" style="font-size: 0.65rem; background: rgba(16, 185, 129, 0.15); color: #047857;">' + t.status + '</span>' +
                                        '</a>';
                                    });
                                }

                                // Users
                                if (res.users && res.users.length > 0) {
                                    hasResults = true;
                                    html += '<div class="px-2 pt-2 pb-1 font-mono-code text-amber-600 font-weight-bold border-top mt-1" style="font-size: 0.68rem; letter-spacing: 0.08em;">PENGGUNA (' + res.users.length + ')</div>';
                                    res.users.forEach(function (u) {
                                        html += '<a href="' + u.url + '" class="d-flex align-items-center gap-2 p-2 rounded-xl text-decoration-none text-slate-800 hover-bg-slate" style="border-radius: 10px;">' +
                                            '<iconify-icon icon="lucide:user" class="text-amber-500" width="16"></iconify-icon>' +
                                            '<div class="flex-grow-1 text-truncate"><span class="font-weight-bold" style="font-size: 0.82rem;">' + u.name + '</span> <span class="text-muted" style="font-size: 0.72rem;">(' + u.email + ')</span></div>' +
                                            '<span class="badge" style="font-size: 0.65rem; background: #F1F5F9; color: #475569;">' + u.role + '</span>' +
                                        '</a>';
                                    });
                                }

                                if (!hasResults) {
                                    html = '<div class="p-3 text-center text-muted font-jakarta" style="font-size: 0.82rem;">Tidak ditemukan hasil untuk "' + query + '"</div>';
                                }

                                searchResultsContent.innerHTML = html;
                                searchResults.classList.remove('d-none');
                            });
                    }, 250);
                });

                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('d-none');
                    }
                });

                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        searchResults.classList.add('d-none');
                    } else if (e.key === 'Enter') {
                        var firstLink = searchResultsContent.querySelector('a');
                        if (firstLink) {
                            window.location.href = firstLink.getAttribute('href');
                        } else if (searchInput.value.trim().length > 0) {
                            window.location.href = '{{ route("admin.products.index") }}?search=' + encodeURIComponent(searchInput.value.trim());
                        }
                    }
                });
            }
        })();
    </script>

    {{-- Page-specific scripts --}}
    @stack('scripts')

</body>

</html>
