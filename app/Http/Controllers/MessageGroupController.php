<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\MessageGroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageGroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        $members_of_group = MessageGroupMember::where('message_group_id', '=', $groupId)->get();
        $messages_of_group = Message::all();

        /* ->with('message_group_members.user')
        ->first(); */

        return view('message_groups.group', compact('users', 'user', 'members_of_group', 'user_full_name', 'groups', 'currentGroup', 'messages_of_group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
