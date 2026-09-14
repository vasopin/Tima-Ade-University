<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveClassParticipant extends Model
{
    protected $table = 'live_class_participants';

    protected $fillable = [
        'live_class_id',
        'user_id',
        'role',
        'is_active',
        'is_muted',
        'camera_on',
        'screen_sharing',
        'permissions',
        'joined_at',
        'left_at',
        'removed_at',
        'removed_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_muted' => 'boolean',
            'camera_on' => 'boolean',
            'screen_sharing' => 'boolean',
            'permissions' => 'array',
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
