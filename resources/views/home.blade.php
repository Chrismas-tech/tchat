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

            socket.on('UserStatusOnline', users => {

                /* Pour tous les utilisateurs connectés  :
                Si #status-id == #status-key ---> alors on ajoute une classe car l'utilisteur est connecté
                */

                /* Chaque index correspond à un objet qui contient l'élement objet el (user_id et le socket_id) */
                
                $.each(users, (index, el) => {
                    if ($('#status-' + el.user_id)) {
                        $('#status-' + el.user_id).addClass('online').attr('title', 'Online')
                    }
                })
            })

            socket.on('UserStatusDisconnect', user_id => {
                if ($('#status-' + user_id)) {
                    $('#status-' + user_id).removeClass('online')
                    $('#status-' + user_id).attr('title', 'Online')
                }
            })

        })
    </script>
@endpush
