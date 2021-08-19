@extends('layouts.app')

@section('content')

    <div class="d-flex p-4 responsive-users">

        <div class="mr-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="w-max-content text-white bg-person-invited rounded px-2 py-2 m-0">
                    <a class="text-white" href="{{ '/home' }}">Return Back <img
                            src="{{ asset('img/Arrow-left.png') }}" alt="img-arrow" class="arrow-back"></a>
                </h5>
            </div>
            <hr>
            <h5 class="w-max-content text-white px-2 py-2 rounded add-user-group" data-toggle="modal"
                data-target="#Modal_add_to_group">Add a group <i class="class text-secondary fa fa-plus ml-1"></i>
            </h5>

            <ul class="list-group list-chat-item mt-3">
                @if ($groups->count())

                    @foreach ($groups as $group)

                        <!-- Si le créateur du groupe est l'utilisateur connecté alors on affiche un lien vers la page du groupe -->

                        @if ($group->user_id == Auth::id())
                            <a href="{{ route('message-groups.show', $group->id) }}">
                                <li class="chat-group-list {{ $group->id == $currentGroup->id ? 'active-group' : '' }}">
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

        <div class="w-100 p-4 chat-section rounded">
            <div class="chat-group-header">

                <div class="d-flex align-items-center">
                    <div class="bg-admin-group mr-2 rounded px-2 py-2 font-weight-bold">
                        <img class="icon-profile" src="{{ asset('img/icon-admin.png') }}" alt="icon-profile">Créateur du
                        groupe : {{ $currentGroup->name }}
                    </div>

                    <div class="d-flex align-items-center bg-blue text-white px-2 py-2 rounded">
                        <div class="chat-group-image">
                            <i class="fa fa-circle fa-xs user-group-status-icon status-{{ $currentGroup->user->id }}"
                                title="Away"></i>

                            <div class="name-group-image">
                                <img src="{{ route('profile_image_friends_serve', $currentGroup->user->id) }}"
                                    alt="avatar">
                            </div>

                        </div>

                        <div class="chat-group-name font-bold">
                            @php
                                $user_name_full = $currentGroup->user->firstname . ' ' . $currentGroup->user->lastname . '';
                            @endphp
                            {{ $currentGroup->user->firstname }} {{ $currentGroup->user->lastname }}
                        </div>
                    </div>

                    <div>
                        <img class="icon-audio" src="{{ asset('img/hp-on.png') }}" alt="icon-audio"
                            title="on/off sounds effects">
                    </div>

                </div>

                <div
                    class="d-flex flex-wrap align-items-center text-white mt-2 border border-2 rounded px-2 py-1 bg-person-invited">
                    <div class="mr-1">
                        <img class="icon-profile" src="{{ asset('img/icon-profile.png') }}" alt="icon-profile">Persons
                        invited :
                    </div>
                    {{-- {{dd($members_of_this_group)}} --}}
                    @foreach ($members_of_this_group as $member)

                        <div class="d-flex align-items-center mr-2 py-1">
                            <div class="chat-image">

                                <i class="fa fa-circle fa-xs user-group-status-icon status-{{ $member->user->id }}"
                                    title="Away"></i>

                                <div class="name-group-image">
                                    <img src="{{ route('profile_image_friends_serve', $member->user->id) }}"
                                        alt="avatar">
                                </div>

                            </div>

                            <div class="chat-group-name font-bold">
                                @php
                                    $member_of_group = $member->user->firstname . ' ' . $member->user->lastname . '';
                                @endphp
                                {{ $member->user->firstname }} {{ $member->user->lastname }}
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="chat-body" id="chatBody">
                <div class="message-listing" id="messageWrapper">

                    <!-- Pour tous les messages du groupe-->
                    @foreach ($messages_of_this_group as $message)

                        <!-- Si le message appartient au groupe courant et que le sender est l'utilisateur connecté, alors on affiche à droite -->

                        @if ($message->message_group_id == $currentGroup->id and $message->sender_id == Auth::id())


                            <div class="d-flex justify-content-end mt-3 mb-3">
                                <div>
                                    <div class="mt-2 mb-2 user-info d-flex align-items-center">

                                        <div class="image-chat">
                                            <img src="{{ route('profile_image_serve', Auth::id()) }}" alt="avatar">
                                        </div>

                                        <div class="chat-name ml-1 font-weight-bold">{{ Auth::user()->firstname }}
                                            {{ Auth::user()->lastname }}
                                            <span class="small time text-secondary"
                                                title="{{ $message->message->created_at }}">{{ created_at_format_date($message->message->created_at) }}</span>
                                        </div>
                                    </div>
                                    @if ($message->message->type == 1)
                                        <div class="message-text">
                                            {{ string_to_html_plus_clean_div($message->message->message) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($message->message->type == 2)
                                <div class="d-flex justify-content-end">
                                    <img src="{{ $message->message->message }}" alt="" class="img-style-append"
                                        data-toggle="modal" data-target="#Modal_zoom_image">
                                </div>
                            @endif

                            <!-- Sinon on affiche à gauche -->
                        @else

                            <div class="d-flex justify-content-start mt-3 mb-3">
                                <div>
                                    <div class="mt-2 mb-2 user-info d-flex align-items-center">

                                        <div class="image-chat">
                                            <img src="{{ route('profile_image_friends_serve', $message->sender_id) }}"
                                                alt="avatar">
                                        </div>

                                        @php
                                            $friend = App\Models\User::find($message->sender_id);
                                            $friend_full_name = $friend->firstname . ' ' . $friend->lastname;
                                        @endphp

                                        <div class="chat-name ml-1 font-weight-bold">
                                            {{ $friend->firstname }} {{ $friend->lastname }}
                                            <span class="small time text-secondary"
                                                title="{{ $friend->created_at }}">{{ created_at_format_date($friend->created_at) }}</span>
                                        </div>
                                    </div>
                                    @if ($message->message->type == 1)
                                        <div class="message-text">
                                            {{ string_to_html_plus_clean_div($message->message->message) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($message->message->type == 2)
                                <div class="d-flex justify-content-start">
                                    <img src="{{ $message->message->message }}" alt="" class="img-style-append"
                                        data-toggle="modal" data-target="#Modal_zoom_image">
                                </div>
                            @endif

                        @endif
                    @endforeach
                </div>
            </div>

            <div id="ErrorTag" style="display:none">
                <div class="d-flex align-items-center">
                    <p class="text-danger font-italic ml-1 mb-1 px-1 rounded">
                        Html Tags are not allowed in your message ! <img src="{{ asset('img/cross.png') }}" alt="cross">
                    </p>
                </div>
            </div>


            <div class="chat-box">
                <div class="d-flex font-italic" id="writing">
                </div>
                <div class="chat-input-toolbar d-flex">
                    {{-- <button id="bold" title="Bold" class="text-white bg-blue">
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
                    | --}}
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

            $('.js-example-basic-single').select2()

            let $chatInput = $("#chatInput")
            let $chatInputTollbar = $('.chat-input-toolbar')
            let $chatBody = $(".chat-Body")

            let sender_id = '{{ Auth::id() }}'
            let sender_name = '{{ Auth::user()->firstname }}' + ' ' + '{{ Auth::user()->lastname }}'
            let avatar = '{{ Auth::user()->avatar }}'

            console.log(avatar);

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

                    if ($('.status-' + el.user_id)) {
                        $('.status-' + el.user_id).addClass('online').attr('title', 'Online')
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

            /* On évite de passer à la ligne avec la touche Enter --> ne marche que sur keydown pas keypress*/
            $chatInput.on('keydown ', e => {
                if (e.which === 13) {
                    console.log('ENTER');
                    /* on évite un retour à la ligne */
                    e.preventDefault()
                }
            })

            $chatInput.on('keyup ', e => {

                let message_html = $chatInput.html().trim()
                let message = $chatInput.text();
                let length_message = message.length

                /* JQuery function which -> which key was pressed */
                /* Si on tape Enter et si Shift n'est pas enfoncée */

                if (e.which === 13 && !e.shiftKey) {

                    /* Si il y a du texte dans le champ input alors --> on envoie un message au serveur */
                    if (length_message > 0) {

                        /* on évite un retour à la ligne */
                        e.preventDefault()
                        sendMessage(message_html);

                        socket.emit('remove_writing_group', {
                            sender_id: sender_id,
                            sender_name: sender_name
                        })
                        $('#audio_arrow_mess')[0].play()
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

                $.ajax({
                    url: "{{ route('message.send-group-message') }}", // La ressource ciblée
                    method: 'POST', // Le type de la requête HTTP
                    dataType: 'JSON', // On définit le type des données retourné par le serveur (évite d'utiliser JSON.parse pour la réponse)

                    data: {
                        message: message,
                        _token: "{{ csrf_token() }}",
                        group_id: group_id,
                        group_name: group_name,
                        avatar: avatar,
                    },

                    success: function(response, status) {
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


            $('#send-btn').on('click', e => {

                let message_text = $chatInput.text().trim()
                let message_html = $chatInput.html().trim()
                let length_message = message_html.length

                if (message_text == 'Write your message here...') {
                    return
                }

                if (length_message > 0) {
                    e.preventDefault()
                    sendMessage(message_html);

                    socket.emit('remove_writing_group', {
                        sender_id: sender_id,
                        sender_name: sender_name
                    })

                    $('#audio_arrow_mess')[0].play()
                    $chatInput.empty();
                }

            })

            socket.on("private-channel:App\\Events\\PrivateGroupEvent", function(message) {
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

            /* END AUDIO */
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/


            /* FUNCTIONS APPEND*/
            /* -------------------------------------------------------------------*/
            /* -------------------------------------------------------------------*/


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

                console.log('APPEND RECEIVER');
                console.log(message)

                $message_receiver_id = parseInt(message.receiver_id)

                if (message.sender_id != sender_id) {
                    /* console.log(true); */
                    let name = message.sender_name

                    let url = "{{ route('profile_image_friends_serve', '') }}" + "/" + message.sender_id;

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="mt-2 mb-2 user-info d-flex align-items-center"><div class="image-chat"><img src="' +
                        url + '" alt="avatar"></div><div class="chat-name ml-1 font-weight-bold">' +
                        message.sender_name + ' ' +
                        '<span class="small time text-secondary" title="' + getCurrent_Date_and_Time() + '">' +
                        getCurrentTime() + '</span></div></div><div class="message-text">' + message.content +
                        '</div></div></div>';

                    $('#messageWrapper').append(new_message);
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
                }
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
                    $(div).addClass('mr-1')

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
                    }, 100);

                }

            })

            /* SEND IMAGE SERVICES */
            /* SEND IMAGE SERVICES */

            /* On mémorise le format base64 des images previews pour socket io */
            let images = [];

            $('#file').change(function() {

                /* Dés que l'on change de fichiers prêts à l'upload, on réinitialise le contenu de la div */
                let div_preview = document.getElementById('images-preview')
                div_preview.innerHTML = "";

                console.log(this.files);

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
                                /* console.log(e.target.result); */
                                data = {
                                    base64: e.target.result,
                                    sender_id: sender_id,
                                    sender_name: sender_name,
                                    room: "group" + group_id,
                                    group_id: group_id,
                                    avatar: avatar
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
                console.log('SEND IMAGES');
                console.log(images);

                $.ajax({
                    url: "{{ route('message.send-group-image') }}", // La ressource ciblée
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

            socket.on('groupImage', (image) => {
                appendImageToReceiver(image)
            })

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
                    $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

                })
            }

            function appendImageToReceiver(datas) {

                if (datas.sender_id != sender_id) {

                    console.log("DATA IMAGE RECEIVED");
                    console.log(datas);

                    let img = document.createElement('img')
                    img.src = datas.data
                    img.className = "img-style-append"
                    img.setAttribute('data-toggle', 'modal')
                    img.setAttribute('data-target', '#Modal_zoom_image')

                    let sender_id = datas.sender_id
                    let name = datas.sender_name
                    let url = "{{ route('profile_image_friends_serve', '') }}" + "/" + sender_id;

                    let new_message =
                        '<div class="d-flex justify-content-start"><div><div class="user-info mt-2 mb-2 d-flex align-items-center"><div class="image-chat"><img src="' +
                        url + '" alt="avatar"></div><div class="chat-name ml-1 font-weight-bold">' +
                        name +
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
