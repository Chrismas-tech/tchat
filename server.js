const app = require('express')();
const http = require('http').Server(app);
const io = require('socket.io')(http, {
    cors: { origin: "*" }
});
const Redis = require('ioredis');
const { sortedIndex } = require('lodash');
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

let users = [];
let groups = [];
let room_to_leave;

io.on('connection', socket => {

    socket.on('user_connected', user_id => {
        /* console.log(users); */

        users.push({ user_id: user_id, socket_id: socket.id })

        /* Update des statuts de sa propre page et celle des autres */
        io.emit('UserStatus', users)
    })

    socket.on('disconnect', () => {

        console.log("ROOM TO LEAVE : " + room_to_leave)
        socket.leave(room_to_leave)

        users.forEach((user, index) => {
            if (user.socket_id == socket.id) {
                users.splice(index, 1);

                /* On change le status de l'utilisateur */
                io.emit('UserStatus', users)
            }
        });
    })

    socket.on('joinGroup', function(data) {

        data['socket_id'] = socket.id

        /* Si le groupe existe */
        if (groups[data.group_id]) {

            let userExist = Check_User_Exist_in_Group(data.user_id, data.group_id, socket);

            /* Si l'utilisateur n'existe pas dans le groupe -> on push l'utilisateur et on le joint dans la room */
            if (!userExist) {

                groups[data.group_id].push(data)
                socket.join(data.room);
                /* 
                                console.log(socket.adapter.rooms); */
                room_to_leave = data.room;

                /* Si l'utilisateur existe dans le groupe -> on le joint dans la room */
            } else {
                socket.join(data.room);
                console.log(socket.adapter.rooms);
                room_to_leave = data.room;
            }

            /* Si le groupe n'existe pas --> on créé le groupe et on joint l'utilisateur dans la room */
        } else {
            groups[data.group_id] = [data];
            socket.join(data.room);

            console.log(socket.adapter.rooms);
            room_to_leave = data.room;
        }
    })
})


redis.on('message', (channel, message) => {

    message = JSON.parse(message)

    if (channel == 'private-channel') {

        /* console.log('PRIVATE CHANNEL'); */
        let data = message.data.data;
        let receiver_id = data.receiver_id;
        let event = message.event;

        users.forEach((user, index) => {
            if (user.user_id == receiver_id) {
                /*    console.log(user) */
                io.to(user.socket_id).emit(channel + ':' + event, data);
            }
        });
    }

    if (channel == 'group-channel') {

        console.log('GROUP CHANNEL');
        console.log(message);

        let data = message.data.data;

        if (data.type == 1) {

            /* 
            console.log('data.type == 1'); 
            */

            /* Astuce : on émet pour tout le groupe, mais du côté client on appendra pour tous les utilisateurs sauf l'expéditeur */

            io.to('group' + data.message_group_id).emit('groupMessage', data);

            console.log('BROADCAST DONE');
        }
    }
})

function Check_User_Exist_in_Group(user_id, group_id, socket) {

    let group = groups[group_id]

    /* Si groups n'est pas vide */
    if (groups.length > 0) {

        /* Dans Groups, y-a-t'il le user_id du socket, si oui on retourne true */
        for (let i = 0; i < group.length; i++) {

            if (group[i]['user_id'] == user_id) {

                /*           
                console.log('User ' + user_id + ' already exist in the group ! --> replace Socket'); 
                */

                group[i]['socket_id'] = socket.id

                /*               
                console.log('GROUP CHECK IF USER EXIST');
                console.log(groups); 
                */

                return true;
            }
        }
    }
    return false;
}