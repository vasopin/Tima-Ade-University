<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'school_class_id', 'section_id',
        'marked_by', 'attendance_date', 'status', 'remarks',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'present' => '<span class="badge bg-success">Present</span>',
            'absent'  => '<span class="badge bg-danger">Absent</span>',
            'late'    => '<span class="badge bg-warning text-dark">Late</span>',
            'excused' => '<span class="badge bg-info">Excused</span>',
            default   => '<span class="badge bg-secondary">N/A</span>',
        };
    }
}
