import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const server = createServer(app);
const io = new Server(server, {
    cors: {
        origin: "http://localhost:8000",
        methods: ["GET", "POST"]
    }
});

const rooms = new Map();

io.on('connection', (socket) => {
    const roomId = socket.handshake.query.roomId;
    const userId = socket.handshake.query.userId;
    const userName = socket.handshake.query.userName;
    
    // Join room
    socket.join(roomId);
    if (!rooms.has(roomId)) {
        rooms.set(roomId, new Set());
    }
    rooms.get(roomId).add(userId);
    
    // Notify others in room
    socket.to(roomId).emit('user-joined', { userId, userName });
    
    // Handle WebRTC signaling
    socket.on('offer', (data) => {
        socket.to(roomId).emit('offer', {
            ...data,
            userId,
            userName
        });
    });
    
    socket.on('answer', (data) => {
        socket.to(roomId).emit('answer', {
            ...data,
            userId
        });
    });
    
    socket.on('ice-candidate', (data) => {
        socket.to(roomId).emit('ice-candidate', {
            ...data,
            userId
        });
    });
    
    // Handle chat messages
    socket.on('chat-message', (data) => {
        socket.to(roomId).emit('chat-message', {
            sender: userName,
            message: data.message
        });
    });
    
    // Handle disconnection
    socket.on('disconnect', () => {
        if (rooms.has(roomId)) {
            rooms.get(roomId).delete(userId);
            if (rooms.get(roomId).size === 0) {
                rooms.delete(roomId);
            }
        }
        socket.to(roomId).emit('user-left', userId);
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
});