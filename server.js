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

var users = [];

redis.subscribe('private-channel', function() {
    console.log('Subscribed to private channel')
})

redis.on('message', (channel, message) => {
    message = JSON.parse(message)
    console.log(message.data);

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


io.on('connection', socket => {

    socket.on('user_connected', user_id => {
        users.push({ user_id: user_id, socket_id: socket.id })

        /* Update des statuts de sa propre page et celle des autres */
        io.emit('UserStatus', users)
    })


    socket.on('disconnect', () => {

        users.forEach((user, index) => {
            if (user.socket_id == socket.id) {
                /*  console.log('Match') */
                /* On supprime l'utilisateur du tableau */
                users.splice(index, 1);

                /* On change le status de l'utilisateur */
                io.emit('UserStatus', users)
            }
        });
    })
})