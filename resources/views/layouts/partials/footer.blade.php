{{-- Footer modern — markup bersih & tetap memakai data dinamis --}}
<footer class="site-footer">
    <div class="container">
        <div class="row site-footer__main">

            {{-- Brand --}}
            <div class="col-lg-4 col-md-6 site-footer__col">
                <div class="site-footer__logo flex-v flex-l-m">
                    <img src="{{ !empty($company['logo']) ? asset('storage/'.$company['logo']) : asset('images/drvn-logo.png') }}" alt="{{ $company['name'] ?? 'DRVN' }}">
                    <span>{{ strtoupper($company['name'] ?? 'DRVN') }} STORE</span>
                </div>
                <p class="site-footer__desc">
                    {{ $company['footer_text'] ?? 'Pakaian esensial yang bertahan. Produksi lokal, bahan pilihan, harga jujur — semuanya untuk lemari harian Anda.' }}
                </p>
                <div class="site-footer__social">
                    @if (!empty($company["email"]))
                        <a href="mailto:{{ $company['email'] }}" aria-label="Email"><i class="fas fa-envelope" aria-hidden="true"></i></a>
                    @endif
                    @if (!empty($company["whatsapp"]))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $company['whatsapp']) }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                    @endif
                    @if (!empty($company["instagram"]))
                        <a href="{{ $company['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                    @endif
                    @if (!empty($company["facebook"]))
                        <a href="{{ $company['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                    @endif
                    @if (!empty($company["tiktok"]))
                        <a href="{{ $company['tiktok'] }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fab fa-tiktok" aria-hidden="true"></i></a>
                    @endif
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
                            <form action="{{ route('logout') }}" method="POST" id="logout-form-footer">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); if(window.confirmLogout){window.confirmLogout('logout-form-footer');}else if(confirm('Apakah kamu yakin ingin keluar dari akun ini?')){document.getElementById('logout-form-footer').submit();}">Keluar</a>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Bantuan / kontak --}}
            <div class="col-lg-3 col-md-6 site-footer__col">
                <h4 class="site-footer__heading">Bantuan</h4>
                <ul class="site-footer__links">
                    @if (!empty($company["email"]))
                        <li><a href="mailto:{{ $company['email'] }}"><i class="fas fa-envelope m-r-8"></i> {{ $company['email'] }}</a></li>
                    @endif
                    @if (!empty($company["phone"]))
                        <li><span class="site-footer__muted--with-icon"><i class="fas fa-phone m-r-8"></i> {{ $company['phone'] }}</span></li>
                    @endif
                    @if (!empty($company["whatsapp"]))
                        <li><a href="https://wa.me/{{ preg_replace('/\D/', '', $company['whatsapp']) }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp m-r-8"></i> {{ $company['whatsapp'] }}</a></li>
                    @endif
                    @if (!empty($company["address"]))
                        <li class="site-footer__muted--with-icon"><i class="fas fa-map-marker-alt m-r-8"></i> {{ $company['address'] }}</li>
                    @endif
                    @if (!empty($company["operating_hours"]))
                        <li class="site-footer__muted--with-icon"><i class="fas fa-clock m-r-8"></i> {{ $company['operating_hours'] }}</li>
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
