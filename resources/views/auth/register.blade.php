@extends('templates.auth-template')

@section('content')
    <div class="width-register">
        <div class="d-flex align-items-center mb-4">
            <h2 class="text-white">Register</h2>
        </div>

        <form id="form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div class="d-flex align-items-center font-male-female mb-3">
                <div class="d-flex align-items-center mr-5">
                    <label class="m-0" for="male">Male</label>
                    <input type="radio" name="sex" id="male" value="male" checked>
                </div>
                <div class="d-flex align-items-center">
                    <label class="m-0" for="female">Female</label>
                    <input type="radio" name="sex" id="female" value="female">
                </div>
            </div>

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

            <div class="d-flex align-items-center input-group input-group-lg mb-3">
                <label for="file" class="text-white label-upload rounded">Upload your profile photo here (3mo max.)<img
                        class="img-upload-arrow" src="{{ asset('img/upload.png') }}" alt="upload-arrow"></label>
                <img id="checked" class="ml-3 d-none img-upload-arrow" src="{{ asset('img/checked.png') }}" alt="checked">
                <img id="unchecked" class="ml-3 d-none img-upload-arrow" src="{{ asset('img/unchecked.png') }}"
                    alt="unchecked">
                <input id="file" type="file" name="avatar" autocomplete="file">
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
                <a href="{{ route('login') }}" class="btn btn-lg bg-blue text-white mr-3">Back</a>
                <button type="submit" class="btn btn-lg btn-dark">
                    Register
                </button>
            </div>
        </form>
    </div>

    <script>
        $(function() {

            $('#file').change(function() {
                /* Si des fichiers sont prêts à l'upload */
                if (this.files[0]) {

                    console.log(this.files[0]);
                    console.log(this.files[0].size);

                    let extension_split = this.files[0].name
                    let extension_file = extension_split.substr(-3);
                    let size_file = this.files[0].size

                    if ((extension_file == "png" || extension_file == "jpg" || extension_file ==
                            "jpeg" || extension_file == "tiff") && size_file < 3000000) {

                        $('#unchecked').addClass('d-none')
                        $('#checked').removeClass('d-none')

                    } else {
                        $('#checked').addClass('d-none')
                        $('#unchecked').removeClass('d-none')
                        alert('Incorrect image format or image too big (png, jpg, jpeg, tiff - 2mo max) !')
                    }
                }
            })


        })
    </script>
@endsection
