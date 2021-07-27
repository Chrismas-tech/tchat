<?php

namespace App\Http\Controllers;

use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        
        $user = Auth::user();
        $user_full_name = $user->firstname . ' ' . $user->lastname;

        $groups = MessageGroup::all();

        return view('home', compact('users', 'user','user_full_name', 'groups'));
    }
}
