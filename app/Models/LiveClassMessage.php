<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveClassMessage extends Model
{
    protected $table = 'live_class_messages';

    protected $fillable = [
        'live_class_id',
        'user_id',
        'user_name',
        'message',
    ];

    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
