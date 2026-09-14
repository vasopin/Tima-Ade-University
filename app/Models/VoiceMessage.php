<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceMessage extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'file_path', 'duration_seconds', 'mime_type', 'file_size', 'played_at'];

    protected function casts(): array
    {
        return [
            'played_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getAudioUrlAttribute(): string
    {
        return route('voice-messages.file', ['voiceMessage' => $this->id]);
    }
}
