<?php

namespace App\Http\Controllers;

use App\Events\PrivateGroupEvent;
use App\Events\PrivateMessageEvent;
use App\Models\Message;
use App\Models\MessageGroup;
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

        $user_messages = UserMessage::where('sender_id', [Auth::id(), $userId])->orwhere('receiver_id', [Auth::id(), $userId])->get();

        $friendInfo = User::findOrFail($userId);
        $friend_full_name = $friendInfo->firstname . ' ' . $friendInfo->lastname;

        $groups = MessageGroup::all();

        return view('message.conversation', compact('users', 'user', 'user_messages', 'user_full_name', 'friendInfo', 'friend_full_name', 'groups'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required',
        ]);

        /* Creating Message */
        $datas_message = [
            'message' => $request->message,
        ];

        /* Creating User_message */
        $count_message = Message::all()->count();

        $datas_user_message = [
            'message_id' => $count_message + 1,
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'receiver_id' => $request->receiver_id,
            'content' => $request->message,
        ];

        event(new PrivateMessageEvent($datas_user_message));

        if (Message::create($datas_message) && UserMessage::create($datas_user_message)) {
            try {
                return response()->json([
                    'message' => $datas_message,
                    'datas_user_message' => $datas_user_message,
                    'success' => true,
                    'confirmation' => 'Message sent successfully'
                ]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function sendGroupMessage(Request $request)
    {

        /* Creating Message */
        $datas_message = [
            'message' => $request->message,
        ];

        /* Creating User_message */
        $count_message = Message::all()->count();

        $datas_user_message = [
            'message_id' => $count_message + 1,
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'message_group_id' => intval($request->group_id),
            'content' => $request->message,
        ];

        event(new PrivateGroupEvent($datas_user_message));

        if (Message::create($datas_message) && UserMessage::create($datas_user_message)) {
            try {
                return response()->json([
                    'datas_message' => $datas_message,
                    'datas_user_message' => $datas_user_message,
                    'success' => true,
                    'confirmation' => 'Message to Group sent successfully'
                ]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
