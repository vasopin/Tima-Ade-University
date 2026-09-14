<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendanceLog extends Model
{
    protected $fillable = ['user_id', 'attendance_date', 'checked_in_at', 'checked_out_at'];

    protected $casts = [
        'attendance_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}