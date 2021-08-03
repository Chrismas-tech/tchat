@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">
                <h5 class="bg-primary w-max-content text-white px-2 py-2 rounded">Users registered</h5>

                <ul class="list-group list-chat-item mt-4">
                    @if ($users->count())
                        @foreach ($users as $user)
                            <li class="chat-user-list">
                                <a href="{{ route('message.conversation', $user->id) }}"
                                    class="d-flex align-items-center">

                                    <div class="chat-image bg-primary">
                                        <i class="fa fa-circle fa-xs user-status-icon" id="status-{{ $user->id }}"
                                            title="Away"></i>
                                        <div class="name-image">
                                            {{ makeShortCutName($user->firstname . ' ' . $user->lastname) }}
                                        </div>
                                    </div>

                                    <div class="m-auto chat-name ml-1">
                                        {{ $user->firstname . ' ' . $user->lastname }}
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <div class="mt-4">
                <h5 class="w-max-content text-white px-2 py-2 rounded add-user-group" data-toggle="modal"
                    data-target="#Modal_add_to_group">Groups <i class="class text-secondary fa fa-plus ml-1"></i>
                </h5>

                <ul class="list-group list-chat-item mt-4">
                    @if ($groups->count())

                        @foreach ($groups as $group)

                            <!-- Si le créateur du groupe est l'utilisateur connecté alors on afficher un lien vers la page du groupe -->

                            @if ($group->user_id == Auth::id())
                                <a href="{{ route('message-groups.show', $group->id) }}">
                                    <li class="chat-group-list">
                                        {{ $group->name }}
                                    </li>
                                </a>
                            @else

                                <!-- Sinon pour chacun des membres du groupe, si le user_id est égal à l'utilisateur connecté alors on lui affiche aussi le lien vers le groupe -->

                                @foreach ($group->message_group_members as $member)
                                    @if ($member->user_id == Auth::id())

                                        <a href="{{ route('message-groups.show', $group->id) }}">
                                            <li class="chat-group-list">
                                                {{ $group->name }}
                                            </li>
                                        </a>
                                    @endif
                                @endforeach

                            @endif

                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-9 d-flex justify-content-center">
            <div>
                <h1>
                    Message Section
                </h1>
                <p>Select a user from the list to begin conversation.</p>
                <p>You can also create a group and add people you know to the discussion.</p>
            </div>
        </div>
    </div>


    @include('templates.modal')
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.js-example-basic-single').select2();

            let user_id = "{{ auth()->user()->id }}"
            let ip_address = '51.77.157.244';
            let socket_port = '3000';

            let address = "https://tchat.duckdns.org:3000";
        /*     let socket = io(address) */

            let socket = io.connect('https://tchat.duckdns.org:4200');

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
