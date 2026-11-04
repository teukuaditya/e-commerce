@extends('layouts.main')

@section('title', 'Daftar | DRVN')

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
                <h1>Buat akun<br><span>dalam semenit</span>.</h1>
                <p>Satu akun untuk semua kebutuhan belanja: keranjang, alamat, ongkir, dan pembayaran.</p>

                <ul class="app-auth__points">
                    <li><i class="fa fa-check"></i> Gratis, tanpa syarat rumit</li>
                    <li><i class="fa fa-check"></i> Data hanya untuk keperluan pesanan</li>
                    <li><i class="fa fa-check"></i> Akses ke promo &amp; produk baru</li>
                </ul>
            </div>

            <div class="app-auth__foot">© {{ date('Y') }} Drive Venture — all about essentials</div>
        </div>

        {{-- Panel form (kanan) --}}
        <div class="app-auth__form">
            <div class="app-auth__head">
                <h2>Daftar akun baru</h2>
                <p>Lengkapi data berikut — semuanya cepat.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="app-auth__field">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" placeholder="Nama lengkap"
                           value="{{ old('name') }}" required @error('name') class="is-invalid" @enderror>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

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
                        <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required>
                        <button type="button" class="pass-toggle" data-target="password"
                                aria-label="Tampilkan password" title="Tampilkan password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="password-confirm">Ulangi Password</label>
                    <div class="pass-wrap">
                        <input type="password" name="password_confirmation" id="password-confirm" required>
                        <button type="button" class="pass-toggle" data-target="password-confirm"
                                aria-label="Tampilkan password" title="Tampilkan password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="app-auth__submit" style="margin-top: 6px;">Buat Akun</button>
            </form>

            <div class="app-auth__alt">
                Sudah punya akun?
                <a href="{{ route('login') }}">Login di sini</a>
            </div>

            <a href="{{ route('user.home') }}" class="app-auth__back" style="margin-top: 22px;">
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
