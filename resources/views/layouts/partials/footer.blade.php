{{-- Footer modern — markup bersih & tetap memakai data dinamis --}}
<footer class="site-footer">
    <div class="container">
        <div class="row site-footer__main">

            {{-- Brand --}}
            <div class="col-lg-4 col-md-6 site-footer__col">
                <div class="site-footer__logo flex-v flex-l-m">
                    <img src="{{ asset('images/drvn-logo.png') }}" alt="DRVN">
                    <span>DRVN STORE</span>
                </div>
                <p class="site-footer__desc">
                    Pakaian esensial yang bertahan. Produksi lokal, bahan pilihan,
                    harga jujur — semuanya untuk lemari harian Anda.
                </p>
                <div class="site-footer__social">
                    @if ($company["email"] ?? null)
                        <a href="mailto:{{ $company['email'] }}" aria-label="Email">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                        </a>
                    @endif
                    <a href="{{ route('user.about.index') }}" aria-label="Tentang kami">
                        <i class="fas fa-info" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('user.products.index') }}" aria-label="Produk">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            {{-- Kategori (dinamis) --}}
            <div class="col-lg-2 col-md-3 col-6 site-footer__col">
                <h4 class="site-footer__heading">Kategori</h4>
                <ul class="site-footer__links">
                    @forelse ($categories as $category)
                        <li><a href="{{ route('user.categories.products', $category->id) }}">
                            {{ $category->category_name }}
                        </a></li>
                    @empty
                        <li><span class="site-footer__muted">Belum ada kategori</span></li>
                    @endforelse
                </ul>
            </div>

            {{-- Jelajah --}}
            <div class="col-lg-3 col-md-3 col-6 site-footer__col">
                <h4 class="site-footer__heading">Jelajah</h4>
                <ul class="site-footer__links">
                    <li><a href="{{ route('user.home') }}">Beranda</a></li>
                    <li><a href="{{ route('user.products.index') }}">Semua Produk</a></li>
                    <li><a href="{{ route('user.about.index') }}">Tentang Kami</a></li>
                    @if (Route::has('user.cart.index'))
                        <li><a href="{{ route('user.cart.index') }}">Keranjang</a></li>
                    @endif
                    @if (Route::has('user.order.index') && auth()->check())
                        <li><a href="{{ route('user.order.index') }}">Pesanan Saya</a></li>
                    @endif
                    @if (!auth()->check() && Route::has('login'))
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    @endif
                    @if (auth()->check())
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</a>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Bantuan / kontak --}}
            <div class="col-lg-3 col-md-6 site-footer__col">
                <h4 class="site-footer__heading">Bantuan</h4>
                <ul class="site-footer__links">
                    @if ($company["email"] ?? null)
                        <li>
                            <a href="mailto:{{ $company['email'] }}">
                                <i class="fas fa-envelope m-r-8"></i> {{ $company['email'] }}
                            </a>
                        </li>
                    @endif
                    @if ($company["address"] ?? null)
                        <li class="site-footer__muted--with-icon">
                            <i class="fas fa-map-marker-alt m-r-8"></i> {{ $company['address'] }}
                        </li>
                    @endif
                    <li><a href="{{ route('user.about.index') }}">Tentang &amp; Kontak</a></li>
                </ul>
            </div>

        </div>

        {{-- Pembayaran + copyright --}}
        <div class="site-footer__bottom">
            <div class="site-footer__pays flex-w">
                <span class="m-all-1"><img src="{{ asset('images/icons/icon-pay-01.png') }}" alt="Visa"></span>
                <span class="m-all-1"><img src="{{ asset('images/icons/icon-pay-02.png') }}" alt="PayPal"></span>
                <span class="m-all-1"><img src="{{ asset('images/icons/icon-pay-03.png') }}" alt="PayPal"></span>
                <span class="m-all-1"><img src="{{ asset('images/icons/icon-pay-04.png') }}" alt="Card"></span>
                <span class="m-all-1"><img src="{{ asset('images/icons/icon-pay-05.png') }}" alt="Card"></span>
            </div>
            <p class="site-footer__copy">
                © {{ date('Y') }} {{ ($company['name'] ?? 'Drive Venture') }} — All rights reserved.
            </p>
        </div>
    </div>
</footer>
