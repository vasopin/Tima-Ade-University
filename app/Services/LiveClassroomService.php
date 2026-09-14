<?php

namespace App\Services;

use App\Models\LiveClass;
use App\Models\LiveClassParticipant;
use App\Models\User;

class LiveClassroomService
{
    public function provider(): LiveClassroomProvider
    {
        return config('live-classroom.provider') === 'livekit'
            ? app(LiveKitClassroomProvider::class)
            : app(MockLiveClassroomProvider::class);
    }

    public function providerName(): string
    {
        return $this->provider()->name();
    }

    public function isProductionConfigured(): bool
    {
        return config('live-classroom.provider') === 'livekit' && $this->provider()->isConfigured();
    }

    public function isMockMode(): bool
    {
        return ! $this->isProductionConfigured();
    }

    public function classroomMetadata(LiveClass $liveClass, User $user): array
    {
        $canJoinProvider = ($user->isTeacher() && $liveClass->teacher_id === $user->id)
            || ($user->isStudent() && $liveClass->participants()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->whereNull('removed_at')
                ->exists());

        return [
            'provider' => $this->providerName(),
            'mode' => $this->isProductionConfigured() ? 'production' : 'development',
            'title' => $liveClass->title,
            'session_id' => 'session_' . $liveClass->id,
            'teacher_name' => optional($liveClass->teacher)->name,
            'participant_count' => $liveClass->participants()->count(),
            'is_demo' => $this->isMockMode(),
            'provider_url' => $this->isProductionConfigured() ? config('live-classroom.production.api_url') : null,
            'join_token' => $this->isProductionConfigured() && $canJoinProvider
                ? $this->provider()->issueJoinToken($liveClass, $user)
                : null,
        ];
    }

    public function canModerate(User $user, LiveClass $liveClass): bool
    {
        return $user->isTeacher() && $liveClass->teacher_id === $user->id;
    }

    public function canMuteParticipant(User $user, LiveClass $liveClass, LiveClassParticipant $participant): bool
    {
        return $this->canModerate($user, $liveClass) && $participant->live_class_id === $liveClass->id;
    }
}
