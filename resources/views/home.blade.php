@extends('layouts.app')

@section('content')
    <div class="d-flex p-5 responsive-users">

        <div class="mr-4">
            <div class="users">
                {{-- <h5 class="bg-primary w-max-content text-white px-2 py-2 rounded">Friends List</h5> --}}

                <div class="py-1" id="input-search-user-help">
                    <input id="input-search-user" type="text" class="w-100 input-search-user"
                        placeholder="Search a friend...">

                    <div>
                        <ul class="list-group list-chat-item" id="search-result-ajax">
                        </ul>
                    </div>
                </div>

                <ul class="list-group list-chat-item mb-2">
                    @if ($users->count())
                        @foreach ($users as $user)
                            <a href="{{ route('message.conversation', $user->id) }}">
                                <li class="chat-user-list d-flex align-items-center">


                                    <div class="chat-image">
                                        <i class="fa fa-circle fa-sm user-status-icon status-{{ $user->id }}"
                                            title="Away"></i>

                                        <div class="name-image mr-1">
                                            <img src="{{ route('profile_image_friends_serve', $user->id) }}" alt="avatar">
                                        </div>

                                    </div>

                                    <div class="m-auto chat-name ml-1">
                                        {{ $user->firstname . ' ' . $user->lastname }}
                                    </div>
                                </li>
                            </a>
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

        <div class="d-flex mt-5 p-4 justify-content-center mx-auto message-section">
            <div>
                <h1>
                    Message Section
                </h1>
                <p class="bg-primary text-white rounded px-1 py-1">Select a user from the list to begin conversation.</p>
                <p class="bg-primary text-white rounded px-1 py-1"> You can also create a group and add people you know to
                    the discussion.</p>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @include('modals.group-modal')
    @include('modals.zoom-image')
@endsection

@push('scripts')
    <script src="{{ asset('js/server-connexion.js') }}"></script>
    <script>
        $(function() {
            $('.js-example-basic-single').select2();

            let user_id = "{{ auth()->user()->id }}"

            socket.emit('user_connected', user_id)

            socket.on('UserStatus', users => {

                console.log(users);

                /* Ici on update le statut, à savoir Online ou Disconnect */
                /* Note : 

                Lorsque l'on se déconnecte, le user déconnecté est retiré du tableau users ---> impossible de lui changer son statut, on passera par une autre classe pour mettre tout le monde à "absent", puis on reparcourt le tableau users */

                let userStatusIcon = $('.user-status-icon');
                userStatusIcon.removeClass('online')
                userStatusIcon.attr('title', 'Away');

                /* Pour tous les utilisateurs connectés  :
                Si #status-el.user_id existe ---> alors on ajoute une classe car l'utilisateur en question est connecté 
                */

                /*    Chaque index contient un élement objet el {user_id et le socket_id} */

                $.each(users, (index, el) => {
                    console.log(el);
                    if ($('.status-' + el.user_id)) {
                        console.log('match');
                        $('.status-' + el.user_id).addClass('online').attr('title', 'Online')
                    }
                })
            })

            /* SEARCH USER AJAX */
            /* SEARCH USER AJAX */
            /* SEARCH USER AJAX */

            let $input_search = $('#input-search-user')

            $input_search.on('keyup', e => {

                let message = $input_search.val()
                let length_message = message.length

                console.log(message);
                console.log(length_message);

                if (length_message > 2) {

                    $.ajax({
                        url: "{{ route('search_user_DB') }}", // La ressource ciblée
                        method: 'POST', // Le type de la requête HTTP
                        dataType: 'JSON', // On définit le type des données retourné par le serveur (évite d'utiliser JSON.parse pour la réponse)

                        data: {
                            message: message,
                            _token: "{{ csrf_token() }}",
                        },

                        success: function(response, status) {
                            if (response.users_list) {

                                $('#search-result-ajax').empty();
                                $('#search-result-ajax').append(
                                    '<span class="results_ajax p-1 mt-1">There is <strong>' +
                                    response.users_list.length +
                                    '</strong> result(s) for your search</span>')

                                response.users_list.forEach(user => {

                                    let url_a =
                                        "{{ route('message.conversation', '') }}" +
                                        "/" + user.id
                                    let url_img =
                                        "{{ route('profile_image_friends_serve', '') }}" +
                                        "/" + user.id



                                    $('#search-result-ajax').append(
                                        '<a href="' + url_a +
                                        '"><li class="chat-user-list d-flex align-items-center"><div class="chat-image"><i class="fa fa-circle fa-sm user-status-icon status-' +
                                        user.id +
                                        '"title="Away"></i><div class="name-image mr-1"><img src="' +
                                        url_img +
                                        '" alt="avatar"></div></div><div class="m-auto chat-name ml-1">' +
                                        user.firstname + ' ' + user.lastname +
                                        '</div></li></a>')

                                    socket.emit('update_status', user.id)
                                });
                                
                                $('#search-result-ajax').append('<hr>')

                            }

                        },

                        error: function(response) {
                            console.log(response);
                        }
                    });

                } else if (length_message <= 0) {
                    console.log('0');
                    $('#search-result-ajax').empty();
                }

            })

        })
    </script>
@endpush
