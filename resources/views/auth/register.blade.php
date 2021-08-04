@extends('templates.auth-template')

@section('content')
    <div class="width-register">
        <div class="d-flex align-items-center mb-4">
            <h2 class="text-white">Register</h2>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="d-flex input-group input-group-lg mb-3">
                <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}"
                    autocomplete="firstname" autofocus placeholder="Firstname">
            </div>

            <div class="d-flex input-group input-group-lg mb-3">
                <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}"
                    autocomplete="lastname" autofocus placeholder="Lastname">
            </div>

            <div class="d-flex input-group input-group-lg mb-3">
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                    autocomplete="email" placeholder="Email">
            </div>

            <div class="d-flex input-group input-group-lg mb-3">
                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password"
                    placeholder="Password">
            </div>

            <div class="d-flex input-group input-group-lg mb-3">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                    autocomplete="new-password" placeholder="Confirm your password">
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

            <div class="d-flex justify-content-end mt-4">
                <a href="{{route('login')}}"class="btn btn-lg bg-purple text-white mr-3">Back</a>
                <button type="submit" class="btn btn-lg btn-dark">
                    Register
                </button>
            </div>
        </form>
    </div>

@endsection
