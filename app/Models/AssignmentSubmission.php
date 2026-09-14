<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id', 'student_id', 'attempt_number', 'status', 'submitted_at', 'is_late',
        'file_path', 'original_name', 'mime_type', 'file_size', 'score', 'feedback', 'graded_by', 'graded_at',
    ];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'graded_at' => 'datetime', 'is_late' => 'boolean', 'score' => 'decimal:2'];
    }

    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function grader(): BelongsTo { return $this->belongsTo(User::class, 'graded_by'); }
    public function rubricScores(): HasMany { return $this->hasMany(RubricScore::class, 'assignment_submission_id')->latest('graded_at'); }
}
