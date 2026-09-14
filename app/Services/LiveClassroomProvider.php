<?php

namespace App\Services;

use App\Models\LiveClass;
use App\Models\User;

interface LiveClassroomProvider
{
    public function name(): string;

    public function isConfigured(): bool;

    public function issueJoinToken(LiveClass $liveClass, User $user): ?string;

    public function issueAudioCallToken(string $roomName, User $user, bool $isRoomAdmin = false): ?string;
}