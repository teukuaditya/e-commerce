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

    <link rel="icon" type="image/png" href="{{ !empty($company['favicon']) ? asset('storage/'.$company['favicon']) : asset('images/drvn-logo.png') }}" />

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
    <script>
        (function () {
            try {
                if (window.innerWidth >= 768 && localStorage.getItem('adm_sidebar_collapsed') === '1') {
                    document.body.classList.add('adm-sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Fixed Dark Sidebar (w-64, bg-[#1A1C29]) -->
        @include('layouts.partials.admin-nav')

        <!-- Desktop Sidebar Toggle (in sidebar notch) -->
        <button id="adminDesktopToggle" class="adm-sidebar-edge-toggle d-none d-md-flex" aria-label="Toggle Sidebar" type="button">
            <iconify-icon icon="lucide:chevron-left" id="adminDesktopToggleIcon" width="18" height="18"></iconify-icon>
        </button>

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
                </div>
            </footer>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none shadow" href="#page-top" style="width: 44px; height: 44px; background: #6366F1; z-index: 1000; opacity:0; visibility:hidden; transition: opacity .25s ease, visibility .25s ease, transform .25s ease; transform: translateY(8px);">
        <iconify-icon icon="lucide:arrow-up" width="20" height="20"></iconify-icon>
    </a>
    <script>
        (function () {
            var btn = document.querySelector('.scroll-to-top');
            if (!btn) return;
            function toggle() {
                var show = window.scrollY > 200;
                btn.style.opacity = show ? '1' : '0';
                btn.style.visibility = show ? 'visible' : 'hidden';
                btn.style.transform = show ? 'translateY(0)' : 'translateY(8px)';
            }
            window.addEventListener('scroll', toggle, { passive: true });
            toggle();
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>

    <!-- Overlay drawer mobile + container toast & live alert cards -->
    <div class="adm-drawer-backdrop" id="admOverlay" aria-hidden="true"></div>
    <div id="adm-toasts" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px;"></div>
    <div id="adm-live-alerts" style="position: fixed; top: 24px; right: 24px; z-index: 10000; display: flex; flex-direction: column; gap: 12px; max-width: 420px; width: calc(100vw - 32px); pointer-events: none;"></div>

    <!-- Core JavaScript (Bootstrap v4 required by sb-admin-2 modals & dropdowns) -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom scripts for DataTables -->
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script>
        /* ==== MODERN ADMIN DASHBOARD INTERACTIVITY ==== */
        (function () {
            // Global SweetAlert2 Toast
            window.admin = window.admin || {};
            var admSwalToast = (window.Swal ? Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3200,
                timerProgressBar: true,
                showCloseButton: true,
                customClass: { popup: 'adm-swal-toast' },
                didOpen: function (t) {
                    t.addEventListener('mouseenter', Swal.stopTimer);
                    t.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }) : null);

            // Override native alert -> SweetAlert2
            window.alert = function (msg) {
                if (window.Swal) {
                    Swal.fire({
                        text: String(msg == null ? '' : msg),
                        icon: 'info',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'adm-swal-popup',
                            title: 'adm-swal-title',
                            htmlContainer: 'adm-swal-text',
                            confirmButton: 'adm-swal-btn adm-swal-btn-primary',
                            actions: 'adm-swal-actions'
                        }
                    });
                }
            };

            window.admin.toast = function (msg, type) {
                var isErr = type === 'err';
                if (admSwalToast) {
                    admSwalToast.fire({
                        icon: isErr ? 'error' : 'success',
                        title: msg,
                        timer: isErr ? 5000 : 3200
                    });
                    return;
                }
                // Fallback
                alert(msg);
            };

            // Modern confirm helper
            window.admin.confirm = function (opts) {
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
                        popup: 'adm-swal-popup',
                        title: 'adm-swal-title',
                        htmlContainer: 'adm-swal-text',
                        confirmButton: opts.confirmClass || 'adm-swal-btn adm-swal-btn-danger',
                        cancelButton: 'adm-swal-btn adm-swal-btn-cancel',
                        actions: 'adm-swal-actions'
                    }
                }).then(function (r) { return r.isConfirmed; });
            };

            window.confirmLogout = function (formId) {
                var id = formId || 'logout-form';
                var form = document.getElementById(id);
                if (!form) return;
                window.admin.confirm({
                    title: 'Sign Out',
                    text: 'Apakah kamu yakin ingin keluar dari akun ini?',
                    icon: 'question',
                    confirmText: 'Ya, keluar',
                    confirmClass: 'adm-swal-btn adm-swal-btn-danger'
                }).then(function (ok) {
                    if (ok) form.submit();
                });
            };

            // Global confirm handler for forms with data-confirm
            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || !form.dataset || !form.dataset.confirm) return;
                if (form.dataset.confirmed === '1') { form.dataset.confirmed = '0'; return; }
                e.preventDefault();
                window.admin.confirm({
                    title: form.dataset.confirmTitle || 'Konfirmasi',
                    text: form.dataset.confirm,
                    icon: form.dataset.confirmIcon || 'warning',
                    confirmText: form.dataset.confirmBtn || 'Ya, hapus',
                    confirmClass: form.dataset.confirmClass || 'adm-swal-btn adm-swal-btn-danger'
                }).then(function (ok) {
                    if (ok) { form.dataset.confirmed = '1'; form.submit(); }
                });
            });

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

            // Desktop Sidebar Collapse Toggle
            var desktopToggle = document.getElementById('adminDesktopToggle');
            var desktopToggleIcon = document.getElementById('adminDesktopToggleIcon');
            function syncDesktopSidebarIcon() {
                if (!desktopToggleIcon) return;
                var collapsed = document.body.classList.contains('adm-sidebar-collapsed');
                desktopToggleIcon.setAttribute('icon', collapsed ? 'lucide:chevron-right' : 'lucide:chevron-left');
                if (desktopToggle) desktopToggle.setAttribute('aria-label', collapsed ? 'Show Sidebar' : 'Hide Sidebar');
            }
            syncDesktopSidebarIcon();
            if (desktopToggle) {
                desktopToggle.addEventListener('click', function () {
                    if (window.innerWidth < 768) return;
                    var collapsed = document.body.classList.toggle('adm-sidebar-collapsed');
                    try { localStorage.setItem('adm_sidebar_collapsed', collapsed ? '1' : '0'); } catch (e) {}
                    syncDesktopSidebarIcon();
                });
            }

            // Refresh Button - refresh halaman aktif
            var btnRefresh = document.getElementById('btnRefreshDashboard');
            var refreshIcon = document.getElementById('refreshIcon');
            if (btnRefresh && refreshIcon) {
                btnRefresh.addEventListener('click', function () {
                    refreshIcon.classList.add('spin-1s');
                    btnRefresh.disabled = true;
                    setTimeout(function () {
                        window.location.reload();
                    }, 350);
                });
            }

            // Modern file picker filename
            document.addEventListener('change', function (e) {
                var input = e.target;
                if (!input || !input.classList || !input.classList.contains('adm-file-input')) return;
                var name = (input.files && input.files[0]) ? input.files[0].name : 'No file chosen';
                var label = document.querySelector('[data-file-name-for="' + input.id + '"]');
                if (label) label.textContent = name;
            });
            $(document).on('show.bs.modal', '.modal', function () {
                $(this).find('.adm-file-input').each(function () { this.value = ''; });
                $(this).find('[data-file-name-for]').each(function () {
                    var $el = $(this);
                    if (!$el.data('default-text')) $el.data('default-text', $.trim($el.text()) || 'No file chosen');
                    $el.text($el.data('default-text'));
                });
            });

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

            /* ==== REAL-TIME POLLING & ACTIVE ORDER NOTIFICATIONS ==== */
            (function () {
                var lastTxId = null;
                var lastTxStatus = null;
                var isFirstPoll = true;

                // Web Audio API Synthesized Chime (Tanpa butuh file mp3 eksternal)
                function playNotificationSound(isSettlement) {
                    try {
                        var AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (!AudioCtx) return;
                        var ctx = new AudioCtx();
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }
                        var now = ctx.currentTime;
                        if (isSettlement) {
                            // 3-note celebration chime (C5 -> E5 -> G5)
                            [523.25, 659.25, 783.99].forEach(function (freq, i) {
                                var osc = ctx.createOscillator();
                                var gain = ctx.createGain();
                                osc.type = 'sine';
                                osc.frequency.setValueAtTime(freq, now + (i * 0.11));
                                gain.gain.setValueAtTime(0.2, now + (i * 0.11));
                                gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.11) + 0.35);
                                osc.connect(gain);
                                gain.connect(ctx.destination);
                                osc.start(now + (i * 0.11));
                                osc.stop(now + (i * 0.11) + 0.35);
                            });
                        } else {
                            // 2-tone pleasant alert (D5 -> A5)
                            var osc1 = ctx.createOscillator();
                            var gain1 = ctx.createGain();
                            osc1.type = 'sine';
                            osc1.frequency.setValueAtTime(587.33, now);
                            osc1.frequency.exponentialRampToValueAtTime(880, now + 0.12);
                            gain1.gain.setValueAtTime(0.25, now);
                            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                            osc1.connect(gain1);
                            gain1.connect(ctx.destination);
                            osc1.start(now);
                            osc1.stop(now + 0.5);
                        }
                    } catch (e) {
                        // Audio autoplay restricted before user interaction
                    }
                }

                // Tampilkan Popup Alert Pesanan Aktif di Layar
                function showLiveOrderPopup(tx, isSettlement) {
                    var container = document.getElementById('adm-live-alerts');
                    if (!container) return;

                    var card = document.createElement('div');
                    card.style.cssText = 'pointer-events: auto; background: #FFFFFF; border-radius: 18px; padding: 16px 18px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2), 0 0 0 1px ' + (isSettlement ? 'rgba(16,185,129,0.35)' : 'rgba(99,102,241,0.35)') + '; border-left: 5px solid ' + (isSettlement ? '#10B981' : '#6366F1') + '; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); transform: translateX(110%); opacity: 0; position: relative; overflow: hidden;';

                    var titleText = isSettlement ? 'Pembayaran Berhasil Diverifikasi!' : 'Pesanan Baru Masuk!';
                    var titleColor = isSettlement ? '#059669' : '#4F46E5';
                    var iconName = isSettlement ? 'lucide:badge-check' : 'lucide:shopping-bag';
                    var iconBg = isSettlement ? 'rgba(16,185,129,0.12)' : 'rgba(99,102,241,0.12)';
                    var badgeText = isSettlement ? 'Settlement' : 'Pending Payment';
                    var badgeBg = isSettlement ? '#DCFCE7' : '#FEF3C7';
                    var badgeColor = isSettlement ? '#15803D' : '#B45309';

                    card.innerHTML =
                        '<div class="d-flex align-items-start justify-content-between gap-3">' +
                            '<div class="d-flex align-items-start gap-3">' +
                                '<div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; border-radius: 14px; background: ' + iconBg + '; color: ' + titleColor + ';">' +
                                    '<iconify-icon icon="' + iconName + '" width="24" height="24"></iconify-icon>' +
                                '</div>' +
                                '<div>' +
                                    '<div class="font-jakarta font-weight-bold" style="font-size: 0.95rem; color: #0F172A;">' + titleText + '</div>' +
                                    '<div class="font-mono-code mt-0.5" style="font-size: 0.8rem; font-weight: 700; color: #334155;">#' + (tx.order_id || 'ORDER') + '</div>' +
                                    '<div class="text-slate-500 font-jakarta mt-1" style="font-size: 0.78rem;">' +
                                        '<span class="font-weight-bold text-slate-700">' + (tx.customer_name || 'Pelanggan') + '</span> &bull; ' +
                                        '<span class="font-mono-code font-weight-bold text-slate-900">' + (tx.gross_amount_formatted || ('Rp ' + tx.gross_amount)) + '</span>' +
                                    '</div>' +
                                    '<div class="d-flex align-items-center gap-1.5 mt-2">' +
                                        '<span class="badge" style="background: ' + badgeBg + '; color: ' + badgeColor + '; font-size: 0.68rem; padding: 3px 7px; border-radius: 6px;">' + badgeText + '</span>' +
                                        (tx.payment_type ? '<span class="badge" style="background: #F1F5F9; color: #475569; font-size: 0.68rem; padding: 3px 7px; border-radius: 6px;">' + tx.payment_type.toUpperCase() + '</span>' : '') +
                                        (tx.courier ? '<span class="badge" style="background: #F1F5F9; color: #475569; font-size: 0.68rem; padding: 3px 7px; border-radius: 6px;">' + tx.courier + '</span>' : '') +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                            '<button type="button" class="btn p-1 text-slate-400 hover:text-slate-600 smooth-all close-alert-btn" style="border: none; background: transparent; line-height: 1;" aria-label="Close">' +
                                '<iconify-icon icon="lucide:x" width="18" height="18"></iconify-icon>' +
                            '</button>' +
                        '</div>' +
                        '<div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-slate-100">' +
                            '<span class="text-slate-400 font-mono-code" style="font-size: 0.68rem;">Real-time Telemetry</span>' +
                            '<a href="{{ route("admin.transactions.index") }}" class="btn btn-sm py-1 px-3 rounded-lg font-jakarta font-weight-bold text-decoration-none smooth-all" style="background: ' + (isSettlement ? '#10B981' : '#6366F1') + '; color: #fff; font-size: 0.75rem; border-radius: 8px;">' +
                                'Lihat Transaksi &rarr;' +
                            '</a>' +
                        '</div>';

                    container.appendChild(card);

                    // Slide in animation
                    requestAnimationFrame(function () {
                        card.style.transform = 'translateX(0)';
                        card.style.opacity = '1';
                    });

                    function dismissCard() {
                        card.style.transform = 'translateX(110%)';
                        card.style.opacity = '0';
                        setTimeout(function () { card.remove(); }, 400);
                    }

                    var closeBtn = card.querySelector('.close-alert-btn');
                    if (closeBtn) closeBtn.addEventListener('click', dismissCard);

                    // Auto dismiss setelah 10 detik
                    setTimeout(dismissCard, 10000);

                    // Putar nada notifikasi
                    playNotificationSound(isSettlement);
                }

                // Update List & Badge Notifikasi Topbar secara Real-time
                function updateTopbarNotifications(notifications) {
                    var badgeEl = document.getElementById('notifBadgeCount');
                    var pulseEl = document.getElementById('notifPulseWrapper');
                    var listContainer = document.getElementById('notifListContainer');

                    var count = notifications ? notifications.length : 0;
                    var sig = (notifications || []).map(function (n) { return (n.id || '') + ':' + (n.status || ''); }).join('|');
                    window.__admNotifTotal = count;
                    window.__admNotifSig = sig;
                    var seenSig = window.__admNotifSeenSig || localStorage.getItem('adm_notif_seen_sig') || '';
                    var seenSet = seenSig ? seenSig.split('|') : [];
                    var newCount = 0;
                    (notifications || []).forEach(function (n) {
                        var t = (n.id || '') + ':' + (n.status || '');
                        if (seenSet.indexOf(t) === -1) newCount++;
                    });
                    var hasNew = newCount > 0;

                    var dropdownOpen = !!(document.querySelector('#notificationsDropdown') && document.querySelector('#notificationsDropdown').getAttribute('aria-expanded') === 'true');
                    if (badgeEl) {
                        badgeEl.textContent = (dropdownOpen ? 0 : newCount) + ' Baru';
                    }
                    if (pulseEl) {
                        pulseEl.textContent = newCount > 99 ? '99+' : String(newCount);
                        if (hasNew && !dropdownOpen) {
                            pulseEl.classList.remove('d-none');
                        } else {
                            pulseEl.classList.add('d-none');
                        }
                    }

                    if (listContainer) {
                        if (count > 0) {
                            var html = '';
                            notifications.forEach(function (notif) {
                                var token = (notif.id || '') + ':' + (notif.status || '');
                                var isNew = seenSet.indexOf(token) === -1;
                                html += '<a href="' + notif.url + '" data-notif-token="' + token + '" class="list-group-item list-group-item-action p-3 border-bottom text-decoration-none adm-notif-item ' + (isNew ? 'is-new' : '') + '">' +
                                    '<div class="d-flex align-items-start gap-2">' +
                                        '<span class="p-1.5 rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: ' + notif.icon_bg + '; width: 28px; height: 28px;">' +
                                            '<iconify-icon icon="' + notif.icon + '" width="14" height="14"></iconify-icon>' +
                                        '</span>' +
                                        '<div class="flex-grow-1">' +
                                            '<p class="mb-0 font-weight-bold text-slate-800" style="font-size: 0.8rem;">' + notif.title + '</p>' +
                                            '<span class="text-muted font-mono-code" style="font-size: 0.7rem;">' + notif.desc + ' &bull; ' + notif.time_ago + '</span>' +
                                        '</div>' +
                                        '<span class="adm-notif-new-dot ' + (isNew ? '' : 'd-none') + '" style="width:8px;height:8px;border-radius:9999px;background:#6366F1;flex-shrink:0;margin-top:6px;"></span>' +
                                    '</div>' +
                                '</a>';
                            });
                            listContainer.innerHTML = html;
                        } else {
                            listContainer.innerHTML =
                                '<div class="p-4 text-center text-muted font-jakarta" style="font-size: 0.82rem;">' +
                                    '<iconify-icon icon="lucide:bell-off" width="28" height="28" class="text-slate-400 mb-1 d-block mx-auto"></iconify-icon>' +
                                    'Belum ada notifikasi pesanan' +
                                '</div>';
                        }
                    }
                }

                // Loop Polling Telemetry Real-time (tiap 3.5 detik)
                function pollTelemetry() {
                    fetch('{{ route("admin.dashboard.telemetry") }}')
                        .then(function (res) { return res.json(); })
                        .then(function (payload) {
                            if (payload.status !== 'ok' || !payload.data) return;
                            var data = payload.data;

                            // 1. Jika sedang berada di dashboard, perbarui KPI & charts seketika
                            if (typeof window.updateDashboardTelemetry === 'function') {
                                window.updateDashboardTelemetry(data);
                            }

                            // 2. Selalu update notifikasi lonceng di topbar
                            if (data.notifications) {
                                updateTopbarNotifications(data.notifications);
                            }

                            // 3. Deteksi transaksi baru atau perubahan status
                            var latest = data.latestTransaction;
                            if (latest) {
                                if (!isFirstPoll) {
                                    if (lastTxId !== null && latest.id > lastTxId) {
                                        // Ada pesanan baru masuk!
                                        showLiveOrderPopup(latest, latest.transaction_status === 'settlement');
                                    } else if (lastTxId !== null && latest.id === lastTxId && latest.transaction_status !== lastTxStatus && latest.transaction_status === 'settlement') {
                                        // Status transaksi baru saja berubah jadi lunas!
                                        showLiveOrderPopup(latest, true);
                                    }
                                }
                                lastTxId = latest.id;
                                lastTxStatus = latest.transaction_status;
                            } else {
                                lastTxId = null;
                                lastTxStatus = null;
                            }

                            isFirstPoll = false;
                        })
                        .catch(function () {
                            // Abaikan kegagalan jaringan sementara
                        })
                        .finally(function () {
                            setTimeout(pollTelemetry, 3500);
                        });
                }

                // Mulai polling otomatis
                setTimeout(pollTelemetry, 1500);
            })();
        })();
    </script>

    {{-- Page-specific scripts --}}
    @stack('scripts')

</body>

</html>
