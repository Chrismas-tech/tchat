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

    /*     console.log(channel);
        console.log(message); */

    message = JSON.parse(message)
        /*     console.log(message.data); */

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
        /*   console.log('JoinGroup'); */
        data['socket_id'] = socket.id

        console.log('DATA :');
        console.log(data);
        console.log('-------------------------------------------');
        console.log('-------------------------------------------');

        console.log('Groups 1st :');
        console.log(groups);
        console.log('-------------------------------------------');
        console.log('-------------------------------------------');

        if (groups[data.group_id]) {
            console.log('GROUP EXIST');
            let userExist = checkIfUserExistInGroup(data.user_id, data.group_id);

            if (userExist) {
                console.log('User Exist in Group');
            }

        } else {
            console.log('NEW GROUP');
            groups[data.group_id] = data;
        }

        console.log('Groups 2nd :');
        console.log(groups);
        console.log('-------------------------------------------');
        console.log('-------------------------------------------');
    })
})

function checkIfUserExistInGroup(user_id, group_id) {
    let group = groups[group_id]

    if (groups.length > 0) {
        /* Dans Groups, y-a-t'il le user_id du socket, si oui on retourne true */
        for (let i = 0; i < group.length; i++) {
            if (group[i]['user_id'] == user_id) {
                return true;
                break;
            }
        }
    }
}