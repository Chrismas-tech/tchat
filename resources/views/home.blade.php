@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">

                <h5>Users</h5>

                <ul class="list-group list-chat-item">
                    @if ($users->count())
                        @foreach ($users as $user)
                            <li class="chat-user-list">
                                <a href="{{ route('message.conversation', $user->id) }}"
                                    class="d-flex align-items-center">
                                    
                                    <div class="chat-image bg-primary">
                                        <i class="fa fa-circle fa-xs user-status-icon" id="status-{{ $user->id }}"
                                            title="Away"></i>
                                        <div class="name-image">
                                            {{ makeShortCutName($user->name) }}
                                        </div>
                                    </div>

                                    <div class="m-auto chat-name ml-1">
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
            <h1>
                Message Section
            </h1>
            Select user from the list to begin conversation.
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
