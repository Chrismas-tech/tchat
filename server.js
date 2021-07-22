const app = require('express')();
const http = require('http').Server(app);
const io = require('socket.io')(http, {
    cors: { origin: "*" }
});

http.listen(3000, function() {
    console.log('Listening to port 3000');
})

var users = [];

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

        /* Update des statuts de sa propre page et celle des autres */

        /*         console.log("USERS DISCONNECTION");
                console.log(users);
                console.log("---------------------------------------");
                console.log("---------------------------------------");
                console.log("---------------------------------------");
                console.log("---------------------------------------"); */
    })
})