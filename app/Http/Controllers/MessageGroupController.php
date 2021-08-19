<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\MessageGroupMember;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'name' => $request->name,
            'user_id' => Auth::id()
        ];

        $messageGroup = MessageGroup::create($data);

        if ($messageGroup) {
            if ($request->user_id && !empty($request->user_id)) {

                foreach ($request->user_id as $userId) {

                    $member_data = [
                        'message_group_id' => $messageGroup->id,
                        'user_id' => $userId,
                        'status' => 0,
                    ];

                    MessageGroupMember::create($member_data);
                }
            }
        }
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($groupId)
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $user = Auth::user();
        $user_full_name = $user->firstname . ' ' . $user->lastname;

        $groups = MessageGroup::all();
        $currentGroup = MessageGroup::where('id', '=', $groupId)->first();

        $members_of_this_group = MessageGroupMember::where('message_group_id', '=', $groupId)->get();
        $messages_of_this_group = UserMessage::where('message_group_id', '=', $groupId)->get();

        /* dd($images_members); */
        /* dd($members_of_group); */

        return view('message_groups.group', compact('users', 'user', 'user_full_name', 'groups', 'currentGroup', 'messages_of_this_group', 'members_of_this_group'));
    }
}
