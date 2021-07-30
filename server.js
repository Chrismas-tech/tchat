const app = require('express')();
const http = require('http').Server(app);
const io = require('socket.io')(http, {
    cors: { origin: "*" }
});
const Redis = require('ioredis');
const redis = new Redis();

http.listen(3000, function() {
    console.log('Listening to port 3000');
});


redis.subscribe('private-channel', function() {
    console.log('Subscribed to private channel')
})

redis.subscribe('group-channel', function() {
    console.log('Subscribed to group channel')
})

redis.on('message', (channel, message) => {

    /*     
    console.log(channel);
    console.log(message);
    */

    message = JSON.parse(message)
        /*     
        console.log(message.data);
        */

    if (channel == 'private-channel') {
        /* console.log('PRIVATE CHANNEL'); */
        let data = message.data.data;
        let receiver_id = data.receiver_id;
        let event = message.event;

        /* console.log(message.data.data); */
        /* console.log(users); */

        users.forEach((user, index) => {
            if (user.user_id == receiver_id) {
                /*    console.log(user) */
                io.to(user.socket_id).emit(channel + ':' + event, data);
            }
        });
    }

    if (channel == 'group-channel') {

        let data = message.data.data;
        console.log('DATA');
        console.log(data);

        if (data.type == 2) {
            let socket_id = getSocketIdOfUserInGroup(data.sender_id, data.group_id);
            let socket = io.sockets.connected[socket_id];
            socket.broadcast.to('group' + data.group_id).emit('groupMessage', data);
        }
    }
})

let users = [];
let groups = [];

io.on('connection', socket => {

    socket.on('user_connected', user_id => {
        /* console.log(users); */
        users.push({ user_id: user_id, socket_id: socket.id })

        /* Update des statuts de sa propre page et celle des autres */
        io.emit('UserStatus', users)
    })


    socket.on('disconnect', () => {

        users.forEach((user, index) => {
            if (user.socket_id == socket.id) {
                users.splice(index, 1);

                /* On change le status de l'utilisateur */
                io.emit('UserStatus', users)
            }
        });
    })

    socket.on('joinGroup', function(data) {

        /*         
        console.log('-----------------------------------------------------------------------------');
        console.log('-----------------------------------------------------------------------------');
        console.log('1) STEP ---> JOINING GROUP BY LOADING PAGE'); 
        */

        data['socket_id'] = socket.id

        /*  console.log('DATA SENT BY JOINING GROUP:'); */

        if (groups[data.group_id]) {
            console.log('-----------------------------------------------------------------------------');
            console.log('-----------------------------------------------------------------------------');
            console.log('GROUP EXIST');

            let userExist = checkIfUserExistInGroup(data.user_id, data.group_id);

            /* If user doesn't exist in group */
            if (!userExist) {
                console.log('User doesn\'t exist in Group -> Creating One entry');

                groups[data.group_id].push(data)
                socket.join(data.room);

                console.log('-----------------------------------------------------------------------------');
                console.log('-----------------------------------------------------------------------------');
                console.log(groups);

            } else {
                console.log('User already exist');

            }

        } else {
            console.log('-----------------------------------------------------------------------------');
            console.log('-----------------------------------------------------------------------------');
            console.log('GROUP DOES NOT EXIST ---> CREATING ONE');

            groups[data.group_id] = [data];
            socket.join(data.room);
            console.log('-----------------------------------------------------------------------------');
            console.log('-----------------------------------------------------------------------------');
            console.log('GROUP CREATED');

        }
    })
})

function checkIfUserExistInGroup(user_id, group_id) {
    let group = groups[group_id]

    console.log('-----------------------------------------------------------------------------');
    console.log('-----------------------------------------------------------------------------');
    console.log('GROUP CHECK USER EXIST');
    console.log(group);

    /* Si groups n'est pas vide */
    if (groups.length > 0) {

        /* Dans Groups, y-a-t'il le user_id du socket, si oui on retourne true */
        for (let i = 0; i < group.length; i++) {

            console.log('-----------------------------------------------------------------------------');
            console.log('-----------------------------------------------------------------------------');
            console.log('USER_ID TO CHECK : ' + user_id);
            console.log('FOR THE CURRENT GROUP, DOEST USER_ID EXIST ? : ' + group[i]['user_id']);
            console.log('-----------------------------------------------------------------------------');
            console.log('-----------------------------------------------------------------------------');

            if (group[i]['user_id'] == user_id) {
                return true;
            } else {
                return false;
            }

        }

    }
}

function getSocketIdOfUserInGroup(user_id, group_id) {
    let group = groups[group_id]

    /* Si groups n'est pas vide */
    if (groups.length > 0) {

        /* Dans Groups, y-a-t'il le user_id du socket, si oui on retourne true */
        for (let i = 0; i < group.length; i++) {

            if (group[i]['user_id'] == user_id) {
                return group[i]['socket_id'];
            }

        }

    }
}