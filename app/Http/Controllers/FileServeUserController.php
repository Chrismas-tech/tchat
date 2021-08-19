<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FileServeUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile_image_serve($user_id)
    {
        /* Vérification que l'image affiché correspond à l'utilisateur connecté ! */
        if ($user_id == Auth::id()) {

            /* Si un avatar existe en base de donnée --> on renvoie le fichier existant */
            if (Auth::user()->avatar) {
                $path = storage_path('app/private/profile-photos/user-id-' . $user_id . '/' . Auth::user()->avatar);
                return response()->file($path);

                /* Sinon on renvoie une image no-name*/
            } else {
                $path = public_path('/img/unknown.png');
                return response()->file($path);
            }
        } else {
            /* Sinon page d'erreur */
            return abort('404');
        }
    }

    public function profile_image_friends_serve($user_id)
    {
        $friend = User::find($user_id);

        if ($friend->avatar) {
            $path = storage_path('app/private/profile-photos/user-id-' . $friend->id . '/' . $friend->avatar);
            return response()->file($path);

            /* Sinon on renvoie une image no-name*/
        } else {
            $path = public_path('/img/unknown.png');
            return response()->file($path);
        }
    }
}
