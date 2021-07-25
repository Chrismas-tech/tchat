<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageEvent;
use App\Models\Message;
use App\Models\User;
use App\Models\UserMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function conversation($userId)
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $user = Auth::user();

        $user_full_name = $user->firstname . ' ' . $user->lastname;

        $friendInfo = User::findOrFail($userId);
        $friend_full_name = $friendInfo->firstname . ' ' . $friendInfo->lastname;

        return view('message.conversation', compact('users', 'user','user_full_name', 'friendInfo', 'friend_full_name'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required',
        ]);

        /* Creating User_message */
        $datas_user_message = [
            'message_id' => Auth::id(),
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->firstname.' '.Auth::user()->lastname,
            'receiver_id' => $request->receiver_id,
            'content' => $request->message,
        ];

        /* Creating Message */
        $datas_message = [
            'message' => $request->message,
        ];

        event(new PrivateMessageEvent($datas_user_message));

        if (Message::create($datas_message) && UserMessage::create($datas_user_message)) {
            try {
                return response()->json([
                    'data' => $datas_user_message,
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
