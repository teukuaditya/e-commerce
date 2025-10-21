<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Flexora</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Nav Item - Category -->
    <li class="nav-item {{ Request::is('admin/categories') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.categories.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Category</span>
        </a>
    </li>

    <!-- Nav Item - Product -->
    <li class="nav-item {{ Request::is('admin/products') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.products.index') }}">
            <i class="fas fa-fw fa-box"></i>
            <span>Product</span>
        </a>
    </li>

    <!-- Nav Item - Transaction -->
    <li class="nav-item {{ Request::is('admin/transactions') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.transactions.index') }}">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transaction</span>
        </a>
    </li>

    <!-- Nav Item - Transaction -->
    <li class="nav-item {{ Request::is('admin/transactions-details') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.transactions-details.index') }}">
            <i class="fas fa-fw fa-list-alt"></i>
            <span>Transaction Detail</span>
        </a>
    </li>

    <!-- Nav Item - Customer -->
    <li class="nav-item {{ Request::is('admin/users') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>User</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin/carts') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.carts.index') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
