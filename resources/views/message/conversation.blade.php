@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3">

            <h5 class="bg-primary w-max-content text-white px-2 py-2 rounded">Users registered</h5>

            <div class="users">

                <div class="d-flex py-1">
                    <input type="text" class="w-100 input-search-user" placeholder="Search a user...">
                    <button class="btn-loupe">
                        <img src="{{ asset('img/loupe.png') }}" alt="loupe">
                    </button>
                </div>

                <ul class="list-group list-chat-item mt-2">
                    @if ($users->count())
                        @foreach ($users as $user)

                            <li class="chat-user-list @if ($user->id == $friendInfo->id) active @endif">

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

                                    <div
                                        class="m-auto chat-name ml-1 font-bold 
                                        {{ $user->id == $friendInfo->id ? 'text-white' : '' }}">
                                        {{ $user_name_full }} <span id="notif"></span>
                                    </div>

                                    <div>
                                        <img src="{{ asset('img/conversation.png') }}" alt="img-conversation"
                                            class="img-conversation">
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

                <ul class="list-group list-chat-item mt-2">
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

        <div class="col-md-9 chat-section rounded">
            <div class="chat-header d-flex align-items-center">
                <div class="chat-image">
                    <div class="name-image">
                        {{ makeShortCutName($friend_full_name) }}
                    </div>
                </div>

                <div class="chat-name ml-1 font-bold">
                    {{ $friend_full_name }}
                </div>

                <div>
                    <img class="icon-audio" src="{{ asset('img/hp-on.png') }}" alt="icon-audio"
                        title="on/off sounds effects">
                </div>
            </div>

            <div class="chat-body" id="chatBody">
                <div class="message-listing" id="messageWrapper">

                    @foreach ($user_messages as $user_message)
                        @if ($user_message->sender_id == Auth::id() && $user_message->receiver_id == $friendInfo->id)
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
                        @elseif ($user_message->sender_id == $friendInfo->id && $user_message->receiver_id ==
                            Auth::id())
                            <div class="d-flex justify-content-start">
                                <div>
                                    <div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center">
                                        <div class="chat-image">
                                            <div class="name-image">
                                                {{ makeShortCutName($friend_full_name) }}
                                            </div>
                                        </div>
                                        <div class="chat-name ml-1 font-weight-bold">{{ $friendInfo->firstname }}
                                            {{ $friendInfo->lastname }}
                                            <span class="small time text-secondary"
                                                title="{{ $user_message->message->created_at }}">{{ created_at_format_date($user_message->message->created_at) }}</span>
                                        </div>
                                    </div>
                                    <div class="message-text">{{ $user_message->message->message }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
            </div>
            <div class="d-flex font-italic" id="writing">

            </div>

            <div class="chat-box">
                <div class="d-flex align-items-end">
                    <div class="chat-input bg-white w-100" id="chatInput" contenteditable="true">Write your message here...
                    </div>
                    <div class="p-1">
                        <button id="send-btn"><i class="fas fa-paper-plane fa-2x"></i></button>
                    </div>
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
    <audio id="audio_hp" src="{{ asset('audio/1313.mp3') }}" preload="auto"></audio>
    <audio id="audio_arrow_mess" src="{{ asset('audio/1314.mp3') }}" preload="auto"></audio>

    @include('templates.modal')
@endsection


@push('scripts')
    <script src="{{ asset('js/server-connexion.js') }}"></script>
    <script>
        $(function() {

            $('.js-example-basic-single').select2();

            let $chatInput = $("#chatInput");
            let $chatInputTollbar = $('.chat-input-toolbar');
            let $chatBody = $(".chat-Body")

            let sender_id = '{{ Auth::id() }}'
            let sender_name = '{{ Auth::user()->firstname }}' + ' ' + '{{ Auth::user()->lastname }}'

            let receiver_id = "{{ $friendInfo->id }}"
            let receiver_name = '{{ $friendInfo->firstname }}' + ' ' + '{{ $friendInfo->lastname }}'

            socket.emit('user_connected', sender_id)

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

                        socket.emit('remove_writing', {
                            receiver_id: receiver_id,
                            sender_name: sender_name
                        })

                        $chatInput.empty();

                    } else {
                        e.preventDefault()
                    }
                }

                if (length_message > 0) {

                    /* Si le champ n'est pas vide, on émet vers le serveur */

                    socket.emit('is_writing', {
                        receiver_id: receiver_id,
                        sender_name: sender_name
                    })


                } else {
                    /* Sinon on émet vers le serveur pour que l'autre enlève sa div writing */

                    socket.emit('remove_writing', {
                        receiver_id: receiver_id,
                        sender_name: sender_name
                    })
                }
            })

            function sendMessage(message) {

                appendMessageToSender(message)

                $.ajax({
                    url: "{{ route('message.send-message') }}", // La ressource ciblée
                    method: 'POST', // Le type de la requête HTTP
                    dataType: 'JSON', // On définit le type des données retourné par le serveur (évite d'utiliser JSON.parse pour la réponse)

                    data: {
                        message: message,
                        _token: "{{ csrf_token() }}",
                        receiver_id: receiver_id,
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
                $('#audio_hp')[0].play()
            })

            function appendMessageToReceiver(message) {
                /*                 
                console.log('APPEND RECEIVER');
                console.log(message) 
                */

                $message_receiver_id = parseInt(message.receiver_id)

                if (message.sender_id == receiver_id) {
                    /*         console.log(true); */
                    let $friend_full_name = '{{ $friend_full_name }}';
                    let $image = '{{ makeShortCutName($friend_full_name) }}';

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center"><div class="chat-image"><div class="name-image">' +
                        $image + '</div></div><div class="chat-name ml-1 font-weight-bold">' + $friend_full_name +
                        ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                        getCurrentTime() + '</span></div></div><div class="message-text">' + message.content +
                        '</div></div></div>';

                    $('#messageWrapper').append(new_message);
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
                }
            }

            function appendMessageToSender(message) {
                /* console.log(message); */

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


            $('.icon-audio').on('click', function() {
                if ($(this).attr('src') == '{{ asset('img/hp-on.png') }}') {
                    console.log('ON -> OFF');
                    $(this).attr('src', '{{ asset('img/hp-off.png') }}')
                    $('#audio_hp').attr('src', '')
                    $('#audio_arrow_mess').attr('src', '')
                } else {
                    console.log('OFF -> ON');
                    $(this).attr('src', '{{ asset('img/hp-on.png') }}')
                    $('#audio_hp').attr('src', '{{ asset('audio/1313.mp3') }}')
                    $('#audio_arrow_mess').attr('src', '{{ asset('audio/1314.mp3') }}')
                }
            })

            socket.on('is_writing', (data) => {

                let attribute = 'writer' + '-' + data.user_id + '-' + data.user_name;
                let find_attribute = document.getElementById(attribute);

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
                $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

            })


            socket.on('remove_writing', (data) => {
                let attribute = 'writer' + '-' + data.user_id + '-' + data.user_name;

                console.log(attribute);
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

            $('#send-btn').on('click', e => {

                let message = $chatInput.text().trim();
                console.log(message);
                let length_message = message.length

                if (message == 'Write your message here...') {
                    return
                }

                if (length_message > 0) {
                    e.preventDefault()
                    sendMessage(message);

                    socket.emit('remove_writing', {
                        receiver_id: receiver_id,
                        sender_name: sender_name
                    })

                    $('#audio_arrow_mess')[0].play()

                    $chatInput.empty();
                }

            })

        })
    </script>
@endpush
