@extends('layouts.home')

@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12 container justify-content-center">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="mtext-111 cl2 mb-5">Reset Your Password</h1>
                                </div>

                                <!-- Form Start -->
                                <form method="POST" action="{{ route('password.update') }}" class="user">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="form-group">
                                        <input id="email" type="email"
                                            class="stext-102 form-control form-control-user @error('email') is-invalid @enderror"
                                            name="email" value="{{ $email ?? old('email') }}"
                                            placeholder="Enter Email Address..."
                                            required autocomplete="email" autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input id="password" type="password"
                                            class="stext-102 form-control form-control-user @error('password') is-invalid @enderror"
                                            name="password" placeholder="New Password"
                                            required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input id="password-confirm" type="password"
                                            class="stext-102 form-control form-control-user"
                                            name="password_confirmation" placeholder="Confirm New Password"
                                            required autocomplete="new-password">
                                    </div>

                                    <button type="submit"
                                        class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                                        Reset Password
                                    </button>
                                </form>
                                <!-- Form End -->

                                <hr>
                                <div class="stext-102 cl2 text-center">
                                    <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                </div>
                                <div class="stext-102 cl2 text-center">
                                    <a class="small" href="{{ route('login') }}">Already have an account? Login!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
