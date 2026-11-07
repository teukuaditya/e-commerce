<!-- Modern Developer Admin Sidebar -->
<aside class="adm-modern-sidebar" id="adminSidebar" aria-label="Sidebar Navigation">

    <!-- Brand Header -->
    <a href="{{ route('admin.dashboard.index') }}" class="d-flex align-items-center gap-3 px-4 py-4 text-decoration-none border-bottom border-white/5" style="border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
        <div class="d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 14px;">
            <img src="{{ asset('images/drvn-logo.png') }}" alt="DRVN Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div>
            <div class="font-clash text-white" style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.02em; line-height: 1.2;">
                DRVN<span style="color: #818CF8;">.</span>STORE
            </div>
            <div class="d-flex align-items-center gap-1 font-mono-code" style="font-size: 0.65rem; color: #94A3B8; letter-spacing: 0.1em; font-weight: 600; text-transform: uppercase;">
                <span class="d-inline-block rounded-circle" style="width: 6px; height: 6px; background-color: #10B981; box-shadow: 0 0 8px #10B981;"></span>
                <span>v2.4 &bull; LIVE</span>
            </div>
        </div>
    </a>

    <!-- Navigation List -->
    <nav class="d-flex flex-column py-3 flex-grow-1" style="overflow-y: auto;">

        <!-- Section: Menu Utama -->
        <div class="adm-nav-heading">MENU UTAMA</div>

        <a href="{{ route('admin.dashboard.index') }}" class="adm-nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <iconify-icon icon="lucide:layout-dashboard"></iconify-icon>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="adm-nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
            <iconify-icon icon="lucide:layers"></iconify-icon>
            <span>Category</span>
        </a>

        <a href="{{ route('admin.products.index') }}" class="adm-nav-link {{ Request::is('admin/products*') ? 'active' : '' }}">
            <iconify-icon icon="lucide:package"></iconify-icon>
            <span>Product</span>
        </a>

        <!-- Section: Transaksi -->
        <div class="adm-nav-heading mt-2">TRANSAKSI</div>

        <a href="{{ route('admin.transactions.index') }}" class="adm-nav-link {{ Request::is('admin/transactions') || Request::is('admin/transactions/*') ? 'active' : '' }}">
            <iconify-icon icon="lucide:receipt"></iconify-icon>
            <span>Transaction</span>
        </a>

        <a href="{{ route('admin.transactions-details.index') }}" class="adm-nav-link {{ Request::is('admin/transactions-details*') ? 'active' : '' }}">
            <iconify-icon icon="lucide:file-text"></iconify-icon>
            <span>Transaction Detail</span>
        </a>

        <!-- Section: Pengguna -->
        <div class="adm-nav-heading mt-2">PENGGUNA</div>

        <a href="{{ route('admin.users.index') }}" class="adm-nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
            <iconify-icon icon="lucide:users"></iconify-icon>
            <span>User</span>
        </a>

        <a href="{{ route('admin.carts.index') }}" class="adm-nav-link {{ Request::is('admin/carts*') ? 'active' : '' }}">
            <iconify-icon icon="lucide:shopping-cart"></iconify-icon>
            <span>Cart</span>
        </a>

    </nav>

    <!-- Bottom Developer Environment Widget -->
    <div class="p-3 mx-3 mb-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 1rem;">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="font-mono-code" style="font-size: 0.65rem; color: #64748B; font-weight: 600;">SERVER STATUS</span>
            <span class="d-flex align-items-center gap-1 font-mono-code" style="font-size: 0.65rem; color: #10B981; font-weight: 600;">
                <iconify-icon icon="lucide:activity" width="12" height="12"></iconify-icon>
                <span>99.9%</span>
            </span>
        </div>
        <div class="font-mono-code" style="font-size: 0.7rem; color: #CBD5E1;">
            PHP 8.2 &bull; Latency 12ms
        </div>
        <a href="{{ route('user.home') }}" class="d-flex align-items-center justify-content-center gap-1 text-decoration-none mt-2 py-1 px-2 rounded-2" style="font-size: 0.72rem; color: #818CF8; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); transition: all 0.2s ease;">
            <iconify-icon icon="lucide:external-link" width="13" height="13"></iconify-icon>
            <span>Lihat Storefront</span>
        </a>
    </div>

</aside>
