<?php

namespace App\Services;

use App\Models\LiveClass;
use App\Models\User;

class LiveKitClassroomProvider implements LiveClassroomProvider
{
    public function name(): string
    {
        return 'livekit';
    }

    public function isConfigured(): bool
    {
        return filled(config('live-classroom.production.api_url'))
            && filled(config('live-classroom.production.api_key'))
            && filled(config('live-classroom.production.api_secret'));
    }

    public function issueJoinToken(LiveClass $liveClass, User $user): ?string
    {
        return $this->issueVideoCallToken(
            'live-class-' . $liveClass->id,
            $user,
            $user->isTeacher() && $liveClass->teacher_id === $user->id,
        );
    }

    public function issueAudioCallToken(string $roomName, User $user, bool $isRoomAdmin = false, bool $allowVideo = false): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $header = $this->encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = $this->encode([
            'iss' => config('live-classroom.production.api_key'),
            'sub' => (string) $user->id,
            'name' => $user->name,
            'nbf' => now()->timestamp,
            'exp' => now()->addHours(2)->timestamp,
            'video' => [
                'room' => $roomName,
                'roomJoin' => true,
                'canPublish' => true,
                'canSubscribe' => true,
                'canPublishData' => true,
                'roomAdmin' => $isRoomAdmin,
                'canPublishVideo' => $allowVideo,
                'canPublishAudio' => true,
                'canPublishScreen' => $allowVideo,
            ],
        ]);
        $signature = hash_hmac('sha256', $header . '.' . $payload, config('live-classroom.production.api_secret'), true);

        return $header . '.' . $payload . '.' . $this->base64Url($signature);
    }

    public function issueVideoCallToken(string $roomName, User $user, bool $isRoomAdmin = false): ?string
    {
        return $this->issueAudioCallToken($roomName, $user, $isRoomAdmin, true);
    }

    private function encode(array $value): string
    {
        return $this->base64Url(json_encode($value, JSON_UNESCAPED_SLASHES));
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}