<!-- Sidebar -->
<ul class="navbar-nav sidebar accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard.index') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="sidebar-brand-text">DRVN <span style="color: var(--adm-accent)">.</span></div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Section label -->
    <div class="sidebar-heading" style="font-size:10.5px;font-weight:700;letter-spacing:.12em;color:var(--adm-ink-3);padding:14px 24px 6px;">MENU UTAMA</div>

    <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard.index') }}" data-label="Dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin/categories*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.categories.index') }}" data-label="Category">
            <i class="fas fa-fw fa-tags"></i>
            <span>Category</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin/products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.products.index') }}" data-label="Product">
            <i class="fas fa-fw fa-box"></i>
            <span>Product</span>
        </a>
    </li>

    <div class="sidebar-heading" style="font-size:10.5px;font-weight:700;letter-spacing:.12em;color:var(--adm-ink-3);padding:14px 24px 6px;">TRANSAKSI</div>

    <li class="nav-item {{ Request::is('admin/transactions') || Request::is('admin/transactions/*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.transactions.index') }}" data-label="Transaction">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transaction</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin/transactions-details*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.transactions-details.index') }}" data-label="Transaction Detail">
            <i class="fas fa-fw fa-list-alt"></i>
            <span>Transaction Detail</span>
        </a>
    </li>

    <div class="sidebar-heading" style="font-size:10.5px;font-weight:700;letter-spacing:.12em;color:var(--adm-ink-3);padding:14px 24px 6px;">PENGGUNA</div>

    <li class="nav-item {{ Request::is('admin/users') || Request::is('admin/users/*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}" data-label="User">
            <i class="fas fa-fw fa-users"></i>
            <span>User</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin/carts') || Request::is('admin/carts/*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.carts.index') }}" data-label="Cart">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Perkecil atau perbesar sidebar"></button>
    </div>

</ul>
<!-- End of Sidebar -->
