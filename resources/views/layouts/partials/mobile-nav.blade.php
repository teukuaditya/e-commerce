<!-- Header -->
<header>
    <!-- Header Mobile -->
    <div class="wrap-header-mobile">
        @guest
            <!-- Logo moblie -->
            <a href="{{ route('user.home') }}" class="logo-mobile">
                <span style="font-size: 20px; font-weight: bold; color: #000; letter-spacing: 2px;">DRVN</span>
            </a>
            <!-- Jika user belum login -->
            <a href="{{ route('login') }}"
                class="flex-c-m stext-110 cl0 size-127 bg1 ml-2 mr-2 bor14 hov-btn5 p-lr-15 trans-04 pointer"
                style="font-size: 14px">
                Sign In
            </a>
            {{-- <a href="{{ route('register') }}"
                class="flex-c-m stext-110 cl0 size-127 bg3 ml-2 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                style="font-size: 14px">
                Register
            </a> --}}
        @else
            <a href="{{ route('user.home') }}" class="logo-mobile">
                <span style="font-size: 20px; font-weight: bold; color: #000; letter-spacing: 2px;">DRVN</span>
            </a>
            <!-- Icon header -->
            <div class="wrap-icon-header flex-w flex-r-m">
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
                    <i class="zmdi zmdi-search"></i>
                </div>

                <a href="{{ route('user.cart.index') }}">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-11 p-r-11 icon-header-noti js-show-cart"
                        data-notify="{{ $cartCount }}">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>
                </a>

                <a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti"
                    data-notify="0">
                    <i class="zmdi zmdi-favorite-outline"></i>
                </a>
                <!-- Dropdown User Info -->
                <div class="">
                    <a class="icon-header-item cl2 hov-cl1 trans-04 p-l-11 p-r-11" href="#" id="userDropdown"
                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="zmdi zmdi-account"></i>
                    </a>
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <h6 class="dropdown-header stext-110 cl2" style="font-weight: bold; font-size: 14px">
                            {{ Auth::user()->name }}<br>
                            <small class="stext-104 cl4" style="font-size: 12px">{{ Auth::user()->email }}</small>
                        </h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item stext-104" href="{{ route('user.profile.edit') }}">
                            <i class="fas fa-user fa-fw mr-2 mb-2"></i>
                            My Profile
                        </a>
                        <a class="dropdown-item stext-104" href="{{ route('user.address.edit') }}">
                            <i class="fas fa-map-marker-alt fa-fw m-r-10 mb-2"></i>
                            My Address
                        </a>
                        <a class="dropdown-item stext-104" href="{{ route('user.order.index') }}">
                            <i class="fas fa-shopping-bag fa-fw mr-2"></i>
                            Order History
                        </a>
                        <!-- Menambahkan link Admin jika pengguna adalah admin -->
                        @if (Auth::user()->role == 'admin')
                            <a class="dropdown-item stext-104" href="{{ route('admin.products.index') }}">
                                <i class="fas fa-user-cog fa-fw m-r-10 mt-2"></i>
                                Admin
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
       document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt fa-fw mr-2 text-gray-400"></i>
                            Sign Out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            @endguest
            <!-- Button show menu -->
            <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </div>
        </div>
    </div>


    <!-- Menu Mobile -->
    <div class="menu-mobile">

        <ul class="main-menu-m">
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

            <li class="{{ Route::currentRouteName() == 'blog' ? 'active-menu' : '' }}">
                <a href="">Blog</a>
            </li>

            <li class="{{ Route::currentRouteName() == 'about' ? 'active-menu' : '' }}">
                <a href="">About</a>
            </li>

            <li class="{{ Route::currentRouteName() == 'contact' ? 'active-menu' : '' }}">
                <a href="">Contact</a>
            </li>
        </ul>
    </div>
</header>
