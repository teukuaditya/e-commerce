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

                        <li class="{{ Route::currentRouteName() == 'blog' ? 'active-menu' : '' }}">
                            <a href="">Blog</a>
                        </li>

                        <li class="{{ Route::currentRouteName() == 'user.about.index' ? 'active-menu' : '' }}">
                            <a href="{{ route('user.about.index') }}">About</a>
                        </li>

                        <li class="{{ Route::currentRouteName() == 'contact' ? 'active-menu' : '' }}">
                            <a href="">Contact</a>
                        </li>
                    </ul>
                </div>

                <!-- Icon header -->
                <div class="wrap-icon-header flex-w flex-r-m">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                        <i class="zmdi zmdi-search"></i>
                    </div>

                    <a href="{{ route('user.cart.index') }}">
                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                            data-notify="{{ $cartCount }}">
                            <i
                                class="zmdi zmdi-shopping-cart {{ Route::currentRouteName() == 'user.cart.index' ? 'active-icon' : '' }}"></i>
                        </div>
                    </a>

                    <a href="#"
                        class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                        data-notify="0">
                    <i class="zmdi zmdi-favorite-outline"></i>
                    </a>

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
                            class="flex-c-m stext-110 cl0 size-127 bg3 ml-2 bor14 hov-btn4 p-lr-15 trans-04 pointer"
                            style="font-size: 14px; min-width: 10%; height: 30px">
                            Register
                        </a>
                    @else
                        <!-- Dropdown User Info -->
                        <div class="">
                            <a class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11" href="#" id="userDropdown"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="zmdi zmdi-account"></i>
                            </a>
                            <!-- Dropdown Menu -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <h6 class="dropdown-header stext-110 cl2" style="font-weight: bold; font-size: 14px">
                                    {{ Auth::user()->name }}<br>
                                    <small class="stext-104 cl4" style="font-size: 12px">{{ Auth::user()->email }}</small>
                                </h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item stext-104" href="{{ route('user.edit') }}">
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
                                <a class="dropdown-item" href="{{ route('logout') }}" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-fw mr-2 text-gray-400"></i>
                                    Sign Out
                                </a>
                                {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form> --}}
                            </div>
                        </div>
                    @endguest
                </div>

            </div>
            <!-- Logout Modal-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title mtext-101 cl2" id="exampleModalLabel">Ready to Leave?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body stext-102">Select "Logout" below if you are ready to end your current session.
                        </div>
                        <div class="modal-footer">
                            <button class="flex-c-m stext-102 cl0 size-304 bg10 bor14 hov-btn4 p-lr-15 trans-04 pointer" type="button" data-dismiss="modal">Cancel</button>
                            <a class="flex-c-m stext-102 cl0 size-304 bg1 bor14 hov-btn5 p-lr-15 trans-04 pointer" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <style>

        .dropdown-item:active {
            background-color: #717fe0; /* Warna yang diinginkan */
            color: #fff; /* Warna teks jika perlu */
        }
        /* CSS untuk Modal */
        .modal-backdrop {
            z-index: 1200 !important;
            /* Pastikan backdrop lebih tinggi dari top bar */
        }

        .modal {
            z-index: 1300 !important;
            /* Modal harus berada di atas backdrop */
        }

        /* Memberikan margin atas pada modal */
        .modal-dialog {
            margin-top: 80px;
            /* Atur sesuai dengan kebutuhan, semakin besar maka semakin ke bawah */
        }
    </style>

    <!-- Modal Search -->
    <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
        <div class="container-search-header">
            <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                <img src={{ asset('images/icons/icon-close2.png') }} alt="CLOSE">
            </button>

            <form class="wrap-search-header flex-w p-l-15">
                <button class="flex-c-m trans-04">
                    <i class="zmdi zmdi-search"></i>
                </button>
                <input class="plh3" type="text" name="search" placeholder="Search...">
            </form>
        </div>
    </div>
</header>
