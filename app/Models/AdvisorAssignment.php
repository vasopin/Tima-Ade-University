<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisorAssignment extends Model
{
    protected $fillable = [
        'student_id', 'advisor_id', 'assigned_by', 'assigned_at', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
