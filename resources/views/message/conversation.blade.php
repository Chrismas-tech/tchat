@extends('layouts.app')

@section('content')

    <div class="d-flex p-4 responsive-users">

        <div>
            {{-- <h5 class="bg-primary w-max-content text-white px-2 py-2 rounded">Friends List</h5> --}}

            <div class="users">
                

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

                            <a href="{{ route('message.conversation', $user->id) }}" class="text-decoration-none">

                                <li class="chat-user-list d-flex align-items-center @if ($user->id ==
                                    $friendInfo->id) active @endif">

                                    <div class="chat-image">
                                        <i class="fa fa-circle fa-sm user-status-icon" id="status-{{ $user->id }}"
                                            title="Away"></i>

                                        <div class="name-image mr-1">
                                            <img src="{{ route('profile_image_friends_serve', $user->id) }}" alt="avatar">
                                        </div>
                                        @php
                                            $user_name_full = $user->firstname . ' ' . $user->lastname . '';
                                        @endphp

                                    </div>

                                    <div class="chat-name m-auto {{ $user->id == $friendInfo->id ? 'text-white' : '' }}">
                                        {{ $user_name_full }} <span id="notif"></span>
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

        <div class="chat-section w-100 p-3 rounded ml-4">
            <div class="chat-header d-flex align-items-center">

                <div class="chat-header-name ml-1 font-bold">
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
                                    <div class="mt-2 mb-2 user-info d-flex align-items-center">

                                        <div class="image-chat">
                                            <img src="{{ route('profile_image_serve', Auth::id()) }}" alt="avatar">
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
                                    <div class="mt-2 mb-2 user-info d-flex align-items-center">

                                        <div class="image-chat">
                                            <img src="{{ route('profile_image_friends_serve', $friendInfo->id) }}"
                                                alt="avatar">
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

            <div class="chat-box">
                <div class="d-flex font-italic" id="writing">
                </div>
                <div class="chat-input-toolbar d-flex">

                    <form id="form_send_image">
                        @csrf
                        <div class="d-flex align-items-center">
                            <div title="Add File" class="d-flex align-items-center text-white bg-blue div-send-photos">
                                <label for="file" class="attach-photo">Select photo(s) to send</label>
                                <img class="img-attach" src="{{ asset('img/img-attach.png') }}" alt="">
                                <input type="file" name="file[]" id="file" accept="image/*" multiple>
                            </div>
                            <div class="d-none p-0 m-0 btn-image-send">
                                <button type="button" class="text-white image-send-btn rounded bg-success border-none">Send
                                    files</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="images-preview" class="d-flex align-items-center">
                </div>
            </div>

            <div id="ErrorTag" style="display:none">
                <div class="d-flex align-items-center">
                    <p class="text-danger font-italic ml-1 mb-1 px-1 rounded">
                        Html Tags are not allowed in your message ! <img src="{{ asset('img/cross.png') }}" alt="cross">
                    </p>
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

            $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
            $('.js-example-basic-single').select2();

            /* GLOBAL VARIABLES */
            /* GLOBAL VARIABLES */

            let $chatInput = $("#chatInput");
            let $chatBody = $(".chat-Body")

            let sender_id = '{{ Auth::id() }}'
            let sender_name = '{{ Auth::user()->firstname }}' + ' ' + '{{ Auth::user()->lastname }}'
            let avatar = '{{ Auth::user()->avatar }}'

            let avatar_friend = '{{ $friendInfo->avatar }}'
            let receiver_id = "{{ $friendInfo->id }}"
            let receiver_name = '{{ $friendInfo->firstname }}' + ' ' + '{{ $friendInfo->lastname }}'


            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

            socket.emit('user_connected', sender_id)

            socket.on('UserStatus', users => {

                console.log('USERS');
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
                }

            })

            /* On évite de passer à la ligne avec la touche Enter --> ne marche que sur keydown pas keypress */
            $chatInput.on('keydown', e => {
                if (e.which === 13) {
                    /* on évite un retour à la ligne */
                    e.preventDefault()
                }
            })

            $chatInput.on('keyup', e => {

                let message_html = $chatInput.html()
                let message = $chatInput.text()
                let length_message = message.length

                if (length_message > 0) {

                    /* Si le champ n'est pas vide, on émet vers le serveur */
                    socket.emit('is_writing', {
                        sender_id: sender_id,
                        sender_name: sender_name,
                        receiver_id: receiver_id,
                        receiver_name: receiver_name,
                    })

                } else {
                    /* Sinon on émet vers le serveur pour que l'autre enlève sa div writing */
                    socket.emit('remove_writing', {
                        sender_id: sender_id,
                        sender_name: sender_name,
                        receiver_id: receiver_id,
                        receiver_name: receiver_name,
                    })
                }

                /* JQuery function which -> which key was pressed */
                /* Si on tape Enter et si Shift n'est pas enfoncée */

                if (e.which === 13 && !e.shiftKey) {
                    /* Si il y a du texte dans le champ input alors --> on envoie un message au serveur */
                    if (length_message > 0) {

                        sendMessage(message_html);

                        socket.emit('remove_writing', {
                            sender_id: sender_id,
                            sender_name: sender_name,
                            receiver_id: receiver_id,
                            receiver_name: receiver_name,
                        })

                        $('#audio_arrow_mess')[0].play()
                        $chatInput.empty();

                    } else {
                        e.preventDefault()
                    }
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


            function appendMessageToSender(message) {
                /* console.log('SENDER');
                console.log(message); */

                let $user_full_name = '{{ $user_full_name }}';

                let new_message =
                    '<div class="d-flex justify-content-end"><div><div class="mt-2 mb-2 user-info d-flex align-items-center"><div class="image-chat"><img src="{{ route('profile_image_serve', Auth::id()) }}" alt="avatar"></div><div class="chat-name ml-1 font-weight-bold">' +
                    $user_full_name + ' ' +
                    '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                    getCurrentTime() + '</span></div></div><div class="message-text">' + message +
                    '</div></div></div>';


                $('#messageWrapper').append(new_message);
                $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
            }

            function appendMessageToReceiver(message) {
                /*                 
                console.log('APPEND RECEIVER');
                console.log(message) 
                */

                $message_receiver_id = parseInt(message.receiver_id)

                if (message.sender_id == receiver_id) {
                    /*         console.log(true); */
                    let $friend_full_name = '{{ $friend_full_name }}';

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="mt-2 mb-2 user-info d-flex align-items-center"><div class="image-chat"><img src="{{ route('profile_image_friends_serve', $friendInfo->id) }}" alt="avatar"></div><div class="chat-name ml-1 font-weight-bold">' +
                        $friend_full_name + ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                        getCurrentTime() + '</span></div></div><div class="message-text">' + message.content +
                        '</div></div></div>';

                    $('#messageWrapper').append(new_message);
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
                }
            }

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

                console.log(data);

                /* Si l'action reçue est bien dédiée à la conversation en cours */
                /* L'id de celui qui a envoyé le message doit être égal à celui du friend info */
                if (data.sender_id == receiver_id) {

                    let attribute = 'writer' + '-' + data.sender_id + '-' + data.sender_name;
                    let find_attribute = document.getElementById(attribute);

                    if (!find_attribute) {

                        let div = document.createElement('div');
                        let message = data.sender_name + ' is writing '

                        $(div).attr("id", attribute)
                        $(div).text(message)
                        $(div).addClass('is-writing')

                        $('#writing').append(div);
                        let gif = '<img class="writing-gif" src="{{ asset('img/writing.gif') }}"/>';
                        $(div).append(gif)
                    }
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

                }

            })


            socket.on('remove_writing', (data) => {

                if (data.sender_id == receiver_id) {

                    let attribute = 'writer' + '-' + data.sender_id + '-' + data.sender_name;

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

                }

            })

            /*---------------------------------------------------------------------------------------*/
            /*---------------------------------------------------------------------------------------*/

            /* SEND IMAGE SERVICES */
            /* SEND IMAGE SERVICES */

            /* On mémorise le format base64 des images previews pour socket io */
            let images = [];

            $('#file').change(function() {

                /* Dés que l'on change de fichiers prêts à l'upload, on réinitialise le contenu de la div */
                let div_preview = document.getElementById('images-preview')
                div_preview.innerHTML = "";

                /* console.log(this.files); */

                /* Si des fichiers sont prêts à l'upload */
                if (this.files) {

                    images = [];

                    for (const [key, file] of Object.entries(this.files)) {

                        let extension_split = file.name
                        let extension_file = extension_split.substr(-3);

                        if (extension_file == "png" || extension_file == "jpg" || extension_file ==
                            "jpeg" || extension_file == "gif" || extension_file == "tiff") {

                            console.log('EXTENSION OK');

                            let div_image_preview = document.createElement('div')
                            div_image_preview.classList.add('div-image-preview')

                            let img_preview = document.createElement('img')
                            img_preview.classList.add('img-preview')

                            let reader = new FileReader()

                            reader.onload = function(e) {
                                data = {
                                    base64: e.target.result,
                                    sender_id: sender_id,
                                    sender_name: sender_name,
                                    receiver_id: receiver_id,
                                    receiver_name: receiver_name,
                                }

                                img_preview.src = e.target.result;
                                images.push(data)
                            }

                            reader.readAsDataURL(file)

                            div_preview.append(div_image_preview)
                            div_image_preview.append(img_preview)
                            /* div_image_preview.append(cross_image) */

                            /* On fait apparaître le bouton Send */
                            $('.btn-image-send').removeClass('d-none');

                        } else {
                            alert('Please select only images !')
                        }

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
                /* console.log('SEND IMAGES');
                console.log(images); */

                $.ajax({
                    url: "{{ route('message.send-image') }}", // La ressource ciblée
                    method: 'POST', // Le type de la requête HTTP
                    dataType: 'JSON', // On définit le type des données retourné par le serveur (évite d'utiliser JSON.parse pour la réponse)

                    data: {
                        images: images,
                        _token: "{{ csrf_token() }}",
                    },

                    xhr: function() {
                        let div_image_preview = $('#images-preview');

                        let xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(evt) {

                            if (evt.lengthComputable) {


                                let percentComplete = (evt.loaded / evt.total) * 100;
                                //Do something with upload progress here

                            }

                        }, false);

                        return xhr;
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

            function appendImageToSender(images) {
                /* console.log("SENDER IMAGE RECEIVED");
                console.log(images) */

                images.forEach(data => {

                    let img = document.createElement('img')
                    img.src = data.base64
                    img.className = "img-style-append";
                    img.setAttribute('data-toggle', 'modal')
                    img.setAttribute('data-target', '#Modal_zoom_image')

                    let $user_full_name = '{{ $user_full_name }}'

                    let new_message =
                        '<div class="d-flex justify-content-end"><div><div class="user-info d-flex align-items-center"><div class="image-chat"><img src="{{ route('profile_image_serve', Auth::id()) }}" alt="avatar"></div><div class="chat-name ml-1 font-weight-bold">' +
                        $user_full_name +
                        ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() +
                        '">' +
                        getCurrentTime() + '</span></div></div></div></div>';

                    $('#messageWrapper').append(new_message)

                    let div_image_append = document.createElement('div')
                    div_image_append.style.display = 'flex'
                    div_image_append.style.justifyContent = 'flex-end'

                    div_image_append.append(img)

                    $('#messageWrapper').append(div_image_append)
                    $("#chatBody").scrollTop($("#chatBody")[0]
                        .scrollHeight);

                })
            }

            function appendImageToReceiver(datas) {

                /* console.log("DATA IMAGE RECEIVED");
                console.log(datas); */

                if (datas.sender_id == "{{ $friendInfo->id }}") {

                    /* Create and Style of image */
                    let img = document.createElement('img')
                    img.src = datas.data
                    img.className = "img-style-append"
                    img.setAttribute('data-toggle', 'modal')
                    img.setAttribute('data-target', '#Modal_zoom_image')

                    /* New message */
                    let $friend_full_name = datas.sender_name

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="user-info mt-2 mb-2 d-flex align-items-center"><div class="image-chat"><img src="{{ route('profile_image_friends_serve', $friendInfo->id) }}" alt="avatar"></div><div class="chat-name ml-1 font-weight-bold">' +
                        $friend_full_name +
                        ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() +
                        '">' +
                        getCurrentTime() + '</span></div></div></div></div>';

                    let div_image_append = document.createElement('div')
                    div_image_append.style.display = 'flex'
                    div_image_append.style.justifyContent = 'flex-start'
                    div_image_append.append(img)

                    $('#messageWrapper').append(new_message)
                    $('#messageWrapper').append(div_image_append)

                    $('#audio_hp')[0].play()
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
                }
            }

            /* Image zoom photo with modal */
            $(document).on('click', '.img-style-append', function() {
                console.log('MODAL ZOOM IMAGE');
                image_src = $(this).attr('src')
                $('#modal_image').attr('src', image_src)
                /* console.log(image_src) */
            })

            /* SEARCH USER AJAX */
            /* SEARCH USER AJAX */
            /* SEARCH USER AJAX */

            let $input_search = $('#input-search-user')

            $input_search.on('keyup', e => {

                let message = $input_search.val()
                let length_message = message.length

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
