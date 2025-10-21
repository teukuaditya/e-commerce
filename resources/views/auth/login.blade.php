@extends('layouts.home')

@section('content')

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-lg-6 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12 container justify-content-center">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="mtext-111 cl2 mb-5">Welcome Back!</h1>
                                </div>
                                <form method="POST" action="{{ route('login') }}" class="user">
                                    @csrf

                                    <!-- Email Field -->
                                    <div class="form-group">
                                        <input type="email" name="email" class="stext-102 form-control form-control-user @error('email') is-invalid @enderror"
                                               id="email" placeholder="Enter Email Address..." value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Password Field -->
                                    <div class="form-group">
                                        <input type="password" name="password" class="stext-102 form-control form-control-user @error('password') is-invalid @enderror"
                                               id="password" placeholder="Password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Login Button -->
                                    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                                        Login
                                    </button>

                                    <hr>

                                    <!-- Forgot Password and Register Links -->
                                    @if (Route::has('password.request'))
                                        <div class="stext-102 cl2 text-center">
                                            <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                                        </div>
                                    @endif

                                    <div class="stext-102 text-center">
                                        <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
