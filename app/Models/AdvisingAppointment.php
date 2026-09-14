<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisingAppointment extends Model
{
    protected $fillable = [
        'student_id', 'advisor_id', 'requested_by', 'scheduled_at',
        'duration_minutes', 'mode', 'status', 'topic', 'notes', 'outcome',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
