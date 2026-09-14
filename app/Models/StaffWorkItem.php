<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffWorkItem extends Model
{
    protected $fillable = ['user_id', 'type', 'subject', 'description', 'status', 'resolution_notes', 'resolved_at'];

    protected $casts = ['resolved_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}