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
                                            @if ($user->id == $friendInfo->id) active @endif">
                                <a href="{{ route('message.conversation', $user->id) }}"
                                    class="d-flex align-items-center text-decoration-none">

                                    <div class="chat-image">
                                        <i class="fa fa-circle fa-xs user-status-icon" id="status-{{ $user->id }}"
                                            title="Away"></i>
                                        <div class="name-image">
                                            {{ makeShortCutName($user->name) }}
                                        </div>
                                    </div>

                                    <div class="m-auto chat-name ml-1 font-bold 
                                                {{ $user->id == $friendInfo->id ? 'text-white' : '' }}">
                                        {{ $user->name }}
                                    </div>

                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-9 chat-section">
            <div class="chat-header d-flex align-items-center">
                <div class="chat-image">
                    <i class="fa fa-circle fa-xs user-status-icon" title="away"></i>
                    <div class="name-image">
                        {{ makeShortCutName($myInfo->name) }}
                    </div>
                </div>

                <div class="chat-name ml-1 font-bold">
                    {{ $myInfo->name }}
                </div>
            </div>

            <div class="chat-body" id="chatBody">
                <div class="message-listing" id="messageWrapper">
                    <div class="row message align-items-center mb-2">
                        <div class="col-md-12 user-info d-flex align-items-center">
                            <div class="chat-image">
                                <i class="fa fa-circle fa-xs user-status-icon" title="away"></i>
                                <div class="name-image">
                                    {{ makeShortCutName($friendInfo->name) }}
                                </div>
                            </div>
                            <div class="chat-name ml-1 font-weight-bold">
                                Manohar Kahdka
                                <span class="small time text-secondary" title="2020-05-06 10:30 PM">
                                    10:30 PM
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 message-content">
                        <div class="message-text">
                            Message Here
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-box">
                <div class="chat-input bg-white" id="chatInput" contenteditable="">
                    Write your message here...
                </div>
                <div class="chat-input-toolbar">
                    <button title="Add File" class="btn btn-light btn-sm btn-fil-upload">
                        <i class="fa fa-paperclip"></i>
                    </button>
                    |
                    <button title="Bold" class="btn btn-light btn-sm tool-items"
                        onclick="document.execCommand('bold',false,'')">
                        <i class="fa fa-bold tool-icon"></i>
                    </button>
                    |
                    <button title="Italic" class="btn btn-light btn-sm tool-items"
                        onclick="document.execCommand('italic',false,'')">
                        <i class="fa fa-italic tool-icon"></i>
                    </button>
                    |
                    <button title="Underline" class="btn btn-light btn-sm tool-items"
                        onclick="document.execCommand('underline',false,'')">
                        <i class="fa fa-underline tool-icon"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

@endsection


@push('scripts')
    <script>
        $(function() {
            let user_id = "{{ auth()->user()->id }}"
            let ip_address = '127.0.0.1';
            let socket_port = '3000';
            let socket = io(ip_address + ':' + socket_port)

            socket.emit('user_connected', user_id)

            socket.on('UserStatus', users => {

                /* Ici on update le statut, à savoir Online ou Disconnect */
                /* Note : 

                Lorsque l'on se déconnecte, le user déconnecté est retiré du tableau users ---> impossible de lui changer son statut, on passera par une autre classe pour mettre tout le monde à "absent", puis on reparcourt le tableau users */

                let userStatusIcon = $('.user-status-icon');
                userStatusIcon.removeClass('online')
                userStatusIcon.attr('title', 'Away');

                /* Pour tous les utilisateurs connectés  :
                Si #status-el.user_id existe ---> alors on ajoute une classe car l'utilisateur en question est connecté
                */

                /* Chaque index contient un élement objet el {user_id et le socket_id} */

                $.each(users, (index, el) => {
                    if ($('#status-' + el.user_id)) {
                        $('#status-' + el.user_id).addClass('online').attr('title', 'Online')
                    }
                })

            })
        })
    </script>
@endpush
