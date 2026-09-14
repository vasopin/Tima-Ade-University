<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RubricScore extends Model
{
    protected $fillable = ['assignment_submission_id', 'rubric_criterion_id', 'criterion_name', 'criterion_max_points', 'awarded_points', 'feedback', 'graded_by', 'graded_at'];
    protected function casts(): array { return ['criterion_max_points' => 'decimal:2', 'awarded_points' => 'decimal:2', 'graded_at' => 'datetime']; }
    public function submission(): BelongsTo { return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id'); }
    public function criterion(): BelongsTo { return $this->belongsTo(RubricCriterion::class, 'rubric_criterion_id'); }
    public function grader(): BelongsTo { return $this->belongsTo(User::class, 'graded_by'); }
}
