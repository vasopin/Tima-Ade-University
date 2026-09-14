<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Call extends Model
{
    protected $fillable = ['conversation_id', 'caller_id', 'recipient_id', 'status', 'started_at', 'ended_at', 'duration_seconds', 'call_sid'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(CallHistory::class);
    }

    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function isEnded(): bool
    {
        return in_array($this->status, ['ended', 'declined', 'missed']);
    }
}
