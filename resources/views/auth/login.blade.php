@extends('templates.auth-template')

@section('content')
    <div class="centered-login">
        <div class="mb-4">
            <h3 class="text-white">Laravel Chat App</h3>
        </div>

        <div class="d-flex">

            <form method="POST" action="{{ route('login') }}" class="form-border">
                @csrf

                <div class="d-flex input-group input-group-lg mb-3">
                    <div class="input-group-prepend">
                        <i class="fas fa-lg fa-user icon-login"></i>
                    </div>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="Email" aria-label="Email">
                </div>

                <div class="input-group input-group-lg mb-3">
                    <div class="input-group-prepend">
                        <i class="fas fa-lg fa-lock icon-login"></i>
                    </div>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" value="{{ old('password') }}" placeholder="Password">
                </div>

                @if ($errors->any())
                    <div class="bg-purple text-white p-2 rounded mb-2">
                        <p class="something-went-wrong"><strong>Whoopps ! <br> Something went wrong !</strong></p>
                        @foreach ($errors->all() as $error)
                            <div>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (Route::has('password.request'))
                    <a href=" {{ route('password.request') }}">
                        Forgot Your Password?
                    </a>
                @endif

                <div class="d-flex align-items-center">
                    <input class="size-checkbox mr-1" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label text-white" for="remember">
                        Remember me
                    </label>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-dark">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="m-auto text-center p-3">
                <div>
                    <h4 class="text-white">Not registered yet?</h4>
                    <a href="{{ route('register') }}">
                        <p class="click-here">Click here</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
