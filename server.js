const fs = require('fs');

const httpServer = require("https").createServer({
    key: fs.readFileSync('./ssl/privkey.pem'),
    cert: fs.readFileSync('./ssl/cert.pem')
});

const io = require('socket.io')(httpServer, {
    cors: { origin: "*" }
});

const Redis = require('ioredis');
const redis = new Redis();

httpServer.listen(3000)

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

    /* GROUP */
    /* GROUP */
    socket.on('is_writing_group', (data) => {
        socket.broadcast.emit('is_writing_group', { user_id: data.sender_id, user_name: data.sender_name })
    })

    socket.on('remove_writing_group', (data) => {

        console.log('REMOVE WRITING EVENT');

        socket.broadcast.emit('remove_writing_group', { user_id: data.sender_id, user_name: data.sender_name })
    })

    /* USER TO USER */
    /* USER TO USER */

    socket.on('is_writing', (data) => {
        users.forEach(user => {
            if (data.receiver_id == user.user_id) {
                console.log('BROADCAST send writing');
                console.log(user.socket_id);
                io.to(user.socket_id).emit('is_writing', { user_id: data.receiver_id, user_name: data.receiver_name })
            }
        });
    })

    socket.on('remove_writing', (data) => {

        console.log('REMOVE WRITING EVENT');

        users.forEach(user => {
            if (data.receiver_id == user.user_id) {
                /*  console.log('send remove'); */
                io.to(user.socket_id).emit('remove_writing', { user_id: data.receiver_id, user_name: data.receiver_name })
            }
        });
    })

    socket.on('disconnect', () => {

        /*         console.log("ROOM TO LEAVE : " + room_to_leave) */
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

                /* console.log(socket.adapter.rooms); */
                groups[data.group_id].push(data)
                socket.join(data.room);
                room_to_leave = data.room;

                /* Si l'utilisateur existe dans le groupe -> on le joint dans la room */
            } else {

                /* console.log(socket.adapter.rooms); */
                socket.join(data.room);
                room_to_leave = data.room;

            }

            /* Si le groupe n'existe pas --> on créé le groupe et on joint l'utilisateur dans la room */
        } else {
            /* console.log(socket.adapter.rooms); */

            groups[data.group_id] = [data];
            socket.join(data.room);
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