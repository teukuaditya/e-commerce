@extends('layouts.main')

@section('title', 'Daftar | DRVN')

@push('styles')
<style>
    /* Divider */
    .app-auth__divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 24px 0;
        color: #9ca3af;
        font-size: 14px;
    }

    .app-auth__divider::before,
    .app-auth__divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }

    /* Google Button */
    .app-auth__google-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 14px 20px;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        color: #374151;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .app-auth__google-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    .app-auth__google-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .app-auth__google-btn svg {
        flex-shrink: 0;
    }
</style>
@endpush

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

            {{-- Error Banner Alert --}}
            @if ($errors->any())
                <div class="app-auth__alert-error" role="alert">
                    <div class="app-auth__alert-title">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>Pendaftaran Belum Berhasil</span>
                    </div>
                    <ul class="app-auth__alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="app-auth__field">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" placeholder="Nama lengkap"
                           value="{{ old('name') }}" required class="@error('name') is-invalid @enderror">
                    @error('name')
                        <span class="app-auth__error-msg" role="alert">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="nama@email.com"
                           value="{{ old('email') }}" required class="@error('email') is-invalid @enderror">
                    @error('email')
                        <span class="app-auth__error-msg" role="alert">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="password">Password</label>
                    <div class="pass-wrap">
                        <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required class="@error('password') is-invalid @enderror">
                        <button type="button" class="pass-toggle" data-target="password"
                                aria-label="Tampilkan password" title="Tampilkan password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="app-auth__error-msg" role="alert">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="password-confirm">Ulangi Password</label>
                    <div class="pass-wrap">
                        <input type="password" name="password_confirmation" id="password-confirm" placeholder="Ketik ulang password" required class="@error('password_confirmation') is-invalid @enderror">
                        <button type="button" class="pass-toggle" data-target="password-confirm"
                                aria-label="Tampilkan password" title="Tampilkan password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="app-auth__error-msg" role="alert">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <button type="submit" class="app-auth__submit" style="margin-top: 6px;">Buat Akun</button>
            </form>

            {{-- Google Login Button --}}
            <div class="app-auth__divider">
                <span>atau daftar dengan</span>
            </div>
            <a href="{{ route('google.login') }}" class="app-auth__google-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Lanjutkan dengan Google</span>
            </a>

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

{{-- Toggle show/hide password (delegasi) — JS bersama di public/js/auth-forms.js --}}
@push('scripts')
    <script src="{{ asset('js/auth-forms.js') }}?v={{ filemtime(public_path('js/auth-forms.js')) }}"></script>
@endpush
@endsection
