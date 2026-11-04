@extends('layouts.main')

@section('title', 'Login | DRVN')

@section('content')

<div class="app-auth">
    <div class="app-auth__card">

        {{-- Panel brand (kiri) --}}
        <div class="app-auth__brand">
            <a href="{{ route('user.home') }}" class="app-auth__logo">
                <img src="{{ asset('images/drvn-logo.png') }}" alt="DRVN">
                <span>DRVN STORE</span>
            </a>

            <div class="app-auth__pitch">
                <h1>Selamat datang<br><span>kembali</span>.</h1>
                <p>Login untuk melanjutkan belanja, mengatur keranjang, dan memantau pesanan Anda.</p>

                <ul class="app-auth__points">
                    <li><i class="fa fa-check"></i> Checkout cepat &amp; aman via Midtrans</li>
                    <li><i class="fa fa-check"></i> Lacak riwayat pesanan kapan saja</li>
                    <li><i class="fa fa-check"></i> Simpan alamat &amp; kurir favorit</li>
                </ul>
            </div>

            <div class="app-auth__foot">© {{ date('Y') }} Drive Venture — all about essentials</div>
        </div>

        {{-- Panel form (kanan) --}}
        <div class="app-auth__form">
            <div class="app-auth__head">
                <h2>Masuk ke akun Anda</h2>
                <p>Silakan isi detail akun untuk melanjutkan.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="app-auth__field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="nama@email.com"
                           value="{{ old('email') }}" required @error('email') class="is-invalid" @enderror>
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="password">Password</label>
                    <div class="pass-wrap">
                        <input type="password" name="password" id="password" placeholder="••••••••" required>
                        <button type="button" class="pass-toggle" data-target="password"
                                aria-label="Tampilkan password" title="Tampilkan password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="app-auth__row">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="app-auth__submit">Login</button>
            </form>

            @if (Route::has('register'))
                <div class="app-auth__alt">
                    Belum punya akun?
                    <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            @endif

            <a href="{{ route('user.home') }}" class="app-auth__back" style="margin-top: 26px;">
                <i class="fa fa-arrow-left"></i> Kembali ke beranda
            </a>
        </div>

    </div>
</div>

{{-- Toggle show/hide password (delegasi, 1 listener untuk semua field) --}}
<script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.pass-toggle');
        if (!btn) return;
        var input = document.getElementById(btn.getAttribute('data-target'));
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.innerHTML = '<i class="fa ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
        btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        input.focus({ preventScroll: true });
    });
</script>
@endsection
