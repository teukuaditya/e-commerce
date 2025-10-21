@extends('layouts.home')

@section('content')
<div class="container">

    <div class="row justify-content-center">

        <div class="col-lg-6 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12 container justify-content-center">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="mtext-111 cl2 mb-5">Create an Account!</h1>
                                </div>
                                <!-- Form Start -->
                                <form method="POST" action="{{ route('register') }}" class="user">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="stext-102 form-control form-control-user @error('name') is-invalid @enderror"
                                               id="name" placeholder="Full Name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="email" class="stext-102 form-control form-control-user @error('email') is-invalid @enderror"
                                               id="email" placeholder="Email Address" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password" class="stext-102 form-control form-control-user @error('password') is-invalid @enderror"
                                               id="password" placeholder="Password" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password" class="stext-102 form-control form-control-user @error('password_confirmation') is-invalid @enderror"
                                               id="password-confirm" placeholder="Confirm Password" name="password_confirmation" required>
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                                        Register
                                    </button>
                                    <hr>
                                </form>
                                <!-- Form End -->
                                <div class="stext-102 cl2 text-center">
                                    <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
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
