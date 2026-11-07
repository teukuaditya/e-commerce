<!-- Modern Topbar Header -->
<header class="adm-modern-topbar position-relative">

    <div class="d-flex align-items-center gap-3">
        <!-- Mobile Sidebar Toggle -->
        <button id="adminMobileToggle" class="btn p-2 d-md-none rounded-2xl border border-slate-200 bg-white text-slate-700 btn-press" aria-label="Toggle Sidebar Menu" type="button" style="border-radius: 12px;">
            <iconify-icon icon="lucide:menu" width="20" height="20"></iconify-icon>
        </button>

        <!-- Search Bar with Live Autocomplete & Developer Shortcut -->
        <div class="position-relative d-none d-sm-flex align-items-center">
            <span class="position-absolute" style="left: 14px; color: #94A3B8; pointer-events: none; display: flex;">
                <iconify-icon icon="lucide:search" width="18" height="18"></iconify-icon>
            </span>
            <input type="text"
                   id="admGlobalSearch"
                   class="form-control"
                   placeholder="Search products, orders, customers... (Ctrl + K)"
                   aria-label="Global Search"
                   autocomplete="off"
                   style="padding-left: 42px; padding-right: 16px; height: 42px; width: 340px; background: #F1F5F9; border: 1px solid transparent; border-radius: 14px; font-size: 0.85rem; font-family: var(--font-body); color: #1E293B; transition: all 0.3s ease;">

            <!-- Live Search Autocomplete Floating Panel -->
            <div id="admSearchResults"
                 class="position-absolute shadow-xl bg-white border border-slate-200 p-2 d-none"
                 style="top: 48px; left: 0; width: 380px; max-height: 420px; overflow-y: auto; border-radius: 16px; z-index: 2000;">
                <div id="admSearchResultsContent">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Right Controls -->
    <div class="d-flex align-items-center gap-2 gap-sm-3">

        <!-- Refresh Button with 1s Spin Animation & Telemetry Trigger -->
        <button type="button"
                id="btnRefreshDashboard"
                class="btn p-2 d-flex align-items-center justify-content-center text-slate-600 bg-white border border-slate-200 btn-press smooth-all"
                style="width: 42px; height: 42px; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
                title="Refresh Metrics (Live Telemetry)"
                aria-label="Refresh Dashboard">
            <iconify-icon icon="lucide:rotate-cw" id="refreshIcon" width="18" height="18"></iconify-icon>
        </button>

        <!-- Notification Bell with Pulse Badge & Live Feeds -->
        <div class="dropdown">
            <button type="button"
                    class="btn p-2 position-relative d-flex align-items-center justify-content-center text-slate-600 bg-white border border-slate-200 btn-press smooth-all"
                    id="notificationsDropdown"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    style="width: 42px; height: 42px; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
                    aria-label="Notifications">
                <iconify-icon icon="lucide:bell" width="18" height="18"></iconify-icon>
                <!-- Pulse Badge -->
                <span class="position-absolute" style="top: 8px; right: 8px; width: 8px; height: 8px; display: inline-flex;">
                    <span class="pulse-badge position-absolute" style="width: 100%; height: 100%; border-radius: 9999px; background: #6366F1; opacity: 0.75;"></span>
                    <span style="position: relative; width: 8px; height: 8px; border-radius: 9999px; background: #4F46E5;"></span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow-lg p-0" aria-labelledby="notificationsDropdown" style="width: 320px; border-radius: 18px; border: 1px solid #E2E8F0; overflow: hidden;">
                <div class="px-3 py-2.5 border-bottom d-flex align-items-center justify-content-between" style="background: #F8FAFC;">
                    <span class="font-clash" style="font-weight: 700; font-size: 0.88rem; color: #0F172A;">Notifikasi Toko</span>
                    <span class="badge" id="notifBadgeCount" style="background: rgba(99, 102, 241, 0.1); color: #6366F1; font-size: 0.7rem; border-radius: 9999px; padding: 4px 8px;">
                        {{ isset($notifications) ? count($notifications) : 3 }} Baru
                    </span>
                </div>
                <div class="list-group list-group-flush" id="notifListContainer" style="max-height: 280px; overflow-y: auto;">
                    @if(isset($notifications) && count($notifications) > 0)
                        @foreach($notifications as $notif)
                            <a href="{{ $notif['url'] }}" class="list-group-item list-group-item-action p-3 border-bottom text-decoration-none">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="p-1.5 rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="background: {{ $notif['icon_bg'] }}; width: 28px; height: 28px;">
                                        <iconify-icon icon="{{ $notif['icon'] }}" width="14" height="14"></iconify-icon>
                                    </span>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 font-weight-bold text-slate-800" style="font-size: 0.8rem;">{{ $notif['title'] }}</p>
                                        <span class="text-muted font-mono-code" style="font-size: 0.7rem;">{{ $notif['desc'] }} &bull; {{ $notif['time_ago'] }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <a href="{{ route('admin.transactions.index') }}" class="list-group-item list-group-item-action p-3 border-bottom text-decoration-none">
                            <div class="d-flex align-items-start gap-2">
                                <span class="p-1.5 rounded-circle text-white d-flex align-items-center justify-content-center" style="background: #10B981;">
                                    <iconify-icon icon="lucide:shopping-bag" width="14" height="14"></iconify-icon>
                                </span>
                                <div class="flex-grow-1">
                                    <p class="mb-0 font-weight-bold text-slate-800" style="font-size: 0.8rem;">Pesanan baru #DRVN-8942</p>
                                    <span class="text-muted font-mono-code" style="font-size: 0.7rem;">Rp 850.000 &bull; baru saja</span>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('admin.transactions.index') }}" class="list-group-item list-group-item-action p-3 border-bottom text-decoration-none">
                            <div class="d-flex align-items-start gap-2">
                                <span class="p-1.5 rounded-circle text-white d-flex align-items-center justify-content-center" style="background: #6366F1;">
                                    <iconify-icon icon="lucide:check-circle-2" width="14" height="14"></iconify-icon>
                                </span>
                                <div class="flex-grow-1">
                                    <p class="mb-0 font-weight-bold text-slate-800" style="font-size: 0.8rem;">Pembayaran diverifikasi</p>
                                    <span class="text-muted font-mono-code" style="font-size: 0.7rem;">Order #DRVN-8939 &bull; 15 mnt lalu</span>
                                </div>
                            </div>
                        </a>
                    @endif
                </div>
                <div class="p-2 text-center border-top" style="background: #F8FAFC;">
                    <a href="{{ route('admin.transactions.index') }}" class="text-decoration-none font-weight-bold" style="font-size: 0.75rem; color: #6366F1;">Lihat Semua Transaksi</a>
                </div>
            </div>
        </div>

        <div style="width: 1px; height: 28px; background: #E2E8F0; margin: 0 4px;"></div>

        <!-- Admin Profile Dropdown -->
        <div class="dropdown">
            <a href="#"
               class="d-flex align-items-center gap-2.5 text-decoration-none p-1 rounded-2xl smooth-all"
               id="userDropdown"
               role="button"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false"
               style="border-radius: 14px;">
                <div class="position-relative">
                    <img class="rounded-circle"
                         src="{{ asset('img/undraw_profile.svg') }}"
                         alt="{{ Auth::user()->name ?? 'Admin' }}"
                         style="width: 38px; height: 38px; object-fit: cover; border: 2px solid #818CF8; padding: 1px; background: #F1F5F9;">
                    <span class="position-absolute" style="bottom: 0; right: 0; width: 10px; height: 10px; border-radius: 9999px; background: #10B981; border: 2px solid #FFFFFF;"></span>
                </div>
                <div class="d-none d-lg-block text-left text-dark">
                    <div class="font-clash" style="font-weight: 700; font-size: 0.88rem; line-height: 1.1; color: #0F172A;">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </div>
                    <div class="font-mono-code" style="font-size: 0.65rem; color: #6366F1; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">
                        SUPER ADMIN &bull; DEV
                    </div>
                </div>
                <iconify-icon icon="lucide:chevron-down" class="text-slate-400 d-none d-lg-block" width="14" height="14"></iconify-icon>
            </a>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow-lg p-2" aria-labelledby="userDropdown" style="width: 220px; border-radius: 16px; border: 1px solid #E2E8F0;">
                <div class="px-3 py-2 border-bottom mb-1">
                    <p class="font-weight-bold text-slate-800 mb-0" style="font-size: 0.82rem;">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-muted font-mono-code mb-0" style="font-size: 0.7rem;">{{ Auth::user()->email ?? 'admin@demo.com' }}</p>
                </div>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-xl" href="{{ route('user.home') }}" style="border-radius: 10px; font-size: 0.82rem; color: #334155;">
                    <iconify-icon icon="lucide:store" class="text-indigo-500" width="16" height="16"></iconify-icon>
                    <span>Ke Halaman User</span>
                </a>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-xl" href="{{ route('admin.categories.index') }}" style="border-radius: 10px; font-size: 0.82rem; color: #334155;">
                    <iconify-icon icon="lucide:layers" class="text-emerald-500" width="16" height="16"></iconify-icon>
                    <span>Kelola Kategori</span>
                </a>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-xl" href="{{ route('admin.products.index') }}" style="border-radius: 10px; font-size: 0.82rem; color: #334155;">
                    <iconify-icon icon="lucide:package" class="text-amber-500" width="16" height="16"></iconify-icon>
                    <span>Kelola Produk</span>
                </a>
                <div class="dropdown-divider my-1"></div>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 text-danger rounded-xl" href="#" data-toggle="modal" data-target="#logoutModal" style="border-radius: 10px; font-size: 0.82rem;">
                    <iconify-icon icon="lucide:log-out" width="16" height="16"></iconify-icon>
                    <span>Logout</span>
                </a>
            </div>
        </div>

    </div>

</header>

<!-- Mobile Overlay Backdrop -->
<div class="adm-drawer-backdrop" id="adminDrawerBackdrop" aria-hidden="true"></div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header px-4 pt-4 pb-2 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: #FEE2E2; color: #EF4444;">
                        <iconify-icon icon="lucide:alert-triangle" width="20" height="20"></iconify-icon>
                    </div>
                    <h5 class="modal-title font-clash font-weight-bold text-slate-900 mb-0" id="logoutModalLabel" style="font-size: 1.1rem;">Konfirmasi Logout</h5>
                </div>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-3 text-slate-600 font-jakarta" style="font-size: 0.88rem;">
                Apakah Anda yakin ingin mengakhiri sesi admin ini? Anda dapat masuk kembali kapan saja dengan akun admin.
            </div>
            <div class="modal-footer px-4 pb-4 pt-2 border-0 d-flex gap-2 justify-content-end">
                <button class="btn btn-light px-3 py-2 font-jakarta font-weight-bold" type="button" data-dismiss="modal" style="border-radius: 12px; font-size: 0.85rem; border: 1px solid #E2E8F0;">Batal</button>
                <a class="btn px-4 py-2 font-jakarta font-weight-bold text-white btn-press"
                   href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   style="border-radius: 12px; font-size: 0.85rem; background: #EF4444; border: 1px solid #DC2626;">
                    Ya, Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
