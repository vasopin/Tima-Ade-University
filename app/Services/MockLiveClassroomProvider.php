<?php

namespace App\Services;

use App\Models\LiveClass;
use App\Models\User;

class MockLiveClassroomProvider implements LiveClassroomProvider
{
    public function name(): string
    {
        return 'mock';
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function issueJoinToken(LiveClass $liveClass, User $user): ?string
    {
        return null;
    }

    public function issueAudioCallToken(string $roomName, User $user, bool $isRoomAdmin = false): ?string
    {
        return null;
    }
}