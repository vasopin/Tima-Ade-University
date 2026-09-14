<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    protected $fillable = [
        'name', 'exam_type', 'school_class_id', 'subject_id', 'course_section_id', 'academic_year_id', 'term_id',
        'exam_date', 'start_time', 'duration_minutes',
        'end_time', 'venue', 'total_marks', 'pass_marks', 'status', 'workflow_status', 'submitted_by', 'submitted_at', 'published_at', 'instructions',
    ];

    protected $casts = [
        'exam_date'  => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }

    public function resultsApproval(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ExamResultsApproval::class);
    }

    public function courseSection(): BelongsTo { return $this->belongsTo(CourseSection::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function submittedBy(): BelongsTo { return $this->belongsTo(User::class, 'submitted_by'); }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => '<span class="badge bg-info">Scheduled</span>',
            'ongoing'   => '<span class="badge bg-warning text-dark">Ongoing</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
