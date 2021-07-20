@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">

                <h5>Users</h5>

                <ul class="list-group list-chat-item">
                    @if ($users->count())
                        @foreach ($users as $user)
                            <li class="chat-user-list 
                            @if($user->id == $friendInfo->id) active @endif">
                                <a href="{{ route('message.conversation', $user->id) }}" class="d-flex align-items-center text-decoration-none">
                                    <div class="chat-image bg-primary">
                                        <i class="fa fa-circle fa-xs user-status-icon" title="away"></i>
                                        <div class="name-image">
                                            {{ makeShortCutName($user->name) }}
                                        </div>
                                    </div>

                                    <div class="chat-name ml-1 font-bold {{$user->id == $friendInfo->id ? "text-white" : '' }}">
                                        {{ $user->name }}
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            Chat body here
        </div>

    </div>

@endsection
