<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RealtimeBroadcaster
{
    public function emitToUser(int $userId, string $event, array $payload = []): void
    {
        $url = rtrim((string) config('realtime.url', 'http://127.0.0.1:3001'), '/').'/emit';
        $secret = (string) config('realtime.secret', 'scoop-dev-realtime-secret');

        try {
            Http::timeout(2)
                ->withHeaders(['X-Realtime-Secret' => $secret])
                ->post($url, [
                    'secret' => $secret,
                    'user_id' => $userId,
                    'event' => $event,
                    'payload' => $payload,
                ]);
        } catch (\Throwable $e) {
            // Realtime is best-effort in local/dev — never break core flows
            Log::debug('Realtime emit failed', [
                'user_id' => $userId,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
