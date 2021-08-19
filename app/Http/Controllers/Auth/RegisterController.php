<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => 'mimes:jpeg,jpg,png,tiff|max:3000000',
            'sex' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $count_user = User::all()->count();
        $user_id = $count_user + 1;

        $request = app('request');

        /* Si il y a une photo de profil */
        if ($request->file('avatar')) {

            /* Pour valider l'extension et la taille on s'en charge dans la fonction validator au-dessus*/
            /* Pour modifier le message d'erreur, il faut aller dans validator.php */
            $name_file = uniqid() . $request->file('avatar')->getClientOriginalName();

                $request->file('avatar')->storeAs('profile-photos/user-id-' . $user_id . '/', $name_file, 'private');

                return User::create([
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'sex' => $data['sex'],
                    'avatar' => $name_file,
                ]);
            
            /* Sinon sans photo de profil */
        } else {
            return User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'sex' => $data['sex'],
            ]);
        }
    }
}
