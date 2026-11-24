@extends('layouts.main')

@section('title', 'Login | DRVN')

@push('styles')
<style>
    /* Mencegah pergeseran halaman horizontal */
    html,
    body {
        max-width: 100%;
        overflow-x: hidden !important;
        touch-action: pan-y;
    }

    body {
        background:
            radial-gradient(100% 80% at 50% 0%, rgba(255, 255, 255, 0.95) 0%, rgba(238, 242, 255, 0.5) 45%, rgba(245, 243, 255, 0.95) 100%),
            linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%) !important;
        min-height: 100vh;
    }

    /* Ambient Background Layer: strictly clipped, zero scroll leakage */
    .auth-ambient {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        overflow: hidden !important;
        pointer-events: none;
        z-index: -1;
    }

    /* Subtle micro-dot matrix texture */
    .auth-ambient__grid {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(99, 102, 241, 0.10) 1.2px, transparent 1.2px);
        background-size: 26px 26px;
        mask-image: radial-gradient(ellipse 75% 65% at 50% 50%, #000 40%, transparent 100%);
        -webkit-mask-image: radial-gradient(ellipse 75% 65% at 50% 50%, #000 40%, transparent 100%);
        opacity: 0.65;
        pointer-events: none;
    }

    /* Base Blob Properties */
    .auth-ambient__blob {
        position: absolute;
        border-radius: 50%;
        opacity: 0.35;
        filter: blur(80px);
        -webkit-filter: blur(80px);
        pointer-events: none;
        will-change: transform;
    }

    /* Blob 1: Pojok kiri atas - Indigo #818CF8 */
    .auth-ambient__blob--1 {
        top: -80px;
        left: -60px;
        width: clamp(320px, 42vw, 560px);
        height: clamp(320px, 42vw, 560px);
        background: radial-gradient(circle at 40% 40%, #818CF8 0%, rgba(129, 140, 248, 0.75) 45%, rgba(129, 140, 248, 0.2) 75%, transparent 100%);
        animation: authBlobFloat1 18s ease-in-out infinite alternate;
    }

    /* Blob 2: Kanan bawah - Ungu #A78BFA */
    .auth-ambient__blob--2 {
        bottom: -100px;
        right: -80px;
        width: clamp(340px, 46vw, 620px);
        height: clamp(340px, 46vw, 620px);
        background: radial-gradient(circle at 60% 60%, #A78BFA 0%, rgba(167, 139, 250, 0.75) 45%, rgba(167, 139, 250, 0.2) 75%, transparent 100%);
        animation: authBlobFloat2 20s ease-in-out infinite alternate;
    }

    /* Blob 3: Tengah-kanan - Indigo / Ungu (#818CF8 & #A78BFA) */
    .auth-ambient__blob--3 {
        top: 48%;
        right: -40px;
        transform: translateY(-50%);
        width: clamp(260px, 32vw, 440px);
        height: clamp(260px, 32vw, 440px);
        background: radial-gradient(circle at 50% 50%, #818CF8 0%, rgba(167, 139, 250, 0.7) 50%, rgba(129, 140, 248, 0.15) 80%, transparent 100%);
        animation: authBlobFloat3 16s ease-in-out infinite alternate;
    }

    /* Floating Animations */
    @keyframes authBlobFloat1 {
        0%   { transform: translate(0, 0) scale(1); }
        50%  { transform: translate(24px, 18px) scale(1.06); }
        100% { transform: translate(-16px, 12px) scale(0.96); }
    }

    @keyframes authBlobFloat2 {
        0%   { transform: translate(0, 0) scale(1); }
        50%  { transform: translate(-22px, -20px) scale(1.08); }
        100% { transform: translate(14px, -10px) scale(0.95); }
    }

    @keyframes authBlobFloat3 {
        0%   { transform: translateY(-50%) translate(0, 0) scale(1); }
        50%  { transform: translateY(-50%) translate(-18px, 14px) scale(1.05); }
        100% { transform: translateY(-50%) translate(10px, -14px) scale(0.97); }
    }

    @media (prefers-reduced-motion: reduce) {
        .auth-ambient__blob {
            animation: none !important;
        }
    }

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

    /* Centered layout for auth container */
    .app-auth {
        margin: 32px auto 80px;
        min-height: calc(100vh - 220px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 16px;
        position: relative;
        z-index: 1;
    }

    /* Elevated, crisp card styling */
    .app-auth__card {
        width: 100%;
        max-width: 980px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        background: #ffffff;
        border-radius: 24px;
        box-shadow:
            0 24px 64px -12px rgba(99, 102, 241, 0.18),
            0 12px 32px -8px rgba(23, 23, 29, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.9);
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .app-auth {
            margin: 20px auto 60px;
            min-height: auto;
            padding: 0 14px;
        }

        .app-auth__card {
            grid-template-columns: 1fr;
            border-radius: 20px;
            box-shadow:
                0 16px 40px -10px rgba(99, 102, 241, 0.16),
                0 8px 20px -6px rgba(23, 23, 29, 0.06);
        }
    }

    @media (max-width: 768px) {
        .auth-ambient__blob--1 {
            width: 240px;
            height: 240px;
            top: -40px;
            left: -40px;
            filter: blur(55px);
            -webkit-filter: blur(55px);
        }

        .auth-ambient__blob--2 {
            width: 260px;
            height: 260px;
            bottom: -50px;
            right: -50px;
            filter: blur(55px);
            -webkit-filter: blur(55px);
        }

        .auth-ambient__blob--3 {
            width: 190px;
            height: 190px;
            right: -25px;
            filter: blur(55px);
            -webkit-filter: blur(55px);
        }
    }
</style>
@endpush

@section('content')

{{-- Ambient background glow & blobs (fixed, clipped, zero horizontal overflow) --}}
<div class="auth-ambient" aria-hidden="true">
    <div class="auth-ambient__grid"></div>
    <div class="auth-ambient__blob auth-ambient__blob--1"></div>
    <div class="auth-ambient__blob auth-ambient__blob--2"></div>
    <div class="auth-ambient__blob auth-ambient__blob--3"></div>
</div>

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

            {{-- Error Banner Alert --}}
            @if ($errors->any())
                <div class="app-auth__alert-error" role="alert">
                    <div class="app-auth__alert-title">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>Email atau Password Tidak Sesuai</span>
                    </div>
                    <ul class="app-auth__alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="app-auth__alert-error" role="alert">
                    <div class="app-auth__alert-title">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

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
                        <input type="password" name="password" id="password" placeholder="••••••••" required class="@error('password') is-invalid @enderror">
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

            {{-- Google Login Button --}}
            <div class="app-auth__divider">
                <span>atau login dengan</span>
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

{{-- Toggle show/hide password (delegasi) — JS bersama di public/js/auth-forms.js --}}
@push('scripts')
    <script src="{{ asset('js/auth-forms.js') }}?v={{ filemtime(public_path('js/auth-forms.js')) }}"></script>
@endpush
@endsection
