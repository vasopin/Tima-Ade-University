<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'body', 'read_at', 'deleted_for_everyone_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'deleted_for_everyone_at' => 'datetime',
        ];
    }

    public function canDeleteForEveryone(): bool
    {
        return $this->sender_id === auth()->id() && 
               $this->created_at->addHours(24)->isFuture();
    }

    public function canDeleteForMe(): bool
    {
        return true; // Any participant can delete for themselves
    }

    public function isDeletedForUser(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        return $this->deletedForUsers()->where('user_id', $userId)->exists();
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function deletedForUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_deletions');
    }
}
