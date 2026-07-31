import express from 'express'
import cors from 'cors'
import { createServer } from 'http'
import { Server } from 'socket.io'

const PORT = Number(process.env.REALTIME_PORT || 3001)
const INTERNAL_SECRET = process.env.REALTIME_SECRET || 'scoop-dev-realtime-secret'

const app = express()
app.use(cors())
app.use(express.json({ limit: '1mb' }))

const httpServer = createServer(app)
const io = new Server(httpServer, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
})

io.on('connection', (socket) => {
    const userId = Number(socket.handshake.auth?.userId || socket.handshake.query?.userId || 0)
    if (userId > 0) {
        socket.join(`user:${userId}`)
        socket.emit('connected', { userId })
    }

    socket.on('join', (payload = {}) => {
        const id = Number(payload.userId || 0)
        if (id > 0) socket.join(`user:${id}`)
    })
})

app.get('/health', (_req, res) => {
    res.json({ ok: true, clients: io.engine.clientsCount })
})

/**
 * Laravel posts here when a notification is created.
 * POST /emit { secret, user_id, event, payload }
 */
app.post('/emit', (req, res) => {
    const secret = req.headers['x-realtime-secret'] || req.body?.secret
    if (secret !== INTERNAL_SECRET) {
        return res.status(401).json({ message: 'Unauthorized' })
    }

    const userId = Number(req.body?.user_id || 0)
    const event = String(req.body?.event || 'notification:new')
    const payload = req.body?.payload || {}

    if (userId > 0) {
        io.to(`user:${userId}`).emit(event, payload)
    } else {
        io.emit(event, payload)
    }

    return res.json({ ok: true })
})

httpServer.listen(PORT, () => {
    console.log(`[realtime] Socket.IO listening on http://localhost:${PORT}`)
})
