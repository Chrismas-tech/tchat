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
                                    class="d-flex align-items-center text-decoration-none">

                                    <div class="chat-image">
                                        <i class="fa fa-circle fa-xs user-status-icon" id="status-{{ $user->id }}"
                                            title="Away"></i>
                                        <div class="name-image">
                                            @php
                                                $user_name_full = $user->firstname . ' ' . $user->lastname . '';
                                            @endphp
                                            {{ makeShortCutName($user_name_full) }}
                                        </div>
                                    </div>

                                    <div class="m-auto chat-name ml-1 font-bold">
                                        {{ $user_name_full }} <span id="notif"></span>
                                    </div>
                                </a>
                            </li>

                        @endforeach
                    @endif

                </ul>
            </div>

            <div class="mt-5">
                <h5 class="w-max-content text-white px-2 py-2 rounded add-user-group" data-toggle="modal"
                    data-target="#Modal_add_to_group">Groups <i class="class text-secondary fa fa-plus ml-1"></i>
                </h5>

                <ul class="list-group list-chat-item mt-3">
                    @if ($groups->count())

                        @foreach ($groups as $group)

                            <!-- Si le créateur du groupe est l'utilisateur connecté alors on afficher un lien vers la page du groupe -->

                            @if ($group->user_id == Auth::id())
                                <a href="{{ route('message-groups.show', $group->id) }}">
                                    <li
                                        class="chat-group-list {{ $group->id == $currentGroup->id ? 'active-group' : '' }}">
                                        {{ $group->name }}
                                    </li>
                                </a>
                            @else

                                <!-- Sinon pour chacun des membres du groupe, si le user_id est égal à l'utilisateur connecté alors on lui affiche aussi le lien vers le groupe -->

                                @foreach ($group->message_group_members as $member)
                                    @if ($member->user_id == Auth::id())

                                        <a href="{{ route('message-groups.show', $group->id) }}">
                                            <li
                                                class="chat-group-list {{ $group->id == $currentGroup->id ? 'active-group' : '' }}">
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

        <div class="col-md-9 chat-section">
            <div class="chat-header">

                <div class="d-flex align-items-center">
                    <div class="chat-name text-white font-bold active-group mr-2 rounded px-2 py-2">
                        Name of group : {{ $currentGroup->name }}
                    </div>

                    <div>
                        <img class="icon-audio" src="{{ asset('img/haut-parleur-on.png') }}" alt="icon-audio">
                    </div>
                </div>

                <div class="d-flex align-items-center mt-2">
                    <div class="bg-warning mr-2 rounded px-2 py-1 font-weight-bold text-2xl">
                        <img class="icon-profile" src="{{ asset('img/icon-admin.png') }}" alt="icon-profile">Créateur du
                        groupe :
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="chat-image">
                            <div class="name-image">
                                @php
                                    $user_name_full = $currentGroup->user->firstname . ' ' . $currentGroup->user->lastname . '';
                                @endphp
                                {{ makeShortCutName($user_name_full) }}
                            </div>
                        </div>

                        <div class="chat-name ml-1 font-bold">
                            {{ $currentGroup->user->firstname }} {{ $currentGroup->user->lastname }}
                        </div>
                    </div>

                </div>

                <div class="d-flex flex-wrap align-items-center mt-2">
                    <div class="bg-success text-white mr-2 rounded px-2 py-1 font-weight-bold">
                        <img class="icon-profile" src="{{ asset('img/icon-profile.png') }}" alt="icon-profile">Persons
                        invited :
                    </div>

                    @foreach ($members_of_group as $user)

                        <div class="d-flex align-items-center mr-2">
                            <div class="chat-image">
                                <div class="name-image">
                                    @php
                                        $user_of_group = $user->user->firstname . ' ' . $user->user->lastname . '';
                                    @endphp
                                    {{ makeShortCutName($user_of_group) }}
                                </div>
                            </div>

                            <div class="chat-name ml-1 font-bold">
                                {{ $user->user->firstname }} {{ $user->user->lastname }}
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="chat-body" id="chatBody">
                <div class="message-listing" id="messageWrapper">
                    <!-- Pour tous les messages -->
                    @foreach ($messages_of_group as $message)

                        <!-- Pour chaque message on recherche son groupId et son sender -->
                        @foreach ($message->user_messages as $user_message)

                            <!-- Si le message appartien au groupe courant et que le sender est l'utilisateur connecté, alors on affiche à droite -->

                            @if ($user_message->message_group_id == $currentGroup->id and $user_message->sender_id == Auth::id())
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center">
                                            <div class="chat-image">
                                                <div class="name-image">
                                                    {{ makeShortCutName($user_full_name) }}
                                                </div>
                                            </div>

                                            <div class="chat-name ml-1 font-weight-bold">{{ $user_full_name }}
                                                <span class="small time text-secondary"
                                                    title="{{ $user_message->message->created_at }}">{{ created_at_format_date($user_message->message->created_at) }}</span>
                                            </div>
                                        </div>
                                        <div class="message-text">
                                            {{ $user_message->message->message }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Sinon on affiche à gauche -->
                            @elseif ($user_message->message_group_id == $currentGroup->id and $user_message->sender_id
                                != Auth::id())

                                <div class="d-flex justify-content-start">
                                    <div>
                                        <div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center">
                                            <div class="chat-image">
                                                <div class="name-image">
                                                    @php
                                                        $friend = App\Models\User::find($user_message->sender_id);
                                                        $friend_full_name = $friend->firstname . ' ' . $friend->lastname;
                                                    @endphp
                                                    {{ makeShortCutName($friend_full_name) }}
                                                </div>
                                            </div>
                                            <div class="chat-name ml-1 font-weight-bold">
                                                {{ $friend->firstname }} {{ $friend->lastname }}
                                                <span class="small time text-secondary"
                                                    title="{{ $friend->created_at }}">{{ created_at_format_date($friend->created_at) }}</span>
                                            </div>
                                        </div>
                                        <div class="message-text">{{ $user_message->message->message }}</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="d-flex font-italic" id="writing">

            </div>

            <div class="chat-box">
                <div class="chat-input bg-white" id="chatInput" contenteditable="true">Write your message here...
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

    <!-- Audio Sound -->
    <audio id="audio_sent" src="{{ asset('audio/1313.mp3') }}" preload="auto"></audio>

    <!-- Modal -->
    <div class="modal fade" id="Modal_add_to_group" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('message-groups.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Choose group name</label>
                            <input type="text" class="form-control" name="name">
                        </div>

                        <div class="form-group">
                            <label for="name">Add user(s) to the current discussion</label>
                            <select class="js-example-basic-single form-control" name="user_id[]" multiple="multiple">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->firstname }} {{ $user->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(function() {

            $('.js-example-basic-single').select2()

            let ip_address = 'tchat.duckdns.org';
            let socket_port = '3000';
            let socket = io(ip_address + ':' + socket_port)

            let $chatInput = $("#chatInput")
            let $chatInputTollbar = $('.chat-input-toolbar')
            let $chatBody = $(".chat-Body")

            let sender_id = '{{ Auth::id() }}'
            let sender_name = '{{ Auth::user()->firstname }}' + ' ' + '{{ Auth::user()->lastname }}'

            let group_id = '{{ $currentGroup->id }}'
            let group_name = '{{ $currentGroup->name }}'

            socket.on('connect', function() {
                let data = {
                    group_id: group_id,
                    user_id: sender_id,
                    room: "group" + group_id
                }
                socket.emit('user_connected', sender_id)
                socket.emit('joinGroup', data)
            })

            $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);


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

            /* CHAT INPUT */
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/

            $chatInput.on('click', function() {
                let placeholder = $(this).text();

                if (placeholder.trim() == ('Write your message here...')) {
                    $(this).text('');
                }
            })

            $chatInput.on('keyup ', e => {

                let message = $chatInput.text();
                let length_message = message.length

                /* JQuery function which -> which key was pressed */
                /* Si on tape Enter et si Shift n'est pas enfoncée */

                if (e.which === 13 && !e.shiftKey) {

                    /* Si il y a du texte dans le champ input alors --> on envoie un message au serveur */
                    if (length_message > 0) {

                        /* on évite un retour à la ligne */
                        e.preventDefault()
                        sendMessage(message);

                        socket.emit('remove_writing_group', {
                            sender_id: sender_id,
                            sender_name: sender_name
                        })

                        $chatInput.empty();

                    } else {
                        e.preventDefault()
                    }
                }

                if (length_message > 0) {

                    /* Si le champ n'est pas vide, on émet vers le serveur */

                    socket.emit('is_writing_group', {
                        sender_id: sender_id,
                        sender_name: sender_name
                    })


                } else {
                    /* Sinon on émet vers le serveur pour que le destinataire enlève sa div writing */

                    socket.emit('remove_writing_group', {
                        sender_id: sender_id,
                        sender_name: sender_name
                    })
                }
            })

            /* END CHAT INPUT */
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/

            function sendMessage(message) {

                appendMessageToSender(message)

                $.ajax({
                    url: "{{ route('message.send-group-message') }}", // La ressource ciblée
                    method: 'POST', // Le type de la requête HTTP
                    dataType: 'JSON', // On définit le type des données retourné par le serveur (évite d'utiliser JSON.parse pour la réponse)

                    data: {
                        message: message,
                        _token: "{{ csrf_token() }}",
                        group_id: group_id,
                        group_name: group_name,
                    },

                    success: function(response, status) {
                        if (response.success) {
                            console.log(response);
                        }
                    },

                    error: function(response) {
                        console.log(response);
                    }
                });
            }

            socket.on("private-channel:App\\Events\\PrivateMessageEvent", function(message) {
                appendMessageToReceiver(message)
                $('#audio_sent')[0].play()
            })

            socket.on("groupMessage", function(message) {
                appendMessageToReceiver(message)
                $('#audio_sent')[0].play()
            })

            /* AUDIO */
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/

            $('.icon-audio').on('click', function() {
                if ($(this).attr('src') == '{{ asset('img/haut-parleur-on.png') }}') {
                    console.log('ON -> OFF');
                    $(this).attr('src', '{{ asset('img/haut-parleur-off.png') }}')
                    $('#audio_sent').attr('src', '')
                } else {
                    console.log('OFF -> ON');
                    $(this).attr('src', '{{ asset('img/haut-parleur-on.png') }}')
                    $('#audio_sent').attr('src', '{{ asset('audio/1313.mp3') }}')
                }
            })

            /* END AUDIO */
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/


            /* FUNCTIONS APPEND*/
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/
            function appendMessageToReceiver(message) {


                if (message.sender_id != sender_id) {

                    let name = message.sender_name;
                    let name_shortcut = message.shortcut_name;

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center"><div class="chat-image"><div class="name-image">' +
                        name_shortcut + '</div></div><div class="chat-name ml-1 font-weight-bold">' + name + ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                        getCurrentTime() + '</span></div></div><div class="message-text">' + message.content +
                        '</div></div></div>';

                    $('#messageWrapper').append(new_message);
                }
                $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
            }

            function appendMessageToSender(message) {

                let $user_full_name = '{{ $user_full_name }}';
                let $image = '{{ makeShortCutName($user_full_name) }}';

                let new_message =
                    '<div class="d-flex justify-content-end"><div><div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center"><div class="chat-image"><div class="name-image">' +
                    $image + '</div></div><div class="chat-name ml-1 font-weight-bold">' + $user_full_name + ' ' +
                    '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                    getCurrentTime() + '</span></div></div><div class="message-text">' + message +
                    '</div></div></div>';

                $('#messageWrapper').append(new_message);
                $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
            }
            /* END FUNCTIONS APPEND*/
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/

            socket.on('is_writing_group', (data) => {

                let attribute = 'writer' + '-' + data.user_id + '-' + data.user_name;
                let find_attribute = document.getElementById(attribute);
                /* console.log(find_attribute); */

                if (!find_attribute) {

                    let div = document.createElement('div');
                    let message = data.user_name + ' is writing '

                    $(div).attr("id", attribute)
                    $(div).text(message)
                    $(div).addClass('is-writing')

                    $('#writing').append(div);
                    let gif = '<img class="writing-gif" src="{{ asset('img/writing.gif') }}"/>';
                    $(div).append(gif)
                }

            })


            socket.on('remove_writing_group', (data) => {

                let attribute = 'writer' + '-' + data.user_id + '-' + data.user_name;
                let find_attribute = document.getElementById(attribute);

                console.log(find_attribute);

                if (find_attribute) {
                    console.log(true);
                    console.log('attribute exist');
                    
                    /* AUCUNE IDEE MAIS SANS UN SET-TIMEOUT CA NE MARCHE PAS !!!! */
                    setTimeout(() => {
                        document.getElementById(attribute).remove()
                    }, 1);

                }

            })
        })
    </script>
@endpush
