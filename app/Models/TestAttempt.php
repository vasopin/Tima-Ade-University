<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    protected $fillable = [
        'test_id', 'student_id', 'attempt_number',
        'started_at', 'submitted_at', 'time_spent_minutes',
        'status', 'score', 'grade', 'is_passed', 'graded_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'is_passed' => 'boolean',
        'score' => 'float',
        'time_spent_minutes' => 'integer',
        'attempt_number' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TestAttemptQuestion::class)->orderBy('order_index');
    }

    /**
     * Check if the attempt is within the time limit (if any)
     */
    public function isTimedOut(): bool
    {
        if (!$this->test->duration_minutes || !$this->started_at) {
            return false;
        }

        $elapsed = $this->started_at->diffInMinutes(now());
        return $elapsed >= $this->test->duration_minutes;
    }

    /**
     * Calculate remaining time in seconds
     */
    public function getRemainingSeconds(): int
    {
        if (!$this->test->duration_minutes || !$this->started_at) {
            return 0;
        }

        $elapsed = $this->started_at->diffInSeconds(now());
        $totalSeconds = $this->test->duration_minutes * 60;
        $remaining = max(0, $totalSeconds - $elapsed);

        return $remaining;
    }

    /**
     * Get percentage score
     */
    public function getPercentageAttribute(): ?float
    {
        if ($this->score === null || $this->test->total_marks === 0) {
            return null;
        }

        return round(($this->score / $this->test->total_marks) * 100, 2);
    }
}
