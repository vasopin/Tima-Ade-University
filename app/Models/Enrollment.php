<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    public const ACTIVE_STATUSES = ['enrolled', 'completed'];
    public const STATUSES = ['enrolled', 'dropped', 'withdrawn', 'completed', 'cancelled'];

    protected $fillable = ['student_id', 'course_section_id', 'enrolled_at', 'status', 'final_grade', 'action_by', 'status_changed_at', 'action_reason'];
    protected function casts(): array { return ['enrolled_at' => 'datetime', 'status_changed_at' => 'datetime']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function courseSection(): BelongsTo { return $this->belongsTo(CourseSection::class); }
    public function actionBy(): BelongsTo { return $this->belongsTo(User::class, 'action_by'); }
}
