@extends('backend.admin.layouts.plain')

@section('meta_title', 'Admin Login')

@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-12 text-center">
            <img src="{{asset('images/phonshop.jpg')}}" width="10%" alt="">
            <h5 class="py-3">Admin Login</h5>
        </div>
    </div>

    <div class="row justify-content-center align-items-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="email"
                                class="col-md-12 col-form-label"><strong>{{ __('E-Mail Address') }}</strong></label>

                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password"
                                class="col-md-12 col-form-label"><strong>{{ __('Password') }}</strong></label>

                            <div class="col-md-12">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 d-flex justify-content-start">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
