<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallHistory extends Model
{
    protected $table = 'call_history';
    
    protected $fillable = ['call_id', 'user_id', 'action'];

    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
