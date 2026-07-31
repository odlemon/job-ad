<?php

return [
    // Socket.IO bridge used for live notifications (local/dev)
    'url' => env('REALTIME_URL', 'http://127.0.0.1:3001'),
    'secret' => env('REALTIME_SECRET', 'scoop-dev-realtime-secret'),
];
