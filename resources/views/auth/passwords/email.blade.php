@extends('layouts.main')

@section('title', 'Reset Password | DRVN')

@section('content')

<div class="app-auth">
    <div class="app-auth__card">

        <div class="app-auth__brand">
            <a href="{{ route('user.home') }}" class="app-auth__logo">
                <img src="{{ asset('images/drvn-logo.png') }}" alt="DRVN">
                <span>DRVN STORE</span>
            </a>

            <div class="app-auth__pitch">
                <h1>Lupa <span>password</span>?</h1>
                <p>Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat password baru.</p>
            </div>

            <div class="app-auth__foot">© {{ date('Y') }} Drive Venture</div>
        </div>

        <div class="app-auth__form">
            <div class="app-auth__head">
                <h2>Reset password</h2>
                <p>Kami akan kirim tautan reset ke email Anda.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success py-2 px-3 small" style="margin-bottom: 18px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="app-auth__field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="nama@email.com"
                           value="{{ old('email') }}" required @error('email') class="is-invalid" @enderror>
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="app-auth__submit">Kirim Tautan Reset</button>
            </form>

            <div class="app-auth__alt">
                Sudah ingat password?
                <a href="{{ route('login') }}">Login di sini</a>
            </div>
        </div>

    </div>
</div>
@endsection
