@extends('layouts.main')

@section('title', 'Password Baru | DRVN')

@section('content')

<div class="app-auth">
    <div class="app-auth__card">

        <div class="app-auth__brand">
            <a href="{{ route('user.home') }}" class="app-auth__logo">
                <img src="{{ asset('images/drvn-logo.png') }}" alt="DRVN">
                <span>DRVN STORE</span>
            </a>

            <div class="app-auth__pitch">
                <h1>Password <span>baru</span></h1>
                <p>Tentukan password baru untuk akun Anda. Gunakan kombinasi yang kuat dan mudah Anda ingat.</p>

                <ul class="app-auth__points">
                    <li><i class="fa fa-check"></i> Minimal 8 karakter</li>
                    <li><i class="fa fa-check"></i> Campur huruf, angka &amp; simbol lebih aman</li>
                    <li><i class="fa fa-check"></i> Jangan gunakan data pribadi mudah ditebak</li>
                </ul>
            </div>

            <div class="app-auth__foot">© {{ date('Y') }} Drive Venture</div>
        </div>

        <div class="app-auth__form">
            <div class="app-auth__head">
                <h2>Buat password baru</h2>
                <p>Selesaikan reset password di sini.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="app-auth__field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}"
                           required @error('email') class="is-invalid" @enderror>
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="password">Password Baru</label>
                    <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                           required @error('password') class="is-invalid" @enderror>
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="app-auth__field">
                    <label for="password-confirm">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password-confirm" required>
                </div>

                <button type="submit" class="app-auth__submit">Simpan Password Baru</button>
            </form>

            <div class="app-auth__alt">
                Sudah ingat password lama?
                <a href="{{ route('login') }}">Login di sini</a>
            </div>
        </div>

    </div>
</div>
@endsection
