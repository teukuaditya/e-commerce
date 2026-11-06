<!-- Header -->
<header class="header-v4">
    <!-- Header desktop -->
    <div class="container-menu-desktop">

        <div class="wrap-menu-desktop how-shadow1">
            <nav class="limiter-menu-desktop container">

                <!-- Logo desktop -->
                <a href="{{ route('user.home') }}" class="logo">
                    <span style="font-size: 25px; font-weight: bold; color: #000; letter-spacing: 2px;">DRVN</span>
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

                        <li class="{{ request()->routeIs('user.about.*') ? 'active-menu' : '' }}">
                            <a href="{{ route('user.about.index') }}">About</a>
                        </li>


                    </ul>
                </div>

                <!-- Icon header -->
                <div class="wrap-icon-header flex-w flex-r-m">
                    <a href="{{ route('user.cart.index') }}">
                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                            data-notify="{{ $cartCount }}">
                            <i
                                class="zmdi zmdi-shopping-cart {{ Route::currentRouteName() == 'user.cart.index' ? 'active-icon' : '' }}"></i>
                        </div>
                    </a>

                    {{-- <a href="#"
                        class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                        data-notify="0">
                        <i class="zmdi zmdi-favorite-outline"></i>
                    </a> --}}

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
                        class="flex-c-m stext-110 size-127 tu-btn-outline ml-2 bor14 p-lr-15 trans-04 pointer"
                        style="font-size: 14px; min-width: 10%; height: 30px">
                        Register
                    </a>
                    @else
                    <!-- Dropdown User Info (mandiri — tidak bergantung data-toggle Bootstrap/modal) -->
                    <div class="tu-dd">
                        <a class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11" href="#" id="userDropdown"
                            role="button" data-tu-dropdown aria-haspopup="true" aria-expanded="false">
                            <i class="zmdi zmdi-account"></i>
                        </a>
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu dropdown-menu-right" id="userDropdownMenu"
                            aria-labelledby="userDropdown">
                            <h6 class="dropdown-header stext-110 cl2" style="font-weight: bold; font-size: 14px">
                                {{ Auth::user()->name }}<br>
                                <small class="stext-104 cl4" style="font-size: 12px">{{ Auth::user()->email }}</small>
                            </h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item stext-104" href="{{ route('user.profile.edit') }}">
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
                            @if(Auth::user()->role == 'admin')
                            <a class="dropdown-item stext-104" href="{{ route('admin.products.index') }}">
                                <i class="fas fa-user-cog fa-fw m-r-10 mt-2"></i>
                                Admin
                            </a>
                            @endif

                            <div class="dropdown-divider"></div>
                            <!-- Sign Out: submit POST logout-form langsung (punya @csrf) -->
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt fa-fw mr-2 text-gray-400"></i>
                                Sign Out
                            </a>
                        </div>
                    </div>
                    @endguest
                    @auth
                        @php
                            $fullname = Auth::user()->name;
                            $firstName = explode(' ',($fullname))[0];
                        @endphp
                        <span class="nav-greeting ml-2">
                            Hi {{ $firstName }},
                        </span>
                    @endauth
                </div> <!-- /wrap-icon-header -->

            </nav> <!-- /limiter-menu-desktop -->
        </div> <!-- /wrap-menu-desktop -->
    </div> <!-- /container-menu-desktop -->

    <!-- Form logout POST (punya @csrf) — dipakai oleh item "Sign Out" di dropdown -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="{{ asset('js/navbar.js') }}?v={{ filemtime(public_path('js/navbar.js')) }}"></script>
</header>