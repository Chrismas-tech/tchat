@extends('layouts.app')

@section('content')

    <div class="row chat-row">

        <div class="col-md-3">
            <div>
                <a href="{{ route('home') }}"
                    class="w-max-content bg-primary text-white px-2 py-2 rounded add-user-group">Return to the
                    user section</a>
            </div>

            <div class="mt-5">
                <h5 class="w-max-content text-white px-2 py-2 rounded add-user-group" data-toggle="modal"
                    data-target="#Modal_add_to_group">Groups <i class="class text-secondary fa fa-plus ml-1"></i>
                </h5>

                <ul class="list-group list-chat-item mt-3">
                    @if ($groups->count())
                        @foreach ($groups as $group)
                            @if ($group->user_id == Auth::id() or $group->message_group_members[0]->user_id == Auth::id())

                                <a href="{{ route('message-groups.show', $group->id) }}">
                                    <li class="chat-group-list">
                                        {{ $group->name }}
                                    </li>
                                </a>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-9 chat-section">
            <div class="chat-header">

                <div class="d-flex align-items-center">
                    <div class="chat-name text-white font-bold bg-success mr-2 rounded px-2 py-2">
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

                <div class="d-flex align-items-center mt-2">
                    <div class="bg-warning mr-2 rounded px-2 py-1 font-weight-bold text-2xl">
                        <img class="icon-profile" src="{{ asset('img/icon-profile.png') }}" alt="icon-profile">Persons
                        invited :
                    </div>

                    @foreach ($users_of_group as $user)

                        <div class="d-flex align-items-center mr-3">
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

                </div>
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

        <div class="col-md-3">
            {{-- @if ($currentGroup->message_goup_members) --}}
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

            let ip_address = '127.0.0.1'
            let socket_port = '3000'
            let socket = io(ip_address + ':' + socket_port)

            let $chatInput = $("#chatInput")
            let $chatInputTollbar = $('.chat-input-toolbar')
            let $chatBody = $(".chat-Body")

            let groupId = "{{ $currentGroup->id }}"
            let groupName = "{{ $currentGroup->name }}"



            socket.emit('user_connected', sender_id)

            $("#messageWrapper").scrollTop($("#messageWrapper")[0].scrollHeight);

 /*            socket.on('UserStatus', users => {

                let userStatusIcon = $('.user-status-icon');
                userStatusIcon.removeClass('online')
                userStatusIcon.attr('title', 'Away');

                $.each(users, (index, el) => {
                    if ($('#status-' + el.user_id)) {
                        $('#status-' + el.user_id).addClass('online').attr('title', 'Online')
                    }
                }) 

            }) */

            $chatInput.on('click', function() {
                let placeholder = $(this).text();

                if (placeholder.trim() == ('Write your message here...')) {
                    $(this).text('');
                }
            })

            $chatInput.keypress(function(e) {
                let message = $(this).text();

                /* JQuery function which -> which key was pressed */
                /* Si on tape Enter et si Shift n'est pas enfoncée */

                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault()
                    $chatInput.empty();
                    sendMessage(message);
                }
            })

            function sendMessage(message) {
                console.log("SENDER GROUP MESSAGE");
                console.log(message);
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
                $('#audio_sent')[0].play()
            })

            function appendMessageToReceiver(message) {
                /*                 console.log('APPEND RECEIVER');
                                console.log(message) */

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

            }


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





        })
    </script>
@endpush
