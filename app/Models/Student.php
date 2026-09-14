<?php

namespace App\Models;

use App\Services\StudentIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'roll_number', 'admission_number', 'school_class_id', 'section_id',
        'admission_date', 'gender', 'date_of_birth', 'address',
        'parent_name', 'parent_phone', 'parent_email', 'emergency_contact',
        'blood_group', 'religion', 'nationality', 'status', 'lifecycle_status', 'program_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Student $student): void {
            if (!$student->student_id) {
                $student->student_id = app(StudentIdGenerator::class)->generate($student->admission_date);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'admission_date' => 'date',
            'date_of_birth'  => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function program(): BelongsTo { return $this->belongsTo(Program::class); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function scholarshipAwards(): HasMany { return $this->hasMany(ScholarshipAward::class); }
    public function statusHistories(): HasMany { return $this->hasMany(StudentStatusHistory::class); }
    public function programHistories(): HasMany { return $this->hasMany(StudentProgramHistory::class); }
    public function transfers(): HasMany { return $this->hasMany(StudentTransfer::class); }
    public function holds(): HasMany { return $this->hasMany(StudentHold::class); }

    public function examMarks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }

    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship_type')
            ->withTimestamps();
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(Transcript::class);
    }

    public function academicStanding(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AcademicStanding::class);
    }

    public function graduationRecords(): HasMany
    {
        return $this->hasMany(GraduationRecord::class);
    }

    public function advisorAssignments(): HasMany
    {
        return $this->hasMany(AdvisorAssignment::class);
    }

    public function advisingAppointments(): HasMany
    {
        return $this->hasMany(AdvisingAppointment::class);
    }

    public function advisingNotes(): HasMany
    {
        return $this->hasMany(AdvisingNote::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => '<span class="badge bg-success">Active</span>',
            'inactive'  => '<span class="badge bg-secondary">Inactive</span>',
            'graduated' => '<span class="badge bg-info">Graduated</span>',
            'expelled'  => '<span class="badge bg-danger">Expelled</span>',
            default     => '<span class="badge bg-warning">Unknown</span>',
        };
    }
}
