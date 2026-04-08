<?php

namespace App\Services;

use App\Models\User;
use App\Models\VideoCallSession;
use Illuminate\Support\Str;
use RuntimeException;

class LiveKitVideoService
{
    public function isConfigured(): bool
    {
        return filled(config('services.livekit.url'))
            && filled(config('services.livekit.api_key'))
            && filled(config('services.livekit.api_secret'));
    }

    public function tokenTtlSeconds(): int
    {
        return max(300, (int) config('services.livekit.token_ttl_seconds', 7200));
    }

    public function generateRoomName(string $mode = 'friend'): string
    {
        return strtolower(sprintf(
            'speak-%s-%s',
            Str::slug($mode) ?: 'call',
            Str::lower(Str::random(18))
        ));
    }

    public function buildConnectionPayload(VideoCallSession $session, User $user): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('LiveKit video call is not configured yet.');
        }

        $expiresAt = now()->addSeconds($this->tokenTtlSeconds());
        $identity = sprintf('user-%d-session-%d', $user->id, $session->id);

        $claims = [
            'iss' => (string) config('services.livekit.api_key'),
            'sub' => $identity,
            'nbf' => now()->subMinute()->timestamp,
            'exp' => $expiresAt->timestamp,
            'name' => (string) $user->name,
            'metadata' => json_encode([
                'user_id' => $user->id,
                'session_id' => $session->id,
            ], JSON_UNESCAPED_SLASHES),
            'video' => [
                'roomJoin' => true,
                'room' => (string) $session->daily_room_name,
                'canPublish' => true,
                'canSubscribe' => true,
                'canPublishData' => true,
            ],
        ];

        return [
            'url' => (string) config('services.livekit.url'),
            'room_name' => (string) $session->daily_room_name,
            'identity' => $identity,
            'token' => $this->encodeJwt($claims, (string) config('services.livekit.api_secret')),
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    protected function encodeJwt(array $claims, string $secret): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
