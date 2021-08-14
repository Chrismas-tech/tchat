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
                                    @if ($user_message->message->type == 1)
                                        <div class="message-text">
                                            {{ string_to_html_plus_clean_div($user_message->message->message) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($user_message->message->type == 2)
                                <div class="d-flex justify-content-end">
                                    <img src="{{ $user_message->message->message }}" alt="" class="img-style-append"
                                        data-toggle="modal" data-target="#Modal_zoom_image">
                                </div>
                            @endif

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
                                    @if ($user_message->message->type == 1)
                                        <div class="message-text">
                                            {{ string_to_html_plus_clean_div($user_message->message->message) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($user_message->message->type == 2)
                                <div class="d-flex justify-content-start">
                                    <img src="{{ $user_message->message->message }}" alt="" class="img-style-append"
                                        data-toggle="modal" data-target="#Modal_zoom_image">
                                </div>
                            @endif
                        @endif
                    @endforeach

                </div>
            </div>

            <div class="d-flex font-italic" id="writing">
            </div>

            <div id="ErrorTag" style="display:none">
                <div class="d-flex align-items-center">
                    <p class="text-danger font-italic ml-1 mb-1 px-1 rounded">
                        Html Tags are not allowed in your message ! <img src="{{ asset('img/cross.png') }}" alt="cross">
                    </p>
                </div>
            </div>

            <div class="chat-box">
                <div class="chat-input-toolbar d-flex">
                    <button id="bold" title="Bold" class="text-white bg-blue">
                        <i class="fa fa-bold tool-icon"></i>
                    </button>
                    |
                    <button id="italic" title="Italic" class="text-white bg-blue">
                        <i class="fa fa-italic tool-icon"></i>
                    </button>
                    |
                    <button id="underline" title="Underline" class="text-white bg-blue">
                        <i class="fa fa-underline tool-icon"></i>
                    </button>
                    |
                    <form id="form_send_image">
                        @csrf
                        <div class="d-flex align-items-center">
                            <div title="Add File" class="d-flex align-items-center text-white bg-blue div-send-photos">
                                <label for="file" class="attach-photo">Select photo(s) to send</label>
                                <img class="img-attach" src="{{ asset('img/img-attach.png') }}" alt="">
                                <input type="file" name="file[]" id="file" accept="image/*" multiple>
                            </div>
                            <div class="d-none p-0 m-0 btn-image-send">
                                |
                                <button type="button" class="text-white image-send-btn rounded bg-success border-none">Send
                                    files</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="images-preview" class="d-flex align-items-center">

                </div>
            </div>

            <div class="d-flex align-items-end">
                <div class="chat-input bg-white w-100" id="chatInput" contenteditable="true"> Write your message here...
                </div>
                <div class="p-1">
                    <button id="send-btn">
                        <i class="fas fa-paper-plane text-primary fa-2x"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Audio Sound -->
    <audio id="audio_hp" src="{{ asset('audio/1313.mp3') }}" preload="auto"></audio>
    <audio id="audio_arrow_mess" src="{{ asset('audio/1314.mp3') }}" preload="auto"></audio>

    <!-- Modal -->
    @include('modals.group-modal')
    @include('modals.zoom-image')
@endsection

@push('scripts')
    <script src="{{ asset('js/server-connexion.js') }}"></script>
    <script>
        $(function() {

            $('.js-example-basic-single').select2();

            /* GLOBAL VARIABLES */
            /* GLOBAL VARIABLES */

            let $chatInput = $("#chatInput");
            let $chatInputTollbar = $('.chat-input-toolbar');
            let $chatBody = $(".chat-Body")

            let sender_id = '{{ Auth::id() }}'
            let sender_name = '{{ Auth::user()->firstname }}' + ' ' + '{{ Auth::user()->lastname }}'

            let receiver_id = "{{ $friendInfo->id }}"
            let receiver_name = '{{ $friendInfo->firstname }}' + ' ' + '{{ $friendInfo->lastname }}'

            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

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

            /* CHAT INPUT SERVICES */
            /* CHAT INPUT SERVICES */

            $chatInput.on('click', function() {
                let placeholder = $(this).text();

                if (placeholder.trim() == ('Write your message here...')) {
                    $(this).text('');

                    /* Problème :
                    Si l'on clique sur un élement style puis sur le champ au chargement de la page, la fonction CommandExec ne rentre plus en compte, je contourne le problème en rétablissant les styles à 0 d'abord
                    */

                    if ($('#bold').hasClass('blue-hover-style')) {
                        console.log('blue foncé');
                        $('#bold').removeClass('blue-hover-style')
                        $('#bold').addClass('bg-blue')
                    }

                    if ($('#italic').hasClass('blue-hover-style')) {
                        console.log('blue foncé');
                        $('#italic').removeClass('blue-hover-style')
                        $('#italic').addClass('bg-blue')
                    }

                    if ($('#underline').hasClass('blue-hover-style')) {
                        console.log('blue foncé');
                        $('#underline').removeClass('blue-hover-style')
                        $('#underline').addClass('bg-blue')
                    }
                }

            })

            /* On évite de passer à la ligne avec la touche Enter --> ne marche que sur keydown pas keypress */
            $chatInput.on('keydown ', e => {
                if (e.which === 13) {
                    /* on évite un retour à la ligne */
                    e.preventDefault()
                }
            })

            $chatInput.on('keyup ', e => {

                let message_html = $chatInput.html()
                let message = $chatInput.text()
                let length_message = message.length

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

                /* JQuery function which -> which key was pressed */
                /* Si on tape Enter et si Shift n'est pas enfoncée */

                if (e.which === 13 && !e.shiftKey) {

                    /* Si il y a du texte dans le champ input alors --> on envoie un message au serveur */
                    if (length_message > 0) {

                        sendMessage(message_html);

                        socket.emit('remove_writing', {
                            receiver_id: receiver_id,
                            sender_name: sender_name
                        })

                        $('#audio_arrow_mess')[0].play()
                        $chatInput.empty();

                    } else {
                        e.preventDefault()
                    }
                }

            })


            $('#bold').on('click', () => {
                console.log('bold');
                document.execCommand('bold', false, null)
                if ($('#bold').hasClass('bg-blue')) {
                    console.log('blue');
                    $('#bold').removeClass('bg-blue')
                    $('#bold').addClass('blue-hover-style')
                } else if ($('#bold').hasClass('blue-hover-style')) {
                    console.log('blue foncé');
                    $('#bold').removeClass('blue-hover-style')
                    $('#bold').addClass('bg-blue')
                }
            })

            $('#italic').on('click', () => {
                console.log('italic');
                document.execCommand('italic', false, null)
                if ($('#italic').hasClass('bg-blue')) {
                    console.log('blue');
                    $('#italic').removeClass('bg-blue')
                    $('#italic').addClass('blue-hover-style')
                } else if ($('#italic').hasClass('blue-hover-style')) {
                    console.log('blue foncé');
                    $('#italic').removeClass('blue-hover-style')
                    $('#italic').addClass('bg-blue')
                }
            })

            $('#underline').on('click', () => {
                console.log('underline');
                document.execCommand('underline', false, null)
                if ($('#underline').hasClass('bg-blue')) {
                    console.log('blue');
                    $('#underline').removeClass('bg-blue')
                    $('#underline').addClass('blue-hover-style')
                } else if ($('#underline').hasClass('blue-hover-style')) {
                    console.log('blue foncé');
                    $('#underline').removeClass('blue-hover-style')
                    $('#underline').addClass('bg-blue')
                }
            })

            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

            /* SEND MESSAGE SERVICES */
            /* SEND MESSAGE SERVICES */

            $('#send-btn').on('click', e => {

                let message_html = $chatInput.html().trim()
                let length_message = message_html.length

                if (message_html == 'Write your message here...') {
                    return
                }

                if (length_message > 0) {
                    e.preventDefault()
                    sendMessage(message_html);

                    socket.emit('remove_writing', {
                        receiver_id: receiver_id,
                        sender_name: sender_name
                    })

                    $('#audio_arrow_mess')[0].play()

                    $chatInput.empty();
                }

            })

            function sendMessage(message) {
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
                        if (response.ErrorTag) {
                            console.log('ERRORTAG SERVER');

                            if (($('#ErrorTag:hidden'))) {
                                $('#ErrorTag').fadeIn();
                                $('#p-ErrorTag').text(response.ErrorTag);
                                setTimeout(() => {
                                    $('#ErrorTag').fadeOut();
                                }, 5000);
                            }
                        }

                        if (response.success) {
                            appendMessageToSender(message)
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
                /* console.log('SENDER');
                console.log(message); */

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

            $('#ErrorTag').on('click', () => {
                $('#ErrorTag').addClass('d-none');
            })

            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

            /* ICON AUDIO */
            /* ICON AUDIO */

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

            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

            /* SOCKET IO WRITING */
            /* SOCKET IO WRITING */

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
                    }, 100);
                }

            })

            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

            /* SEND IMAGE SERVICES */
            /* SEND IMAGE SERVICES */

            /* On mémorise le format base64 des images previews pour socket io */
            let images = [];

            $('#file').change(function() {

                $('.btn-image-send').removeClass('d-none');

                let div_preview = document.getElementById('images-preview')
                div_preview.innerHTML = "";

                /* console.log(this.files); */

                if (this.files) {

                    images = [];

                    for (const [key, file] of Object.entries(this.files)) {

                        let div_image_preview = document.createElement('div')
                        div_image_preview.classList.add('div-image-preview')
                        div_image_preview.id = file.name;

                        let img_preview = document.createElement('img')
                        img_preview.classList.add('img-preview')

                        /* let cross_image = document.createElement('img')
                        cross_image.classList.add('cross-image-preview')
                        cross_image.src = "{{ asset('img/cross-upload.png') }}" */

                        let reader = new FileReader()

                        reader.onload = function(e) {
                            data = {
                                base64: e.target.result,
                                receiver_id: receiver_id,
                                sender_name: sender_name,
                            }

                            img_preview.src = e.target.result;
                            images.push(data)
                        }

                        reader.readAsDataURL(file)

                        div_preview.append(div_image_preview)
                        div_image_preview.append(img_preview)
                        /* div_image_preview.append(cross_image) */

                    }
                }
            })

            $('.btn-image-send').on('click', () => {
                let div_images_preview = document.getElementById('images-preview')
                div_images_preview.innerHTML = "";

                $('.btn-image-send').addClass('d-none')
                sendImage(images)
            })

            function sendImage(images) {
                console.log('SEND IMAGES');
                console.log(images);

                $.ajax({
                    url: "{{ route('message.send-image') }}", // La ressource ciblée
                    method: 'POST', // Le type de la requête HTTP
                    dataType: 'JSON', // On définit le type des données retourné par le serveur (évite d'utiliser JSON.parse pour la réponse)

                    data: {
                        images: images,
                        _token: "{{ csrf_token() }}",
                        receiver_id: receiver_id,
                    },

                    success: function(response, status) {
                        if (response.ErrorTag) {
                            console.log('ERRORTAG SERVER');

                            if (($('#ErrorTag:hidden'))) {
                                $('#ErrorTag').fadeIn();
                                $('#p-ErrorTag').text(response.ErrorTag);
                                setTimeout(() => {
                                    $('#ErrorTag').fadeOut();
                                }, 5000);
                            } else {
                                return
                            }
                        }

                        if (response.success) {
                            appendImageToSender(images)
                            console.log(response);
                        }
                    },

                    error: function(response) {
                        console.log(response);
                    }
                });
            }

            socket.on('image', (image) => {
                appendImageToReceiver(image)
            })

            function appendImageToReceiver(datas) {

                console.log("DATA IMAGE RECEIVED");
                console.log(datas);

                if (datas.receiver_id == receiver_id) {

                    let img = document.createElement('img')
                    img.src = image.data
                    img.className = "img-style-append";
                    img.setAttribute('data-toggle', 'modal')
                    img.setAttribute('data-target', '#Modal_zoom_image')

                    let $friend_full_name = '{{ $friend_full_name }}';
                    let $image = '{{ makeShortCutName($friend_full_name) }}';

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center"><div class="chat-image"><div class="name-image">' +
                        $image + '</div></div><div class="chat-name ml-1 font-weight-bold">' + $friend_full_name +
                        ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                        getCurrentTime() + '</span></div></div></div></div>';

                    $('#messageWrapper').append(new_message)

                    let div_image_append = document.createElement('div')
                    div_image_append.style.display = 'flex'
                    div_image_append.style.justifyContent = 'flex-start'

                    div_image_append.append(img)

                    $('#messageWrapper').append(div_image_append)
                    $('#audio_hp')[0].play()
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
                }
            }

            function appendImageToSender(images) {
                console.log("SENDER IMAGE RECEIVED");
                console.log(images)

                images.forEach(data => {

                    let img = document.createElement('img')
                    img.src = data.base64
                    img.className = "img-style-append";
                    img.setAttribute('data-toggle', 'modal')
                    img.setAttribute('data-target', '#Modal_zoom_image')

                    let $user_full_name = '{{ $user_full_name }}'
                    let $image = '{{ makeShortCutName($user_full_name) }}'

                    let new_message =
                        '<div class="d-flex justify-content-end"><div><div class="col-md-12 mt-2 mb-2 user-info d-flex align-items-center"><div class="chat-image"><div class="name-image">' +
                        $image + '</div></div><div class="chat-name ml-1 font-weight-bold">' +
                        $user_full_name + ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() +
                        '">' +
                        getCurrentTime() + '</span></div></div></div></div>'

                    $('#messageWrapper').append(new_message)

                    let div_image_append = document.createElement('div')
                    div_image_append.style.display = 'flex'
                    div_image_append.style.justifyContent = 'flex-end'

                    div_image_append.append(img)

                    $('#messageWrapper').append(div_image_append)
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

                })
            }

            /* Image zoom photo with modal */
            $(document).on('click', '.img-style-append', function() {
                console.log('MODAL ZOOM IMAGE');
                image_src = $(this).attr('src')
                $('#modal_image').attr('src', image_src)
                /* console.log(image_src) */
            })

        })
    </script>
@endpush
