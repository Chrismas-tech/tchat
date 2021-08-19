<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchUserController extends Controller
{
    public function search_user_DB(Request $request)
    {
        $name = $request->message;

        $users_match = DB::table('users')
            ->where('id', '!=', Auth::id())->where('firstname', 'LIKE', '%' . $name . '%')->orWhere('lastname', 'LIKE', '%' . $name . '%')->get();

        if ($users_match->count()) {
            return response()->json([
                'users_list' => $users_match
            ]);
        } else {
            return response()->json([
                'error' => 'There is no result for your search'
            ]);
        }

        /* try {
            return response()->json([
                'message' => $datas_message,
                'datas_user_message' => $datas_user_message,
                'success' => true,
                'confirmation' => 'Message sent successfully'
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        } */
    }
}
