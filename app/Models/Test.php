<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'teacher_id', 'school_class_id', 'subject_id',
        'title', 'instructions', 'total_marks', 'pass_marks',
        'duration_minutes', 'attempt_limit', 'status',
        'publish_date', 'close_date', 'results_release_date',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'close_date' => 'datetime',
        'results_release_date' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)->orderBy('order_index');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function selectedQuestions()
    {
        return $this->belongsToMany(TestQuestion::class, 'test_question_selections')
            ->withPivot(['order_index', 'marks'])
            ->select('test_questions.*')
            ->orderBy('test_question_selections.order_index');
    }

    public function questionPools(): HasMany
    {
        return $this->hasMany(TestQuestionPool::class);
    }

    /**
     * Check if the test is currently available for taking
     */
    public function isAvailable(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->publish_date && now() < $this->publish_date) {
            return false;
        }

        if ($this->close_date && now() > $this->close_date) {
            return false;
        }

        return true;
    }

    /**
     * Check if results can be viewed
     */
    public function canViewResults(): bool
    {
        if (!$this->results_release_date) {
            return false;
        }

        return now() >= $this->results_release_date;
    }

    /**
     * Get the badge for test status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'published' => '<span class="badge bg-success">Published</span>',
            'closed' => '<span class="badge bg-warning text-dark">Closed</span>',
            'archived' => '<span class="badge bg-info">Archived</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
